<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\Feature\Support\CreatesTestData;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function name_is_required()
    {
        $res = $this->post('/register', [
            'name' => '',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $res->assertSessionHasErrors(['name']);
    }

    public function email_is_required()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $res->assertSessionHasErrors(['email']);
    }

    public function password_min_is_8()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    public function password_confirmation_must_match()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    public function password_is_required()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    public function can_register_and_persist()
    {
        Notification::fake();

        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'taro@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);

        $res->assertRedirect();
    }

    public function verification_mail_sent_and_verification_flow()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => '次郎',
            'email' => 'jiro@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect();

        $user = \App\Models\User::where('email', 'jiro@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);

        $verificationUrl = URL()->temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)->get($verificationUrl)->assertRedirect('/attendance');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
