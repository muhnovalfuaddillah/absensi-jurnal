<?php

namespace App\Http\Controllers;

use App\Models\SchoolAnnouncement;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolAnnouncementController extends Controller
{
    /**
     * Tampilkan halaman daftar Informasi Sekolah & Maklumat Yayasan (Admin)
     */
    public function index()
    {
        $schoolId = current_school_id();
        $setting = AttendanceSetting::getSettings($schoolId);
        $announcements = SchoolAnnouncement::where('school_id', $schoolId)
            ->orderBy('is_active', 'desc')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $totalActive = SchoolAnnouncement::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalAll = SchoolAnnouncement::where('school_id', $schoolId)->count();

        return view('admin.announcements.index', compact('announcements', 'setting', 'totalActive', 'totalAll'));
    }

    /**
     * Simpan pengumuman / maklumat baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'content' => 'required|string',
            'date' => 'required|date',
            'letter_number' => 'nullable|string|max:150',
            'external_link' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        $badgeColor = match (strtoupper($validated['category'])) {
            'PENTING' => 'rose',
            'MAKLUMAT' => 'amber',
            'SURAT EDARAN' => 'emerald',
            'AGENDA' => 'blue',
            default => 'slate',
        };

        SchoolAnnouncement::create([
            'school_id' => current_school_id(),
            'title' => $validated['title'],
            'category' => $validated['category'],
            'content' => $validated['content'],
            'date' => $validated['date'],
            'letter_number' => $validated['letter_number'],
            'attachment_path' => $attachmentPath,
            'external_link' => $validated['external_link'],
            'badge_color' => $badgeColor,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Informasi Sekolah / Maklumat berhasil diterbitkan.');
    }

    /**
     * Update pengumuman / maklumat
     */
    public function update(Request $request, $id)
    {
        $announcement = SchoolAnnouncement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'content' => 'required|string',
            'date' => 'required|date',
            'letter_number' => 'nullable|string|max:150',
            'external_link' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($announcement->attachment_path && Storage::disk('public')->exists($announcement->attachment_path)) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }
            $announcement->attachment_path = $request->file('attachment')->store('announcements', 'public');
        }

        $badgeColor = match (strtoupper($validated['category'])) {
            'PENTING' => 'rose',
            'MAKLUMAT' => 'amber',
            'SURAT EDARAN' => 'emerald',
            'AGENDA' => 'blue',
            default => 'slate',
        };

        $announcement->title = $validated['title'];
        $announcement->category = $validated['category'];
        $announcement->content = $validated['content'];
        $announcement->date = $validated['date'];
        $announcement->letter_number = $validated['letter_number'];
        $announcement->external_link = $validated['external_link'];
        $announcement->badge_color = $badgeColor;
        $announcement->is_active = $request->boolean('is_active');
        $announcement->save();

        return redirect()->route('admin.announcements.index')->with('success', 'Informasi Sekolah / Maklumat berhasil diperbarui.');
    }

    /**
     * Toggle status aktif / tidak aktif
     */
    public function toggleStatus($id)
    {
        $announcement = SchoolAnnouncement::findOrFail($id);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        $statusStr = $announcement->is_active ? 'diaktifkan & tampil di aplikasi Guru' : 'dinonaktifkan';
        return redirect()->route('admin.announcements.index')->with('success', "Informasi '{$announcement->title}' berhasil {$statusStr}.");
    }

    /**
     * Hapus pengumuman
     */
    public function destroy($id)
    {
        $announcement = SchoolAnnouncement::findOrFail($id);

        if ($announcement->attachment_path && Storage::disk('public')->exists($announcement->attachment_path)) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Informasi Sekolah / Maklumat telah dihapus.');
    }
}
