<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'title',
        'category',
        'content',
        'date',
        'letter_number',
        'attachment_path',
        'external_link',
        'badge_color',
        'is_active',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Helper styling badge category
     */
    public function getCategoryColorClassesAttribute(): array
    {
        return match (strtoupper($this->category)) {
            'PENTING' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'badge' => 'bg-rose-600 text-white'],
            'MAKLUMAT' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'badge' => 'bg-amber-600 text-white'],
            'SURAT EDARAN' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'badge' => 'bg-emerald-600 text-white'],
            'AGENDA' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'badge' => 'bg-blue-600 text-white'],
            default => ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'badge' => 'bg-slate-600 text-white'],
        };
    }
}
