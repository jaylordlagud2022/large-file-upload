<?php

namespace App\Services;

use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseResponse;

class VideoUploadService
{

    /**
     * Handles the file upload.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function handleUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded()) {
            $save = $receiver->receive();

            if ($save->isFinished()) {
                return $this->saveFile($save->getFile());
            } else {
                return response()->json([
                    "done" => $save->handler()->getPercentageDone()
                ]);
            }
        }

        return response()->json([
            "error" => "File not uploaded"
        ], 400);
    }

    protected function saveFile(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();
        $path = Storage::putFileAs('videos', $file, $fileName);

        return response()->json([
            "path" => $path,
            "name" => $fileName,
            "mime_type" => $file->getClientMimeType()
        ]);
    }

    /**
     * Retrieve and stream the video file.
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getVideo($filename)
    {
        // Check if the file exists in storage
        if (!Storage::disk('local')->exists("uploads/$filename")) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        // Stream the video file
        $path = storage_path("app/uploads/$filename");

        return response()->stream(function () use ($path) {
            $stream = fopen($path, 'rb');
            fpassthru($stream);
        }, 200, [
            'Content-Type' => mime_content_type($path),
            'Content-Length' => filesize($path),
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }
}
