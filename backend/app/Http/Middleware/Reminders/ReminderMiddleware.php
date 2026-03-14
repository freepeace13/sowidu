<?php

namespace App\Http\Middleware\Reminders;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tightenco\Ziggy\BladeRouteGenerator;

abstract class ReminderMiddleware
{
    protected $name;

    protected $session;

    /** Seconds */
    protected $remindEvery = 86400; // 1 day

    abstract public function visible($request): bool;

    public function handle(Request $request, Closure $next)
    {
        $this->session = $request->session();

        $response = $next($request);

        if (in_array(get_class($response), [
            BinaryFileResponse::class,
        ])) {
            return $response;
        }

        if (
            config('reminder.enabled') &&
            $this->isInertiaRequest($request) &&
            !$this->isInternalJsonRequest($request)
        ) {
            $remindedLast = $this->getRemindedLast();

            if (!$remindedLast && $this->visible($request)) {
                BladeRouteGenerator::$generated = false;

                $page = $this->render($request);
                $content = $response->getOriginalContent();

                if ($content instanceof View) {
                    $content = $this->onViewContent($content, $page);
                } elseif (is_array($content)) {
                    $content = $this->onArrayContent($content, $page);
                }

                $response->setContent($content);
            }
        }

        return $response;
    }

    protected function onViewContent(View $content, array $page)
    {
        Arr::set($content->page, 'component', $page['component']);
        Arr::set($content->page, 'props', $content->page['props'] + $page['props']);

        return $content;
    }

    protected function onArrayContent(array $content, array $page)
    {
        Arr::set($content, 'component', $page['component']);
        Arr::set($content, 'props', $content['props'] + $page['props']);

        return json_encode($content);
    }

    protected function setRemindedLast($datetime = null)
    {
        if (Carbon::parse($datetime)->isValid()) {
            $this->session->put($this->getId(), $datetime);
        }
    }

    protected function getRemindedLast()
    {
        $remindedLast = $this->session->get($this->getId());

        if ($remindedLast && $this->reminderExpired($remindedLast)) {
            $this->session->forget($this->getId());

            return null;
        }

        return $remindedLast;
    }

    public function reminderExpired($remindedLast)
    {
        $remindedLast = Carbon::parse($remindedLast);

        return $remindedLast->isValid() && Carbon::now()
            ->subSeconds($this->remindEvery)
            ->gt($remindedLast);
    }

    public function getDuration()
    {
        return $this->remindEvery;
    }

    abstract public function render($request): array;

    protected function inertia($component, array $data = [])
    {
        return [
            'component' => $component,
            'props' => $data + [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'duration' => $this->remindEvery,
            ],
        ];
    }

    protected function isInertiaRequest($request)
    {
        if ($request->header('X-Inertia')) {
            return $request->method() === 'GET';
        }

        return true;
    }

    protected function isInternalJsonRequest($request)
    {
        return $request->header('X-Internal-Json-Request');
    }

    public function getId()
    {
        return Str::start(trim(str_replace(' ', '', Str::slug($this->getName()))), 'reminder:');
    }

    public function getName()
    {
        return $this->name ?? Str::title(
            str_replace('_', ' ', Str::snake(class_basename(static::class))),
        );
    }
}
