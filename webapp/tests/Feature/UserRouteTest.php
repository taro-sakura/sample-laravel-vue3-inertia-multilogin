<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザーがログインページにアクセスできる()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_ユーザーがダッシュボードにアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_未認証ユーザーはダッシュボードにアクセスできない()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_ユーザーがプロフィールページにアクセスできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('profile.show'));

        $response->assertStatus(200);
    }

    public function test_ユーザーがログアウトできる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
} 