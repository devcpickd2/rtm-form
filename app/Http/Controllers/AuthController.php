<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // 1. Kalau sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // 2. Jika berasal dari logout lokal,
        // langsung tampilkan halaman login lokal
        if ($request->session()->pull('force_local_login')) {
            return view('auth.login');
        }

        // 3. Cek bypass key
        $bypassKey = $request->query('access_key');
        $expectedKey = config('services.login_bypass.key');

        if (
            $bypassKey &&
            $expectedKey &&
            hash_equals($expectedKey, $bypassKey)
        ) {
            return view('auth.login');
        }

        // 4. Cek apakah Portal PDQC bisa diakses
        try {
            $portalUrl = rtrim(
                config('services.employee_api.portal_url'),
                '/'
            );

            $response = Http::timeout(3)
                ->get($portalUrl . '/login');

            // 5. Kalau PDQC aktif, redirect ke PDQC
            if ($response->successful()) {
                return redirect($portalUrl . '/login');
            }

        } catch (\Throwable $e) {
            // PDQC tidak tersedia
        }

        // 6. Fallback login lokal
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password salah'
            ]);
        }

        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        // Tandai login biasa
        $request->session()->put('login_method', 'normal');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Ambil metode login sebelum session dihapus
        $loginMethod = $request->session()->get('login_method', 'normal');

        // Logout dari RTM-Form
        Auth::logout();

        // Hanya report logout jika login melalui SSO
        if ($user && $loginMethod === 'sso') {
            try {
                Http::withToken(config('services.employee_api.sso_secret'))
                    ->timeout(5)
                    ->post(
                        config('services.employee_api.url') . '/sso/report-logout',
                        [
                            'user_uuid' => $user->uuid,
                            'project_uuid' => config('services.employee_api.this_project_uuid'),
                        ]
                    );

            } catch (\Throwable $e) {
                // Jangan menggagalkan logout RTM-Form
            }
        }

        // Hapus session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Login SSO kembali ke portal PDQC
        if ($loginMethod === 'sso') {
            return redirect(config('services.employee_api.portal_url'));
        }

        // Login lokal: paksa tampilkan halaman login lokal
        $request->session()->flash('force_local_login', true);

        return redirect()->route('login');
    }
}
