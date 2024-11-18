<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $banners = Banner::latest()->get();

    //     return view('admin.banners.index', compact('banners'));
    // }
    public function index()
{
    $banners = Banner::where('admin_id', auth()->id())->get(); // Fetch banners for the logged-in admin

    return view('admin.banners.index', compact('banners'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required',
    //     ]);
    //     // image
    //     $image = $request->file('image');
    //     $ext = $image->getClientOriginalExtension();
    //     $filename = uniqid('banner').'.'.$ext; // Generate a unique filename
    //     $image->move(public_path('assets/img/banners/'), $filename); // Save the file
    //     Banner::create([
    //         'image' => $filename,
    //     ]);

    //     return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
    // }
    public function store(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Image upload logic
    $image = $request->file('image');
    $ext = $image->getClientOriginalExtension();
    $filename = uniqid('banner') . '.' . $ext; // Generate a unique filename
    $image->move(public_path('assets/img/banners/'), $filename); // Save the file

    // Save the banner with the admin's ID
    Banner::create([
        'image' => $filename,
        'admin_id' => auth()->id(), // Associate with the authenticated admin
    ]);

    return redirect(route('admin.banners.index'))->with('success', 'New Banner Image Added.');
}


    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        $request->validate([
            'image' => 'required',
        ]);
        //remove banner from localstorage
        File::delete(public_path('assets/img/banners/'.$banner->image));

        // image
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension();
        $filename = uniqid('banner').'.'.$ext; // Generate a unique filename
        $image->move(public_path('assets/img/banners/'), $filename); // Save the file

        $banner->update([
            'image' => $filename,
        ]);

        return redirect(route('admin.banners.index'))->with('success', 'Banner Image Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        if (! $banner) {
            return redirect()->back()->with('error', 'Banner Not Found');
        }
        //remove banner from localstorage
        File::delete(public_path('assets/img/banners/'.$banner->image));
        $banner->delete();

        return redirect()->back()->with('success', 'Banner Deleted.');
    }
}