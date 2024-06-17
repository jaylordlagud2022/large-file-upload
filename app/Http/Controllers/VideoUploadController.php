<?php

namespace App\Http\Controllers;

use App\Services\VideoUploadService;
use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    protected $videoUploadService;

    public function __construct(VideoUploadService $videoUploadService)
    {
        $this->videoUploadService = $videoUploadService;
    }

    public function upload(Request $request)
    {
        return $this->videoUploadService->handleUpload($request);
    }
}
