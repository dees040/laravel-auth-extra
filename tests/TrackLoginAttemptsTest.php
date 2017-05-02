<?php

use Tests\TestCase;
use MailThief\Testing\InteractsWithMail;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackLoginAttemptsTest extends TestCase
{
    use DatabaseMigrations, InteractsWithMail;

    /** @test */
    public function a_user_login_is_being_tracked()
    {
        config(['track_login_attempts' => true]);

        $this->post('/register', [
            'email' => 'john@doe.com',
            'name' => 'John Doe',
            'password' => 'johndoe',
            'password_confirmation' => 'johndoe',
        ]);

        $user = \App\User::first();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'johndoe',
        ]);

        $attempt = \DB::table('login_attempts')->latest()->first();

        $this->assertEquals($user->id, $attempt->user_id);
    }

    public function a_user_is_being_blocked_when_in_other_country()
    {
        
    }
}
