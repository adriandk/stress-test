<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $table = 'emergency_contacts';

    protected $fillable = [
        'name',
        'description',
        'whatsapp_number',
        'available_days',
        'available_time_start',
        'available_time_end',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Account::class, 'updated_by');
    }
}