<?php

use Illuminate\Support\Facades\Log;

/**
 * Creates and returns a GitHub API client instance.
 *
 * @return \Github\Client
 */
function github_init_client() {
    $client = new \Github\Client();
    $client->authenticate(env('GITHUB_TOKEN'), null, Github\Client::AUTH_HTTP_TOKEN);

    return $client;
}

/**
 * Adds protections to a specified branch.
 *
 * @param $client
 * @param $owner
 * @param $repo
 * @param $branch
 */
function protect_branch($client, $owner, $repo, $branch) {
    Log::info("Adding protections to $branch branch of $owner/$repo");

    $emptyObject = new \stdClass();

    $params = [
        'required_status_checks' => null,
        'required_pull_request_reviews' => [
            'dismissal_restrictions' => $emptyObject,
            'dismiss_stale_reviews' => true,
            'require_code_owner_reviews' => true,
        ],
        'enforce_admins' => true,
        'restrictions' => null,
    ];

    $protection = $client->api('repo')->protection()->update($owner, $repo, $branch, $params);
}

/**
 * Notifies the sender/creator of added protections by
 * creating an issue in the repo with an @mention.
 *
 * @param $client
 * @param $owner
 * @param $repo
 * @param $sender
 */
function notify_sender($client, $owner, $repo, $sender) {
    Log::info("Notifying sender $sender of protections by opening an issue in $owner/$repo");

    $title = "Master branch protected";

    $body = "@$sender This repo's master branch has been protected with the following settings:\n" .
        "* Require pull request reviews before merging\n" .
        "  * Dismiss stale pull request approvals when new commits are pushed\n" .
        "  * Require review from Code Owners\n" .
        "* Enforce above restrictions for administrators";

    $client->api('issue')->create($owner, $repo, ['title' => $title, 'body' => $body]);
}
