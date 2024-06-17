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

    /**
     * Handles the file upload.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(Request $request)
    {
        return $this->videoUploadService->handleUpload($request);
    }

    /**
     * Retrieve and stream the video file.
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function getVideo($filename)
    {
        return $this->videoService->getVideo($filename);
    }
}
