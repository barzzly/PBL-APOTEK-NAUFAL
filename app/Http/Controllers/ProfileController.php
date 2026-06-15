<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();
        $categories = Category::all();
        
        return view('profile.edit', compact('user', 'categories'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'old_password' => 'required_with:password|nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
            'avatar.max' => 'Ukuran foto profil maksimal adalah 2MB.',
            'avatar.image' => 'File harus berupa gambar.',
        ]);

        // Validate old password if new password is provided
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                throw ValidationException::withMessages([
                    'old_password' => ['Kata sandi lama yang Anda masukkan salah.'],
                ]);
            }
            $user->password = Hash::make($request->password);
        }

        // Handle Avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                // If it starts with /storage/
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
