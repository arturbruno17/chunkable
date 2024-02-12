<?php

namespace Posart\Chunkable\Requests;

use Posart\Chunkable\Exceptions\ChunkUploadPayloadException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\HttpFoundation\File\File as MovedFile;

class ChunkUploadRequest
{
    public Request $request;
    public MovedFile $file;

    /**
     * @throws ValidationException
     */
    public function __construct(Request $request)
    {
        Validator::make($request->all(), [
            'file_unique_identifier' => ['required'],
            'chunk_size' => ['required', 'integer'],
            'file_offset' => ['required', 'integer'],
            'file_size' => ['required', 'integer'],
            'file' => ['required', 'file'],
        ])->setException(ChunkUploadPayloadException::class)->validate();

        $this->request = $request;
        /** @var UploadedFile $originalFile */
        $originalFile = $this->request->file('file');

        $parts = intval(ceil($this->request->file_size / $this->request->chunk_size));
        $currentPart = intval(floor($this->request->file_offset / $this->request->chunk_size) + 1);

        $this->file = $originalFile->move(
            storage_path("app/upload/{$this->request->file_unique_identifier}"),
            $this->createChunkFileName($currentPart, $parts)
        );

        $uploadedParts = $this->rememberUploadedParts($currentPart);
        if (count($uploadedParts) == $parts) {
            $this->joinChunkFiles($parts);
        }
    }

    private function createChunkFileName(int $currentPart, int $parts): string
    {
        return "{$this->request->file_unique_identifier}_{$currentPart}_{$parts}.part";
    }


    private function rememberUploadedParts(int $currentPart): array
    {
        $uploadedParts = Cache::get("{$this->request->file_unique_identifier}.uploaded_parts") ?? [];
        $uploadedParts["$currentPart"] = true;
        Cache::set("{$this->request->file_unique_identifier}.uploaded_parts", $uploadedParts);
        return $uploadedParts;
    }

    private function joinChunkFiles(int $parts): void
    {
        $finalExtension = $this->file->guessExtension() ?? 'part';
        $finalVideo = @fopen(storage_path("app/upload/{$this->request->file_unique_identifier}") .
            "/{$this->request->file_unique_identifier}.{$finalExtension}", "a");

        for ($part = 1; $part <= $parts; $part++) {
            $chunkContent = @fopen(storage_path("app/upload/{$this->request->file_unique_identifier}") .
                "/{$this->request->file_unique_identifier}_{$part}_{$parts}.part", "r");

            while ($buff = fread($chunkContent, 4096)) {
                fwrite($finalVideo, $buff);
            }

            @fclose($chunkContent);
        }

        @fclose($finalVideo);
    }

}
