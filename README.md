# AuthExtra

This plugin gives you extra options for Authentication, such as:

- [x] Track login attempts
- [x] Verify email address
- [ ] Block suspicious login attempts
- [ ] Two factor authentication

## Installation

Install the latest version with composer.

```
composer require dees040/laravel-auth-extra
```

After installing the packages and the service provider to the `providers` array in `app/config.php`.

```php
dees040\AuthExtra\ServiceProvider::class,
```

Also add the following class to the `aliases` array. This gives you the ability to use the Facade.

```php
'AuthExtra' => dees040\AuthExtra\Facade\AuthManager::class,
```

## Publish configuration

First run the `vendor:publish` command so the package generate it's config file.

```php
php artisan vendor:publish --provider="dees040\AuthExtra\ServiceProvider"
```

This will generate the `config/auth_extra.php` file, this file holds all the configuration for the package. More about the options in the configuration file can be found [here](https://github.com/dees040/laravel-auth-extra#configurations).

## Usage

Your `User` model should implement the `AuthenticatableContract`. This is done automatically if the model extends the `Illuminate\Foundation\Auth\User`. Which is the [default on a fresh Laravel installation](https://github.com/laravel/laravel/blob/master/app/User.php#L8).

### Email verification

To use email verification, set the `verify_email` config value to `true`.

When an user registers a new account we will automatically send an email to the user to verify if the signed up with a valid email address.

**Check verification**

You can add the AuthExtra trait to your `User` model. This will give you the `verifiedEmail` method. The method will return a boolean depending on the verification of the user.

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use dees040\AuthExtra\ExtraAuthenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, ExtraAuthenticatable;
}
```

**Middleware**

In some scenarios a visitor/user of your website may only visit a page/route when his or her account has a verified email address. For example: to avoid spam, an user maybe only create blog posts when he/she has a valid email address.

To make use of the email verification middleware you need to save the middleware to the `$routeMiddleware` variable in `app/Http/Kernel.php`.

```php
'verified' => \dees040\AuthExtra\Middleware\VerifiedEmail::class,
```

You can now use this middleware on your routes like this:

```php
Route::group(['middleware' => ['verified']], function() {
    Route::post('/posts', 'PostController@store');
});
```

For more information about middleware you can read the [Laravel docs](https://laravel.com/docs/5.4/middleware).

### Login attempts

To track login attempts, set the `track_login_attempts` config value to `true`.

When a visitor of your application tries to login we will create a record into the database with some basic information. Scroll down to the Configuration options to see more info about these recods.

### Suspicious login attempts

**Currently in Development**

To block suspicious login attempts, set the `verify_login_attempt_on_suspicious_login` config value to `true`.

When a visitor tries to login the package will check for any suspicious things. If the login attempt seem suspicious we will send the user a notification about the login attempt. He or she than can take action to secure their account.

### Two Factor Authentication

This is not available yet.

## Configurations

**`verify_email`** (`bool`)

If set to `true` the package will automatically verify the user it's email address on register. It will send an verification email. This email can be customized. See the `notifications` config option.

**`track_login_attempts`** (`bool`)

If set to `true` the package will automatically track any login attempt. It will save each attempt to the database. With the following data:
- `user_id` (`integer`) - Only if user is known
- `ip` (`string`)
- `country` (`string`)
- `city` (`string`)
- `success` (`boolean`) - `true` if the login attempt was successful, `false` if not.
- `type` (`integer`) - The type of login. 0 = successful, 1 = failed, 9 = blocked.
- `suspicious` (`integer`) - Keep track of how suspicious the login attempts are.
- `created_at` (`timestamp`)
- `updated_at` (`timestamp`)

**Note:** if `verify_login_attempt_on_suspicious_login` config is set to true this options will automatically be set to true.

**`login_attempts_model`** (`null` or `string`)

If you wish to create a model for the login attempts, you can store the model here. This options is used to load the login attempts for an user. If this is set to `null` and one is calling `$user->loginAttempts()`, the package will return a Collection from the [Query Builder](https://laravel.com/docs/5.4/queries). If for example `\App\LoginAttempts::class` is set, the package will return a Collection of [models](https://laravel.com/docs/5.4/eloquent-collections).

**`verify_login_attempt_on_suspicious_login`** (`bool`)

If this options is set to true, the package will give the user a notification when the is a suspicious login attempt. The user can use this email to change his or her password.

**`notifications`** (`array`)

This option store all the notification. If you wish to use your own notification you can create a notification and then change it here.

**`routes`** (`array`)

This array stores the routes. These routes are used for email verification and suspicious login verification. If you wish you can change these routes here.
