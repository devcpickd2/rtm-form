<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class SsoLoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'ticket' => 'required|uuid',
        ]);

        $response = Http::withToken(
                config('services.employee_api.sso_secret')
            )
            ->timeout(10)
            ->post(
                config('services.employee_api.url') . '/sso/verify',
                [
                    'ticket' => $request->ticket,
                    'project_uuid' => config(
                        'services.employee_api.this_project_uuid'
                    ),
                ]
            );

        // Tidak memiliki akses ke project
        if ($response->status() === 403) {
            return redirect()->route('login')->withErrors([
                'sso' => 'Anda tidak memiliki akses ke sistem ini.',
            ]);
        }

        // Ticket invalid / expired / sudah digunakan
        if ($response->failed()) {
            return redirect()->route('login')->withErrors([
                'sso' => 'Sesi login otomatis tidak valid atau sudah kedaluwarsa. Silakan login manual.',
            ]);
        }

        $remoteUser = $response->json('user');

        if (!$remoteUser || empty($remoteUser['uuid'])) {
            return redirect()->route('login')->withErrors([
                'sso' => 'Data user dari Employee API tidak valid.',
            ]);
        }

        // Cari user lokal berdasarkan UUID
        $user = User::where(
            'uuid',
            $remoteUser['uuid']
        )->first();

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'sso' => 'Akun tidak ditemukan di sistem ini.',
            ]);
        }

        // Login lokal
        Auth::login($user);

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}