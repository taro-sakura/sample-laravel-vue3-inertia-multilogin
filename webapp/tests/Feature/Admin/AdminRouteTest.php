<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_管理者がログインページにアクセスできる()
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
    }

    public function test_管理者がダッシュボードにアクセスできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_未認証管理者はダッシュボードにアクセスできない()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect());
    }

    public function test_一般ユーザーは管理者ダッシュボードにアクセスできない()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect());
    }

    public function test_管理者がプロフィールページにアクセスできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.profile.show'));

        $response->assertStatus(200);
    }

    public function test_管理者がユーザー一覧ページにアクセスできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_管理者がログ一覧ページにアクセスできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.logs.index'));

        $response->assertStatus(200);
    }

    public function test_管理者が設定ページにアクセスできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.index'));

        $response->assertStatus(200);
    }

    public function test_管理者がログアウトできる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_管理者がパスワードを更新できる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(route('admin.profile.show'));
    }

    public function test_管理者がプロフィール情報を更新できる()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $response->assertRedirect(route('admin.profile.show'));
        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }
} 