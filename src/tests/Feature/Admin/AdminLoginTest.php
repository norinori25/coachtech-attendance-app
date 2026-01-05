<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレス未入力ならバリデーションエラー()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /** @test */
    public function パスワード未入力ならバリデーションエラー()
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /** @test */
    public function 登録内容と一致しない場合はエラーメッセージが表示される()
    {
        User::factory()->create([
            'email' => 'realadmin@example.com',
            'password' => bcrypt('correctpass'),
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }
}