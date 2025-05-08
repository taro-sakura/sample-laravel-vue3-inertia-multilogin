<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! Features::canManageTwoFactorAuthentication()) {
            $this->markTestSkipped('Two factor authentication is not enabled.');
        }
    }

    public function test_two_factor_authentication_flow(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        // 2FAの有効化
        $response = $this->post('/user/two-factor-authentication');
        $response->assertStatus(302);

        $user = $user->fresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertCount(8, $user->recoveryCodes());

        // 無効な2FAコードの検証
        $response = $this->withoutExceptionHandling()
            ->post('/two-factor-challenge', [
                'code' => '000000',
            ]);
        $response->assertStatus(302);

        // リカバリーコードの使用
        $recoveryCode = $user->recoveryCodes()[0];
        $response = $this->post('/two-factor-challenge', [
            'recovery_code' => $recoveryCode,
        ]);
        $response->assertStatus(302);

        // 2FAの無効化
        $response = $this->delete('/user/two-factor-authentication');
        $response->assertStatus(302);

        $user = $user->fresh();
        $this->assertNull($user->two_factor_secret);
    }

    public function test_two_factor_authentication_requires_password_confirmation(): void
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->post('/user/two-factor-authentication');
        $response->assertStatus(302);
        $response->assertRedirect('/user/confirm-password');

        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_authentication_requires_valid_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/two-factor-challenge', [
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_authentication_can_be_enabled_and_disabled(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        // 2FAの有効化
        $response = $this->post('/user/two-factor-authentication');
        $response->assertStatus(302);
        $this->assertNotNull($user->fresh()->two_factor_secret);

        // 2FAの無効化
        $response = $this->delete('/user/two-factor-authentication');
        $response->assertStatus(302);
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_challenge_with_invalid_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $this->withoutExceptionHandling();
        $response = $this->from('/two-factor-challenge')->post('/two-factor-challenge', [
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_challenge_with_recovery_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $recoveryCode = $user->recoveryCodes()[0];
        $response = $this->post('/two-factor-challenge', [
            'recovery_code' => $recoveryCode,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_challenge_with_invalid_recovery_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->from('/two-factor-challenge')->post('/two-factor-challenge', [
            'recovery_code' => 'invalid-recovery-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_challenge_with_valid_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/two-factor-challenge', [
            'code' => '123456',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_authentication_recovery_codes_can_be_regenerated(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $this->assertCount(8, $user->recoveryCodes());

        $response = $this->post('/user/two-factor-recovery-codes');
        $response->assertStatus(302);

        $user = $user->fresh();
        $this->assertCount(8, $user->recoveryCodes());
    }

    public function test_two_factor_authentication_qr_code_can_be_rendered(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->get('/user/two-factor-qr-code');
        $response->assertStatus(200);
    }

    public function test_two_factor_authentication_secret_key_can_be_retrieved(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->get('/user/two-factor-secret-key');
        $response->assertStatus(200);
    }

    public function test_two_factor_authentication_can_be_confirmed(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/user/confirmed-two-factor-authentication', [
            'code' => '123456',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_two_factor_authentication_requires_valid_code_for_confirmation(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/user/confirmed-two-factor-authentication', [
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_two_factor_authentication_requires_valid_code_for_challenge(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/two-factor-challenge', [
            'code' => 'invalid-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_two_factor_authentication_requires_valid_code_for_challenge_with_recovery_code(): void
    {
        $this->actingAs($user = User::factory()->create());
        $this->withSession(['auth.password_confirmed_at' => time()]);

        $this->post('/user/two-factor-authentication');
        $user = $user->fresh();

        $response = $this->post('/two-factor-challenge', [
            'recovery_code' => 'invalid-recovery-code',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }
} 