<?php

namespace App\Http\Controllers;

use App\Models\PageSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('admin.settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'old_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('old_password') || $request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['error' => __('admin.Old password is incorrect.')]);
            }

            if ($request->new_password) {
                $user->password = bcrypt($request->new_password);
            }
        }
        $path = public_path(USER_PATH);
        if ($request->hasFile('image')) {
            if (!empty($user->image) && file_exists($path . $user->image)) {
                unlink($path . $user->image);
            }
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            $user->image = $fileName;
        }
        $user->save();
        return back()->with('success', __('admin.Profile updated successfully.'));
    }


    public function about()
    {
        $page = Setting::where('page_name', 'about')->latest()->first();
        return view('settings.about', compact('page'));
    }

    public function help()
    {
        $page = Setting::where('page_name', 'help')->latest()->first();
        return view('settings.help', compact('page'));
    }

    public function t_and_c()
    {
        $page = Setting::where('page_name', 'terms_and_conditions')->latest()->first();
        return view('settings.t_and_c', compact('page'));
    }

    public function privacy_policy()
    {
        $page = Setting::where('page_name', 'privacy_policy')->latest()->first();
        return view('settings.privacy_policy', compact('page'));
    }

    public function updatePageSetting(Request $request)
    {
        $data = $request->validate([
            'page_name' => 'required|in:about,help,t_and_c,privacy_policy',
        ]);

        $page = Setting::where('page_name', $request->page_name)->first();
        if (!$page) {
            Setting::create([
                'page_name' => $request->page_name,
                'content' => $request->input('content')
            ]);
        } else {
            $page->update([
                'content' => $request->input('content'),
            ]);
        }

        return redirect()->back()->with(['success' => __('admin.Page updated successfully.')]);
    }

    public function getSettings()
    {
        $settings = Setting::first();
        return view('admin.settings.all-settings', compact('settings'));
    }

    public function updateSettings(Request $request, $id)
    {
        $data = $request->validate([
            'tax' => 'required',
            'free_shipping' => 'required',
            'return_policy' => 'required'
        ]);

        $settings = Setting::where('id', $id)->first();
        $settings->update([
            'free_shipping' => $request->free_shipping,
            'tax' => $request->tax,
            'return_policy' => $request->return_policy,
        ]);

        return redirect()->back()->with(['success' => __('admin.settings_updated_successfully')]);
    }

}
