<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    /**
     * INDEX INTERNAL: list untuk Staff BK & Konselor (manage).
     */
    public function index()
    {
        $contacts = EmergencyContact::with(['creator', 'updater'])
            ->orderBy('name')
            ->paginate(10);

        return view('emergency_contacts.index', compact('contacts'));
    }

    /**
     * INDEX PUBLIK: list untuk mahasiswa anonim.
     * Hanya yang aktif (is_active = true).
     */
    public function publicIndex()
    {
        $contacts = EmergencyContact::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('emergency_contacts.public_index', compact('contacts'));
    }

    /**
     * FORM CREATE.
     */
    public function create()
    {
        return view('emergency_contacts.create');
    }

    /**
     * STORE: simpan kontak baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:255'],
            'whatsapp_number'    => ['required', 'string', 'max:30', 'regex:/^(0|62)\d+$/'],
            'available_days'     => ['nullable', 'array'],
            'available_days.*'   => ['in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'available_time_start' => ['nullable', 'date_format:H:i'],
            'available_time_end'   => ['nullable', 'date_format:H:i'],
            'is_active'          => ['nullable', 'boolean'],
        ], [
            'whatsapp_number.regex' => 'Format nomor WhatsApp harus diawali 0 atau 62 dan hanya angka (tanpa +).',
        ]);

        // Normalisasi nomor WhatsApp
        $data['whatsapp_number'] = $this->normalizeWhatsappNumber($data['whatsapp_number']);

        // Ubah array hari → string
        if (!empty($data['available_days'])) {
            $data['available_days'] = implode(', ', $data['available_days']);
        } else {
            $data['available_days'] = null;
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        EmergencyContact::create($data);

        return redirect()
            ->route('emergency-contacts.index')
            ->with('success', 'Kontak darurat berhasil ditambahkan.');
    }

    /**
     * EDIT FORM.
     */
    public function edit(EmergencyContact $emergency_contact)
    {
        return view('emergency_contacts.edit', [
            'contact' => $emergency_contact,
        ]);
    }

    /**
     * UPDATE.
     */
    public function update(Request $request, EmergencyContact $emergency_contact)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:255'],
            'whatsapp_number'    => ['required', 'string', 'max:30', 'regex:/^(0|62)\d+$/'],
            'available_days'     => ['nullable', 'array'],
            'available_days.*'   => ['in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'available_time_start' => ['nullable', 'date_format:H:i'],
            'available_time_end'   => ['nullable', 'date_format:H:i'],
            'is_active'          => ['nullable', 'boolean'],
        ], [
            'whatsapp_number.regex' => 'Format nomor WhatsApp harus diawali 0 atau 62 dan hanya angka (tanpa +).',
        ]);

        $data['whatsapp_number'] = $this->normalizeWhatsappNumber($data['whatsapp_number']);

        // Ubah array hari → string
        if (!empty($data['available_days'])) {
            $data['available_days'] = implode(', ', $data['available_days']);
        } else {
            $data['available_days'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['updated_by'] = Auth::id();

        $emergency_contact->update($data);

        return redirect()
            ->route('emergency-contacts.index')
            ->with('success', 'Kontak darurat berhasil diperbarui.');
    }

    /**
     * DELETE.
     */
    public function destroy(EmergencyContact $emergency_contact)
    {
        $emergency_contact->delete();

        return redirect()
            ->route('emergency-contacts.index')
            ->with('success', 'Kontak darurat berhasil dihapus.');
    }

    /**
     * Normalisasi nomor WA:
     * - 081xxxx → 6281xxxx
     * - 6281xxxx → 6281xxxx
     * - "+6281..." ditolak via regex
     */
    private function normalizeWhatsappNumber(string $number): string
    {
        // Hapus karakter non-angka
        $digits = preg_replace('/\D+/', '', $number);

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62' . $digits;
    }
}