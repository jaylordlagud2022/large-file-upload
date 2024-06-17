<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\VideoUploadService;
use Illuminate\Http\Request;

class VideoUploadServiceTest extends TestCase
{
    public function testHandleUpload()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('video.mp4', 10240); // 10 MB fake file

        $request = Request::create('/upload', 'POST', [], [], ['file' => $file]);

        $service = new VideoUploadService();
        $response = $service->handleUpload($request);

        $this->assertEquals(200, $response->status());
        Storage::disk('local')->assertExists('videos/' . $file->getClientOriginalName());
    }
}
