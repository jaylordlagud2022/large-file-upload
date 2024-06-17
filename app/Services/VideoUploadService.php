<?php

namespace App\Services;

use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoUploadService
{
    public function handleUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, AbstractHandler::class);

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
}
