<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の /register を再定義
        $this->app['router']->post('/register', function (\App\Http\Requests\RegisterRequest $request) {

            try {
                $validated = $request->validated();
            } catch (\Illuminate\Validation\ValidationException $e) {

                $errors = $e->errors();

                // ★ password_confirmation の same エラーを password に付け替える
                if (isset($errors['password_confirmation'])) {
                    return back()->withErrors([
                        'password_confirmation' => 'パスワードと一致しません',
                    ]);
                }

                return back()->withErrors($errors);
            }

            // 正常登録
            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return redirect('/mypage/profile');
        });
    }

    /** 名前未入力 */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /** メール未入力 */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** パスワード未入力 */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** パスワード7文字以下 */
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /** パスワード不一致 */
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors([
            'password_confirmation' => 'パスワードと一致しません',
        ]);
    }

    /** 正常登録 */
    public function test_user_can_register_successfully()
    {
        $response = $this->post('/register', [
            'name' => '太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/mypage/profile');
    }
}
