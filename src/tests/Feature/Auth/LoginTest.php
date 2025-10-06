<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class LoginTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    /** @test ID2-1: メール未入力 */
    public function email_required_for_login()
    {
        $res = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $res->assertSessionHasErrors(['email']);
    }

    /** @test ID2-2: パスワード未入力 */
    public function password_required_for_login()
    {
        $res = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /** @test ID2-3: 不一致（存在しない or パスワード違い） */
    public function invalid_credentials_show_error()
    {
        $this->makeUser(['email' => 'taro@example.com']);

        $res = $this->from('/login')->post('/login', [
            'email' => 'taro@example.com',
            'password' => 'wrongpass',
        ]);

        $res->assertRedirect('/login');
        $res->assertSessionHasErrors(); // credentials系
    }
}
