<?php

namespace App\Http\Controllers\Json\Autocomplete;

use App\Http\Controllers\Json\BaseController;
use App\Models\Employee;
use Illuminate\Http\Request;

class JobtitleController extends BaseController
{
    public function __invoke(Request $request)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $result = Employee::select('position')
            ->distinct()
            ->when(filled($text), function ($query) use ($text) {
                $query->where('position', 'LIKE', "%{$text}%");
            })
            ->orderBy('position')
            ->limit($size)
            ->get();

        return $this->json($result->pluck('position'));
    }
}
