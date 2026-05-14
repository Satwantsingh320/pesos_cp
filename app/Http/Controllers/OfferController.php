<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortEntity = (new Offer())->sortEntity;
        $sortOrder = (new Offer())->sortOrder;

        $result = null;
        if ($request->ajax()) {
            $sortEntity = $request->sortEntity;
            $sortOrder = $request->sortOrder;
            $result = (new Offer)->pagination($request);
            return view('admin.offers.pagination', compact('result', 'sortOrder', 'sortEntity'));
        }
        $url = url()->full();
        return view('admin.offers.index', compact('url', 'result', 'sortOrder', 'sortEntity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        return view('admin.offers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $rules = [
            'title' => 'required|string',
            'description' => 'nullable',
            'status' => 'in:0,1',
            'banner' => 'required|mimes:jpg,jpeg,png,svg,webp'
        ];
        $validation = validator($inputs, $rules);
        if ($validation->fails()) {
            return back()->withErrors($validation->getMessageBag());
        }

        // try{
        DB::beginTransaction();

        $bannerImg = null;
        $path = public_path(OFFER_BANNERS_PATH);
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            $bannerImg = $fileName;
        }
        Offer::create([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'status' => $inputs['status'],
            'banner' => $bannerImg
        ]);

        DB::commit();
        return redirect()->route('banners.index')->with('success', __('admin.offer_created_successfully'));


        // } catch (\Exception $e) {
        // DB::rollBack();
        //   return jsonResponse(true, 207, __('internal_server_error'));
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $banner)
    {

        return view('admin.offers.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $banner)
    {

        return view('admin.offers.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'description' => 'nullable',
            'status' => 'in:0,1',
            'banner' => 'nullable|mimes:jpg,jpeg,png,svg,webp'
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();
            $offer = Offer::find($id);
            $offer->title = $request->title;
            $offer->description = $request->description;
            $offer->status = $request->status;


            /* Cover image */
            if ($request->hasFile('banner')) {
                $path = public_path(OFFER_BANNERS_PATH);
                $file = $request->file('banner');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($path, $fileName);
                $offer->banner = $fileName;
            }

            /* UPDATE Offer */
            $offer->save();

            DB::commit();

            return redirect()->back()->with('success', __('admin.offer_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $offer = Offer::find($id);
            /*  Delete cover image */
            if ($offer->banner) {
                $coverPath = public_path(OFFER_BANNERS_PATH . $offer->banner);
                if (File::exists($coverPath)) {
                    File::delete($coverPath);
                }
            }
            /*  Delete offer */
            $offer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => __('admin.offer_deleted_successfully'),
                'extra' => ['redirect' => route('banners.index')]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleAllStatus($status, Request $request)
    {

        $inputs = $request->only('ids');

        try {
            DB::beginTransaction();
            $inputs = $request->only('ids');

            (new Offer())->toggleStatus($status, $inputs['ids']);
            DB::commit();

            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }

    public function status($id)
    {
        $result = Offer::findorFail($id);
        if (!$result) {
            $message = __('admin.invalid_detail');
            return jsonResponse(false, 207, $message);
        }

        try {
            DB::beginTransaction();
            $result->update(['status' => !$result->status]);
            DB::commit();
            return response()->json(['success' => true, 'status' => 201, 'message' => __('admin.status_updated_successfully'), 'extra' => ['redirect' => route('products.index')]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return jsonResponse(false, 207, __('admin.server_error'));
        }
    }
}
