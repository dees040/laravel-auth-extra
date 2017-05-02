<?php

use Carbon\Carbon;
use Tests\TestCase;
use MailThief\Testing\InteractsWithMail;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SuspiciousLoginAttemptsTest extends TestCase
{
    use DatabaseMigrations, InteractsWithMail;

    /** @test */
    public function a_user_is_being_blocked_when_in_other_country()
    {
        config(['verify_login_attempt_on_suspicious_login' => true]);

        $user = factory(\App\User::class)->create(['password' => 'johndoe']);

        \DB::table('login_attempts')->insert([
            'user_id' => $user->id,
            'ip' => '8.8.8.8',
            'country' => 'United States',
            'city' => 'Mountain View',
            'success' => 1,
            'type' => 0,
            'suspicious' => 0,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'johndoe',
        ]);

        $attempt = \DB::table('login_attempts')->latest()->first();

        $this->assertEquals($attempt->type, 9);
    }

    /** @test */
    public function a_user_is_blocked_when_attempts_are_to_fast()
    {
        config(['verify_login_attempt_on_suspicious_login' => true]);

        $user = factory(\App\User::class)->create(['password' => 'johndoe']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'abc',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'johndoe',
        ]);

        $attempt = \DB::table('login_attempts')->orderByDesc('id')->first();

        $this->assertEquals($attempt->type, 9);
    }

    /** @test */
    public function a_mail_is_send_on_suspicious_login()
    {
        config(['verify_login_attempt_on_suspicious_login' => true]);

        $user = factory(\App\User::class)->create(['password' => 'johndoe']);

        for ($i = 0; $i < 2; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'abc',
            ]);
        }

        $this->seeMessageFor($user->email);
    }

    /** @test */
    public function a_user_needs_to_validate_account_on_suspicious_login()
    {
        config(['verify_login_attempt_on_suspicious_login' => true]);

        $user = factory(\App\User::class)->create(['password' => 'johndoe']);

        for ($i = 0; $i < 2; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'abc',
            ]);
        }
    }
}
