<?php

return [

    'login_identifier_field' => [
        'email',
        'username',
    ],

    'verify_email' => true,

    'track_login_attempts' => true,

    'login_attempts_model' => null,

    'verify_login_attempt_on_suspicious_login' => true,

    'notifications' => [
        'verify_email' => dees040\AuthExtra\Notifications\ActivateYourAccount::class,
    ],

];