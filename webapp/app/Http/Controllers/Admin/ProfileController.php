<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * プロフィール情報を表示
     */
    public function show(): \Inertia\Response
    {
        return Inertia::render('Admin/Profile/Show', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    /**
     * プロフィール情報を更新
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user('admin')->fill($request->validated());

        if ($request->user('admin')->isDirty('email')) {
            $request->user('admin')->email_verified_at = null;
        }

        $request->user('admin')->save();

        return Redirect::route('admin.profile.show');
    }

    /**
     * パスワードを更新
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user('admin')->update([
            'password' => bcrypt($validated['password']),
        ]);

        return Redirect::route('admin.profile.show');
    }

    /**
     * アカウントを削除
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password:admin'],
        ]);

        $user = $request->user('admin');

        Auth::guard('admin')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('admin.login');
    }

    /**
     * 二要素認証を有効化
     */
    public function enableTwoFactorAuthentication(Request $request): RedirectResponse
    {
        $user = $request->user('admin');

        if (!$user->two_factor_secret) {
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            $secret = $google2fa->generateSecretKey();
            $recoveryCodes = $user->generateNewRecoveryCodes();

            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            ])->save();
        }

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * 二要素認証を無効化
     */
    public function disableTwoFactorAuthentication(Request $request): RedirectResponse
    {
        $user = $request->user('admin');

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * リカバリーコードを表示
     */
    public function showRecoveryCodes(Request $request): Response
    {
        $user = $request->user('admin');

        if (!$user->two_factor_secret) {
            return back();
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $qrCode = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            decrypt($user->two_factor_secret)
        );

        return Inertia::render('Admin/Profile/TwoFactorAuthentication', [
            'qrCode' => $qrCode,
            'recoveryCodes' => json_decode(decrypt($user->two_factor_recovery_codes), true),
        ]);
    }

    /**
     * 他のブラウザセッションからログアウト
     */
    public function logoutOtherBrowserSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password:admin'],
        ]);

        Auth::guard('admin')->logoutOtherDevices($request->password);

        return back()->with('status', 'other-browser-sessions-logged-out');
    }
} 