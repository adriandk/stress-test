<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    /**
     * Form untuk Staff BK menambahkan akun baru (Staff BK atau Konselor).
     */
    public function create()
    {
        // Bisa juga kirim daftar role kalau mau dinamis
        $roles = ['staff_bk' => 'Staff BK', 'konselor' => 'Konselor'];

        return view('accounts.create', compact('roles'));
    }

    /**
     * Proses simpan akun baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:150', 'unique:accounts,email'],
            'password'     => ['required', 'min:6', 'confirmed'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'account_type' => ['required', 'in:staff_bk,konselor'],
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat akun utama
            $account = Account::create([
                'email'         => $data['email'],
                'password_hash' => Hash::make($data['password']),
                'account_type'  => $data['account_type'],
                'is_active'     => 1,
                'created_by'    => Auth::id(), // staff BK yang sekarang login
            ]);

            // 2. Buat profil tergantung role
            if ($data['account_type'] === 'staff_bk') {
                DB::table('staffs')->insert([
                    'account_id' => $account->id,
                    'name'       => $data['name'],
                    'phone'      => $data['phone'] ?? null,
                    'bio'        => null,
                    'avatar_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else { // konselor
                DB::table('counselors')->insert([
                    'account_id' => $account->id,
                    'name'       => $data['name'],
                    'phone'      => $data['phone'] ?? null,
                    'bio'        => null,
                    'avatar_url' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('accounts.create')
                ->with('success', 'Akun baru berhasil dibuat sebagai ' . $data['account_type'] . '.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // logger()->error($e); // kalau mau log

            return back()
                ->withErrors(['general' => 'Terjadi kesalahan saat membuat akun baru.'])
                ->withInput();
        }
    }
}