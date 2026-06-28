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
    private function normalizePhoneNumber($phone)
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }
        return $clean;
    }

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
            'avatar' => 'nullable|string|in:avatar1,avatar2,avatar3,avatar4,avatar5,avatar6,avatar7,avatar8,avatar9,avatar10',
            'old_password' => 'required_with:password|nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal harus 8 karakter.',
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

        // Handle pre-selected Avatar
        if ($request->filled('avatar')) {
            // Delete old uploaded avatar if it was custom
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $user->avatar = '/images/avatars/' . $request->avatar . '.png';
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $this->normalizePhoneNumber($request->phone);
        $user->address = $request->address;
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
