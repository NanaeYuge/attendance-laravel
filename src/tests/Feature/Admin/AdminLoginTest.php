<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    /** @test ID3-1: メール未入力 */
    public function admin_email_required()
    {
        $res = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $res->assertSessionHasErrors(['email']);
    }

    /** @test ID3-2: パスワード未入力 */
    public function admin_password_required()
    {
        $res = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /** @test ID3-3: 不一致 */
    public function admin_invalid_credentials()
    {
        $this->makeAdmin(['email' => 'admin@example.com']);

        $res = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ]);

        $res->assertRedirect('/admin/login');
        $res->assertSessionHasErrors();
    }
}
