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
        // 1. Kalau sudah login lokal
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // 2. Cek bypass key
        $bypassKey = $request->query('access_key');
        $expectedKey = config('services.login_bypass.key');

        if (
            $bypassKey &&
            $expectedKey &&
            hash_equals($expectedKey, $bypassKey)
        ) {
            return view('auth.login');
        }

        // 3. Cek apakah Portal PDQC bisa diakses
        try {
            $portalUrl = rtrim(
                config('services.employee_api.portal_url'),
                '/'
            );

            $response = Http::timeout(3)
                ->get($portalUrl . '/login');

            // 4. Kalau PDQC aktif → redirect ke PDQC
            if ($response->successful()) {
                return redirect($portalUrl . '/login');
            }

        } catch (\Throwable $e) {
            // PDQC tidak bisa diakses
            // Lanjut ke login lokal sebagai fallback
        }

        // 5. Kalau PDQC tidak tersedia → tampilkan login lokal
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
            return back()->withErrors(['username' => 'Username atau password salah']);
        }

        Auth::login($user);

    // Ambil daftar nama_produksi dari users dengan type_user 3
        // $produksi = User::where('type_user', 3)->get();
        // session(['pop_up_produksi' => $produksi]);

    return redirect()->route('dashboard'); // Route ke halaman dashboard Blade
}

public function logout(Request $request)
{
    $user = $request->user();

    // Logout dari RTM-Form
    Auth::logout();

    // Hapus session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Report logout ke SSO / Employee API
    if ($user) {
        try {
            Http::withToken(config('services.employee_api.sso_secret'))
                ->timeout(5)
                ->post(
                    config('services.employee_api.url') . '/sso/report-logout',
                    [
                        'user_uuid'   => $user->uuid,
                        'project_uuid' => config('services.employee_api.this_project_uuid'),
                    ]
                );
        } catch (\Throwable $e) {
            // Jangan menggagalkan logout RTM-Form
            // jika SSO API sedang tidak bisa diakses
        }
    }

    // Kembali ke portal SSO
    return redirect(config('services.employee_api.portal_url'));
}

/*public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}*/
}
