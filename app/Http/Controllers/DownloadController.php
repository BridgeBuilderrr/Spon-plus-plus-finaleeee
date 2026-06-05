<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    /**
     * Handle material or assignment file download
     */
    public function download(Request $request)
    {
        $path = $request->query('path');

        if (!$path) abort(404);

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);

        return response()->download($fullPath);
    }
}
