<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'no_hp'    => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profil berhasil diupdate.',
            'user'    => $user,
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        // Hapus foto lama kalau ada
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $file     = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = 'photos/profile/' . $filename;

        // buat directory jika belum ada
        if (!Storage::disk('public')->exists('photos/profile')) {
            Storage::disk('public')->makeDirectory('photos/profile');
        }

        Image::read($file)
            ->scale(width: 500)
            ->save(storage_path('app/public/' . $path));

        $user->update(['foto_profil' => $path]);

        return response()->json([
            'message'     => 'Foto profil berhasil diupdate.',
            'foto_profil' => $path,
        ]);
    }
}