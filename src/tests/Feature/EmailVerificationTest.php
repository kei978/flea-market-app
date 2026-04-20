<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** 会員登録後、認証メールが送信される */
    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** 認証誘導画面でボタンを押すとメール認証サイトに遷移する */
    public function test_verify_notice_page_has_link_to_verification_site()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));

        $response->assertSee('認証はこちらから');
    }

    /** メール認証を完了するとプロフィール設定画面に遷移する */
    public function test_email_verification_redirects_to_profile_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        // 署名付きURLを生成
        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // 認証URLにアクセス
        $response = $this->get($verificationUrl);

        // 認証後の遷移先
        $response->assertRedirect('/mypage/profile');

        // email_verified_at が更新されていること
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}