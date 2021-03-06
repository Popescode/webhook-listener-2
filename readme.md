# webhook-listener - solution #2

This is a simple PHP-based web service that listens for organization events to know when a repository has been created.  When the repository is created, it automatically protects the master branch, and notifies the creator with an @mention in an issue within the repository that outlines the protections that were added.

New repositories will automatically be initialized with a README as needed, if there is no master branch yet.

Webhooks are [validated for security](https://developer.github.com/webhooks/securing/).

# Requirements
The following prerequisites are required to run this service:
* PHP >= 7.2
* [Composer](https://getcomposer.org)
* An Organization in GitHub
* An [access token](https://help.github.com/en/github/authenticating-to-github/creating-a-personal-access-token-for-the-command-line) on your GitHub account
* A web server, e.g. Apache or Nginx, configured to pass requests to Lumen/Laravel.  For examples, see [Laravel Nginx configuration](https://laravel.com/docs/6.x/deployment#nginx)

# Installation
Follow these one-time steps to install this service before running it:
1. Clone this repo and change to the repo directory
1. Copy the `.env.example` file to `.env`
1. Give the `.env` file secure permissions (make it readable by the web server), e.g.:
    ```
    chgrp apache .env 
    chmod 640 .env
    ```
1. Populate `.env` with your real GitHub tokens:
    ```
    GITHUB_TOKEN="<your 40 char access token>"
    GITHUB_SECRET="<your webhook secret - a random string with high entropy>"
    ```
1. Install the PHP dependencies:
    ```
    composer install
    ```
1. In your GitHub Organization's Settings, create a [webhook](https://developer.github.com/webhooks/):
   1. The Payload URL should point to your internet-facing web service.
   1. Change the Content type to "application/json"
   1. In the Secret field, provide the random string you specified for the GITHUB_SECRET value in `.env`
   1. Select the "Let me select individual events" radio button
   1. Check the Repositories event

# Usage
As long as your web server is running and internet-accessible, no further steps are necessary. The service will now listen for the organization event webhooks.

# Resources
The following resources were used in the creation of this service:
* GitHub's [webhook tutorial](https://developer.github.com/webhooks/)
* GitHub's [REST API Documentation](https://developer.github.com/v3/)
* [Lumen](https://lumen.laravel.com), a PHP micro-framework based on [Laravel](https://laravel.com)
* [KnpLabs/php-github-api](https://github.com/KnpLabs/php-github-api/tree/master/doc), a PHP client for the GitHub API
