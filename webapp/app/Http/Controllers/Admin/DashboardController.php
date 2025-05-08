<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * 管理者ダッシュボードを表示
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => \App\Models\User::count(),
                'total_admins' => \App\Models\Admin::count(),
                'total_logs' => 0, // 一時的に0を返す
            ],
        ]);
    }
} 