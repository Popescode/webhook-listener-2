<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function repositories(Request $request) {
        $data = $request->json()->all();

        Log::info("The " . $data['repository']['full_name'] . " repo was " . $data['action']);

        if($data['action'] == 'created') {
            protect_master_branch($data);
            notify_sender($data);
        }

    }

    protected function protect_master_branch($data) {

    }

    protected function notify_sender($data) {

    }
}
