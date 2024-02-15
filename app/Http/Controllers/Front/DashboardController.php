<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function displayDashboard()
    {
        return view('front.dashboard');
    }

    public function displayUserImage($foldername, $filename)
    {
        $path = storage_path('app/public/' . $foldername . '/' . $filename);
        if (!File::exists($path)) {
            $path = public_path('images/avatar5.png');
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
