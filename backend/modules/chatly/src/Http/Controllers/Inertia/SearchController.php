<?php

namespace Modules\Chatly\Http\Controllers\Inertia;

use Illuminate\Http\Request;
use Modules\Chatly\Contracts\External\UserSearchContract;
use Modules\Chatly\Http\Controllers\BaseController;

class SearchController extends BaseController
{
    public function __construct(
        protected UserSearchContract $userSearch,
    ) {}

    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $filters = [];

        // Add exception filters if provided
        if ($request->filled('except')) {
            $filters['except'] = $request->except;
        }

        // Perform search using the contract
        $result = $this->userSearch->search($keyword, $filters, 8);

        return response()->json($result);
    }
}
