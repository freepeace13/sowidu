<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redirect;

trait WithSnackbar
{
    public function redirectBackSuccess($message)
    {
        return Redirect::back()->with('flash', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function redirectBackError($message)
    {
        return Redirect::back()->with('flash', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function redirectSuccess($route, $message)
    {
        return Redirect::route($route)->with('flash', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function redirectError($route, $message)
    {
        return Redirect::route($route)->with('flash', [
            'type' => 'error',
            'message' => $message,
        ]);
    }
}
