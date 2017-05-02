<?php

use Tests\TestCase;
use MailThief\Testing\InteractsWithMail;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VerifyEmailTest extends TestCase
{
    use DatabaseMigrations, InteractsWithMail;

    /** @test */
    public function a_email_is_sent_on_registration()
    {
        config(['auth_extra.verify_email' => 'true']);

        $this->post('/register', [
            'email' => 'john@doe.com',
            'name' => 'John Doe',
            'password' => 'johndoe',
            'password_confirmation' => 'johndoe',
        ]);

        $user = \App\User::first();

        $this->assertEquals('john@doe.com', $user->email);

        $this->seeMessageFor('john@doe.com');
        $this->seeMessageWithSubject('Activate your account');
    }

    /** @test */
    public function a_user_can_validate_its_email_address()
    {
        config(['auth_extra.verify_email' => 'true']);

        $this->post('/register', [
            'email' => 'john@doe.com',
            'name' => 'John Doe',
            'password' => 'johndoe',
            'password_confirmation' => 'johndoe',
        ]);

        $user = \App\User::first();

        $this->assertFalse($user->verifiedEmail());

        $activation = \DB::table('activations')->first();

        $this->get(route('activation.email', $activation->token));

        $this->assertTrue($user->verifiedEmail());
    }

    /** @test */
    public function a_user_cant_visit_routes_which_needs_verification()
    {
        config(['auth_extra.verify_email' => 'true']);

        $this->post('/register', [
            'email' => 'john@doe.com',
            'name' => 'John Doe',
            'password' => 'johndoe',
            'password_confirmation' => 'johndoe',
        ]);

        $user = \App\User::first();

        $response = $this->actingAs($user)->get('/needs-activation');

        $response->assertStatus(403);
        $response->assertSee('This action is unauthorized');
    }
}
