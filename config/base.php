<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Incoming Slack Webhooks 
    |--------------------------------------------------------------------------
    |
    | Incoming webhooks are a simple way to post messages from external sources into Slack.
    | They make use of normal HTTP requests with a JSON payload, which includes the message
    | and a few other optional details. You can include message attachments to display
    | richly-formatted messages.
    |
    */

    'slack_webhook' => env('BASE_SLACK_WEBHOOK')

];
