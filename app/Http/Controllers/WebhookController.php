<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handles 'repository' webhook events. On new repository creation,
     * adds protections to master branch and creates an issue in the
     * repo to notify sender, via GitHub API.  Creates a master branch
     * if not present by pushing a Readme file.
     *
     * @param  Request  $request
     */
    public function repositories(Request $request) {
        $data = $request->json()->all();

        $action = $data['action'];
        $repo = $data['repository']['name'];
        $sender = $data['sender']['login'];

        Log::info("Received webhook: $repo repo was $action by $sender");

        $client = github_init_client();

        if($data['action'] == 'created') {
            $owner = $data['repository']['owner']['login'];

            sleep(2); // Because sometimes the master branch doesn't exist yet (race condition)

            if(!has_master_branch($client, $owner, $repo)) {
                create_readme($client, $owner, $repo);
            }

            protect_branch($client, $owner, $repo, 'master');
            notify_sender($client, $owner, $repo, $sender);
        }
    }
}
