<?php

$config = AuthExtra::getConfig();

Route::get($config->getRoute('verify_email'), 'dees040\AuthExtra\AuthManager@verifyActivationToken')->name('activation.email');
Route::get($config->getRoute('verify_user'), 'dees040\AuthExtra\AuthManager@verifyLogin')->name('verify.user');