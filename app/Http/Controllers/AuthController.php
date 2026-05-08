<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    public function showLoginFormWebsite()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard.index');
        }

        return view('auth.login_website');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors([
            'error-message' => __('admin.Invalid login details.')
        ]);
    }
    public function loginWebsite(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        // Retrieve the customer first
        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer || $customer->status != 1) {
            return back()->withErrors([
                'error-message' => __('admin.Your account is inactive or invalid login details.')
            ]);
        }
        if (Auth::guard('customer')->attempt($credentials)) {
            $sessionId = session()->getId();
            $customerId = Auth::id();

            DB::table('carts')
                ->where('session_id', $sessionId)
                ->whereNull('customer_id')
                ->update([
                    'customer_id' => $customerId,
                    'updated_at' => now()
                ]);
            app(\App\Http\Controllers\Website\WishlistController::class)->mergeAfterLogin();
            $request->session()->regenerate();
            return redirect()->route('customer.dashboard.index');
        }

        return back()->withErrors([
            'error-message' => __('admin.Invalid login details.')
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $redirect = '/admin/login';

        } elseif (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
            $redirect = '/';

        } else {
            Auth::logout(); // fallback default
            $redirect = '/';
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($redirect);
    }

    public function forgotPassword(Request $request)
    {

    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email', // Check uniqueness in customers table
            'dial_code_iso' => 'required|string|max:5',
            'dial_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'password' => 'required|min:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => '6LdVgYEsAAAAANkpUai_jckTNRqJj5h7_4CkYbfu',
            'response' => $request->input('recaptcha_token'),
            'remoteip' => $request->ip(),
        ]);
        $body = $response->json();

        if (!isset($body['success']) || $body['success'] != true || $body['score'] < 0.5) {
            // Adjust score threshold (0.5 is typical)
            return redirect()->back()
                ->withErrors(['captcha' => 'Captcha verification failed.'])
                ->withInput();
        }

        // 2. Handle Image Upload to public/uploads/customers
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/customers'), $imageName);
            $imagePath = 'uploads/customers/' . $imageName;
        }

        // 3. Create the Customer record
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'dial_code_iso' => $request->dial_code_iso,
            'dial_code' => $request->dial_code,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'image' => $imagePath,
            'status' => 1, // Defaulting to 1 (Active)
        ]);

        // 4. Log the customer in
        Auth::guard('customer')->login($customer);

        $sessionId = session()->getId();
        $customerId = Auth::id();
        DB::table('carts')
            ->where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->update([
                'customer_id' => $customerId,
                'updated_at' => now()
            ]);
        app(\App\Http\Controllers\Website\WishlistController::class)->mergeAfterLogin();

        // 5. Regenerate session and redirect
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard.index')
            ->with('success', __('admin.Account created successfully!'));
    }

    public function terms()
    {
        return view('auth.terms');
    }

    public function privacyPolicy()
    {
        return view('auth.privacy_policy');
    }

    public function returnPolicy()
    {
        return view('auth.return_policy');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => __('admin.Current password does not match.')])->with('active_tab', 'v-password');
        }

        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', __('admin.Password changed successfully!'))->with('active_tab', 'v-password');
    }

    public function markAsRead($id)
    {
        $notification = auth('web')->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => __('admin.Notifications marked as read.')]);
    }
    public function markAllAsRead()
    {
        auth('web')->user()
            ->unreadNotifications
            ->markAsRead();
        return response()->json(['success' => true, 'message' => __('admin.All notifications marked as read.')]);
    }

    public function notifications()
    {
        $notifications = auth('web')
            ->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }
}
