<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                'site_name' => config('app.name'),
                'site_description' => config('app.description', ''),
                'maintenance_mode' => app()->isDownForMaintenance(),
                'registration_enabled' => config('auth.registration_enabled', true),
                'default_role' => config('auth.default_role', 'user'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'default_role' => 'required|string|in:user,admin',
        ]);

        // 設定を更新
        config(['app.name' => $validated['site_name']]);
        config(['app.description' => $validated['site_description']]);
        config(['auth.registration_enabled' => $validated['registration_enabled']]);
        config(['auth.default_role' => $validated['default_role']]);

        // メンテナンスモードの切り替え
        if ($validated['maintenance_mode']) {
            if (!app()->isDownForMaintenance()) {
                $this->artisan('down');
            }
        } else {
            if (app()->isDownForMaintenance()) {
                $this->artisan('up');
            }
        }

        // キャッシュをクリア
        Cache::forget('app_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', '設定を更新しました。');
    }

    private function artisan($command)
    {
        $artisan = base_path('artisan');
        exec("php {$artisan} {$command}");
    }
} 