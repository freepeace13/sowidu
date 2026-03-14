<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\ShareOrderWorkLogToClient;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Modules\WorkLogs\Models\WorkLog;

class ShareWorkLogToClientController extends Controller
{
    public function __invoke(Request $request, Order $order, WorkLog $workLog)
    {
        $workLog = ShareOrderWorkLogToClient::run($request->user(), $order, $workLog, $request->all());

        if ($workLog->is_shared) {
            flash_success('Work log has been shared to the client.');
        }

        return back();
    }
}
