<?php

return [

    'verify_email' => true,

    'track_login_attempts' => true,

    'login_attempts_model' => null,

    'verify_login_attempt_on_suspicious_login' => true,

    'notifications' => [
        'verify_email' => dees040\AuthExtra\Notifications\ActivateYourAccount::class,
        'verify_login' => dees040\AuthExtra\Notifications\VerifySuspiciousLogin::class,
    ],

    'routes' => [
        'verify_email' => '/activation/email',
        'verify_user' => '/verify/user',
    ],

];
