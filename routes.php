<?php

Route::get('/activate/{token}', 'dees040\AuthExtra\AuthManager@verifyActivationToken')->name('activate.email');