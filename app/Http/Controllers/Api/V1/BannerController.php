<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use HttpResponses;

    // public function index()
    // {
    //     $data = Banner::all();

    //     return $this->success($data);
    // }
     public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error(null, 'Unauthorized', 401);
        }

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = Banner::where('admin_id', $admin->id)->get();

        return $this->success($data, 'Banners retrieved successfully.');
    }

    public function bannerText()
    {
        $data = BannerText::latest()->first();

        return $this->success($data);
    }

    public function AdsBannerIndex()
    {
        $data = BannerAds::latest()->first();

        return $this->success($data);
    }
}