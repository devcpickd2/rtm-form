<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ApiController extends Controller
{

    private function _mapRole($roleName)
    {
        $mapping = [
            'admin' => 0,
            'Manager' => 1,
            'SPV QC' => 2,
            'Produksi' => 3,
            'Forelady' => 8,
            'QC Inspector' => 4,
        ];

        return $mapping[$roleName] ?? null;
    }
    
    public function syncUser(Request $request)
    {
        $data = $request->json()->all();

        if (empty($data['user'])) {
            return response()->json(['status' => 'error', 'message' => 'User missing'], 400);
        }

        $user = $data['user'];

        if (
            empty($user['username'])
            || empty($user['department']['name'])
            || empty($user['department']['plant'])
        ) {
            return response()->json(['status' => 'error', 'message' => 'Missing required fields'], 400);
        }

        DB::beginTransaction();
        try {
            // Ambil departemen & plant berdasarkan nama
            $departemen = Departemen::firstOrCreate(
                ['nama' => $user['department']['name']],
                ['uuid' => $user['department']['uuid'] ?? Str::uuid()]
            );

            $plant = Plant::firstOrCreate(
                ['plant' => $user['department']['plant']],
                ['uuid' => $user['department']['uuid'] ?? Str::uuid()]
            );

            // Cek user existing
            $existingUser = User::where('username', $user['username'])->first();

            $userData = [
                'uuid' => $user['uuid'] ?? Str::uuid(),
                'name' => $user['name'] ?? '',
                'username' => $user['username'],
                'email' => $user['email'] ?? null,
                'department' => $departemen->uuid,
                'plant' => $plant->uuid,
                'type_user' => $this->_mapRole($user['project_role']['role'] ?? null) ?? 0,
                'activation' => $user['activation'] ?? 0,
            ];

            if (!empty($user['password'])) {
                $userData['password'] = $user['password'];
            }

            if ($existingUser) {
                $existingUser->update($userData);
            } else {
                User::create($userData);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'User synced successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error('SyncUser Error', ['exception' => $e->getMessage(), 'user' => $user]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function syncPlant(Request $request)
    {
        $data = $request->json()->all();

        if (empty($data['plant'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload: plant missing'], 400);
        }

        $plantData = $data['plant'];

        DB::beginTransaction();
        try {
            $plant = Plant::updateOrCreate(
                ['uuid' => $plantData['uuid'] ?? Str::uuid()],
                ['plant' => $plantData['plant']]
            );

            if (!empty($plantData['departments']) && is_array($plantData['departments'])) {
                foreach ($plantData['departments'] as $deptData) {
                    Departemen::updateOrCreate(
                        ['uuid' => $deptData['uuid'] ?? Str::uuid()],
                        ['nama' => $deptData['department']]
                    );
                }
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Plant & Departemen synced successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error('SyncPlant Error', ['exception' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Desync user dari UUID
     */
    public function desyncUser(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user_uuid'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload'
                ], 400);
            }

            $user = User::where('uuid', $data['user_uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User desynced successfully: ' . $data['user_uuid']
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aktivasi user
     */
    public function activation(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user']['uuid'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload: uuid missing'
                ], 400);
            }

            $user = User::where('uuid', $data['user']['uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->activation = 1;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User Activation Success'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password user
     */
    public function changePassword(Request $request)
    {
        try {
            $data = $request->json()->all();

            if (empty($data['user']['uuid']) || empty($data['user']['password'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid payload: uuid or password missing'
                ], 400);
            }

            $user = User::where('uuid', $data['user']['uuid'])->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->password = Hash::make($data['user']['password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}