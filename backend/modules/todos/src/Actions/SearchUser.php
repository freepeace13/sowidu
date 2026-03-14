<?php

namespace Modules\Todos\Actions;

use App\Models\User;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;

class SearchUser
{
    use InteractsWithImpersonator;

    public function get(Request $request)
    {
        $keyword = $request->query('keyword');

        return User::search($keyword)
            ->unless($this->isImpersonating(), function ($query) {
                $query->where('id', '!=', $this->user()->getKey());
            })
            ->when($this->isImpersonating(), function ($query) {
                $query->where('id', '!=', $this->user()->user_id);
            })
            ->when($request->filled('board'), function ($query) use ($request) {
                $query->whereHas('subscribingToBoards', fn ($query) => $query->where('board_id', $request->board));
            })
            ->get();
    }
}
