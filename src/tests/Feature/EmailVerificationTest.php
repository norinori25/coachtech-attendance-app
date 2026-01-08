<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後に認証メールが送信される()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Notification::assertSentTo(
            User::first(),
            VerifyEmail::class
        );
    }

    /** @test */
    public function 認証はこちらからボタンで認証サイトへ遷移する()
    {
        $user = User::factory()->unverified()->create();
        
        // セッションに未認証ユーザーを保存（実際のフロー通り）
        session()->put('unauthenticated_user', $user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    /** @test */
    public function メール認証完了後に勤怠登録画面へ遷移する()
    {
        $user = User::factory()->unverified()->create();

        // セッションに未認証ユーザーを保存（実際のフロー通り）
        session()->put('unauthenticated_user', $user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect('/attendance');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}