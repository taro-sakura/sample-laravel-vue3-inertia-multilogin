<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRouteTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_未認証ユーザーはプロフィールページにアクセスできない()
    {
        $response = $this->get(route('profile.show'));

        $response->assertRedirect(route('login'));
    }
} 