<?php

namespace App\Http\Controllers\AppSettings;

use App\Actions\AppSettings\Translation\UpdateTranslation;
use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use App\Transformers\PaginatorTransformer;
use App\Transformers\TranslationTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TranslationManagerController extends Controller
{
    public function index(Request $request)
    {
        $service = TranslationService::make();
        $translations = $service->paginate($request->only(['q']));

        return Inertia::render('AppSettings/Translation/Manager', [
            'translationList' => collect($translations->items())->values()
                ->map(
                    fn ($lang) => TranslationTransformer::make($lang)->withOtherTranslations($service->getTranslations($lang['key']))
                        ->resolve(),
                )
                ->toArray(),
            'pagination' => PaginatorTransformer::make($translations),
            'trans' => Inertia::lazy(fn () => $service->find($request->get('trans'))),
            'filters' => array_merge(['q' => ''], $request->only(['q'])),
        ]);
    }

    public function store(Request $request)
    {
        UpdateTranslation::run($request->user(), $request->all());

        flash_success(__('app_settings.translation-manager.messages.updated'));

        return back();
    }
}
