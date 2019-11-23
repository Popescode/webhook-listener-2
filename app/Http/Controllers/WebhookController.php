<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function repositories(Request $request) {
        $data = $request->json()->all();

        $action = $data['action'];
        $repo = $data['repository']['name'];
        $sender = $data['sender']['login'];

        Log::info("Received webhook: $repo repo was $action by $sender");

        $client = github_init_client();

        if($data['action'] == 'created') {
            $owner = $data['repository']['owner']['login'];

            sleep(2);
            protect_branch($client, $owner, $repo, 'master');
            notify_sender($client, $owner, $repo, $sender);
        }
    }
}
