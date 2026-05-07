<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Customer())->sortEntity;
        $sortOrder = (new Customer())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;

            $result = (new Customer)->pagination($request);

            return view('admin.customers.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.customers.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clean_phone = preg_replace('/^' . preg_quote($request->dial_code, '/') . '/', '', $request->phone);
        $request->merge(['phone' => $clean_phone]);
        $inputs = $request->all();
        $rules = [
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:customers,email',
            'dial_code' => 'required',
            'dial_code_iso' => 'required',
            'phone' => 'required|digits:10||unique:customers,phone',
            'password' => 'required|min:8',
            'status' => 'in:0,1'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }
        $customerImg = null;
        $path = public_path(CUSTOMERS_PATH);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            $customerImg = $fileName;
        }
        Customer::create([
            'name' => $inputs['name'],
            'email' => $inputs['email'],
            'dial_code' => $inputs['dial_code'],
            'dial_code_iso' => $inputs['dial_code_iso'],
            'phone' => $inputs['phone'],
            'password' => Hash::make($inputs['password']),
            'status' => $inputs['status'],
            'image' => $customerImg
        ]);
        return redirect()->route('customers.index')->with('success', __('admin.customer_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $addresses = $customer->addresses()->orderBy('is_default', 'desc')->get();
        return view('admin.customers.show', compact('customer', 'addresses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $clean_phone = preg_replace('/^' . preg_quote($request->dial_code, '/') . '/', '', $request->phone);
        $request->merge(['phone' => $clean_phone]);
        $validated = $request->validate([
            'name' => 'nullable|string|max:150',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'dial_code' => 'required',
            'dial_code_iso' => 'required',
            'phone' => 'required|digits:10|unique:customers,phone,' . $customer->id,
            'phone' => [
                'required',
                'digits:10',
                Rule::unique('customers')->where(function ($query) use ($request) {
                    return $query->where('dial_code', $request->dial_code)->where('dial_code_iso', $request->dial_code_iso);
                })->ignore($customer->id)
            ],
            'password' => 'nullable|min:8',
            'status' => 'required|boolean',
        ]);
        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        $customer->dial_code = $validated['dial_code'];
        $customer->dial_code_iso = $validated['dial_code_iso'];
        $customer->phone = $validated['phone'];
        $customer->status = $validated['status'];
        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }
        if ($validated['status'] == 0) {
            $customer->tokens()->delete();
        }
        if ($request->hasFile('image')) {
            $path = public_path(CUSTOMERS_PATH);
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);
            $customer->image = $fileName;
        }
        $customer->save();

        return redirect()->back()->with('success', __('admin.customer_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => __('admin.customer_deleted_successfully'),
            'extra' => ['redirect' => route('customers.index')]
        ]);
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new Customer)->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('customers.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = Customer::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('customers.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function dashboard()
    {
        $customerId = Auth::guard('customer')->id();
        // Fetch customer with their specific orders and addresses
        $customer = Auth::guard('customer')->user();
        $orders = Order::where('customer_id', $customerId)->latest()->paginate(10);
        $addresses = Address::where('customer_id', $customerId)->get();

        return view('website.dashboard', compact('customer', 'orders', 'addresses'));
    }
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Add Address
    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required',
            'address' => 'required',
            'colonia' => 'required',
            'city' => 'required',
            'state' => 'required',
            'postcode' => 'required',
            'type' => 'required|in:Home,Office,Other',
        ]);

        $customerId = Auth::guard('customer')->id();

        // If first address, make it default
        $isFirst = Address::where('customer_id', $customerId)->count() == 0;

        Address::create(array_merge($request->all(), [
            'customer_id' => $customerId,
            'is_default' => $isFirst ? 1 : 0,
            'status' => 1
        ]));

        return back()->with('success', 'Address added successfully!')->with('active_tab', 'v-address');
    }

    // Set Default Address
    public function setDefaultAddress($id)
    {
        $customerId = Auth::guard('customer')->id();

        // Remove current default
        Address::where('customer_id', $customerId)->update(['is_default' => 0]);

        // Set new default
        Address::where('customer_id', $customerId)->where('id', $id)->update(['is_default' => 1]);

        return back()->with('success', 'Default address updated.')->with('active_tab', 'v-address');
    }

    // Delete Address
    public function deleteAddress($id)
    {
        Address::where('customer_id', Auth::guard('customer')->id())->where('id', $id)->delete();
        return back()->with('success', 'Address deleted.')->with('active_tab', 'v-address');
    }

    // ... other imports

    public function profile(Request $request)
    {
        $auth = auth('customer')->user();
        $inputs = $request->all();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $auth->id,
            'dial_code' => 'required',
            'phone' => 'required|digits:10|unique:customers,phone,' . $auth->id,
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag())->with('active_tab', 'v-profile');
        }


        // 3. If validation passed, continue with your logic
        $auth->name = $request->name;
        $auth->email = $request->email;
        $auth->dial_code = $request->dial_code;
        $auth->phone = $request->phone;
        $auth->rfc_number = $request->rfc_number;

        $path = public_path(CUSTOMERS_PATH);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            $auth->image = $fileName;
        }

        $auth->save();

        return back()
            ->with('success', __('admin.Profile updated successfully.'))
            ->with('active_tab', 'v-profile');
    }
    public function orderDetail($id)
    {
        $order = Orderfind($id);
        $order->load('customer', 'items.product');
        return view('website.order-detail', compact('order'));
    }

}
