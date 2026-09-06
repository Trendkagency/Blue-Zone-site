<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\View\ViewModels\RoleViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $role = $user->role;
        $modules = RoleViewModel::modules();
        $userPermissions = (array) ($role?->permissions ?? []);

        return view('admin.profile.index', [
            'user' => $user,
            'role' => $role,
            'modules' => $modules,
            'userPermissions' => $userPermissions,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $avatarPath = $user->avatar;

        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $media = $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            $avatarPath = 'storage/' . $media->id . '/' . $media->file_name;
        } elseif ($request->hasFile('avatar_file')) {
            $user->clearMediaCollection('avatar');
            $media = $user->addMediaFromRequest('avatar_file')->toMediaCollection('avatar');
            $avatarPath = 'storage/' . $media->id . '/' . $media->file_name;
        } elseif (is_string($request->input('avatar')) && !empty($request->input('avatar'))) {
            $path = $request->input('avatar');
            if (str_contains($path, '/storage/')) {
                $path = 'storage/' . ltrim(substr($path, strpos($path, '/storage/') + 9), '/');
            }
            $avatarPath = $path;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('admin.profile.index')->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ وتحديث بيانات الملف الشخصي بنجاح!'
            : 'Personal profile details updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => app()->getLocale() === 'ar'
                    ? 'كلمة المرور الحالية غير صحيحة.'
                    : 'The current password entered is incorrect.',
            ])->with('error', app()->getLocale() === 'ar' ? 'كلمة المرور الحالية غير صحيحة.' : 'Current password verification failed.');
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile.index')->with('success', app()->getLocale() === 'ar'
            ? 'تم تغيير كلمة المرور وتأمين الحساب بنجاح!'
            : 'Security credentials and password updated successfully!');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'sound_enabled' => 'nullable',
            'language' => 'nullable|string|in:ar,en',
            'locale' => 'nullable|string|in:ar,en',
            'theme' => 'nullable|string|in:light,dark,system',
            'email_notifications' => 'nullable',
        ]);

        $locale = $validated['language'] ?? $validated['locale'] ?? 'ar';
        $preferences = $user->preferences ?? [];
        $preferences['sound_enabled'] = $request->boolean('sound_enabled', false);
        $preferences['language'] = $locale;
        $preferences['locale'] = $locale;
        $preferences['theme'] = $validated['theme'] ?? 'light';
        $preferences['email_notifications'] = $request->boolean('email_notifications', true);

        $user->update([
            'preferences' => $preferences,
        ]);

        // If language switched, update session locale
        if (!empty($locale)) {
            session(['locale' => $locale]);
        }

        return redirect()->route('admin.profile.index')->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ تفضيلات النظام والإشعارات بنجاح!'
            : 'System preferences and acoustic notifications saved!');
    }
}
