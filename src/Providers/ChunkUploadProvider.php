<?php

namespace Posart\Chunkable\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\Requests\ChunkUploadRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Posart\Chunkable\Storage\AbstractStorage;
use Posart\Chunkable\Storage\ChunkStorage;

class ChunkUploadProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $configPath =  __DIR__.'/../../config/chunkable.php';
        $configFilename = 'chunkable.php';
        $configBasename = 'chunkable';
        $this->publishes([
           $configPath => config_path($configFilename),
        ]);

        $this->mergeConfigFrom($configPath, $configBasename);

        $this->app->bind(AbstractStorage::class, function () {
            return new ChunkStorage(Storage::disk(config("chunkable.storage.disk")));
        });

        $this->app->bind(ChunkUploadRequest::class, function (Application $app) {
            /** @var Request $request */
            $request = $app->make(Request::class);
            /** @var AbstractStorage $storage  */
            $storage = $app->make(AbstractStorage::class);

            Validator::make($request->all(), [
                'file_unique_identifier' => ['required'],
                'chunk_size' => ['required', 'integer'],
                'file_offset' => ['required', 'integer'],
                'file_size' => ['required', 'integer'],
                'file_extension' => ['required', 'string'],
                'file' => ['required', 'file'],
            ])->setException(config('chunkable.payload.exception'))->validate();

            $originalFile = $request->file('file');
            $originalFile->move($originalFile->getPath(), $originalFile->getBasename() . "." . ChunkedFile::CHUNKED_FILE_EXTENSION);
            $parts = intval(ceil($request->file_size / $request->chunk_size)) - 1;
            $currentPart = intval(floor($request->file_offset / $request->chunk_size));

            $chunked = new ChunkedFile(
                $request->file_unique_identifier, $currentPart, $parts,
                $originalFile->getPath(), $originalFile->getFilename(),
                $storage->getDisk()
            );

            $chunked = $storage->storageChunkedFile($chunked);

            $paths = $this->rememberUploadedParts($chunked, $currentPart);
            if (count($paths)-1 == $parts) {
                $unchunked = $storage->joinChunkedFiles($paths, $chunked->getFileUniqueIdentifier(), $request->file_extension);
                $this->forgetUploadedParts($chunked->getFileUniqueIdentifier());
            }

            return new ChunkUploadRequest($request, $chunked, $unchunked ?? null);
        });
    }

    private function rememberUploadedParts(ChunkedFile $file, int $currentPart): array
    {
        Log::debug(json_encode(['file' => $file->getFilename(), 'part' => $file->getChunkPart(), 'parts' => $file->getParts()]));
        $uploadedParts = Cache::get("{$file->getFileUniqueIdentifier()}.uploaded_parts") ?? array();
        $uploadedParts[$currentPart] = $file->getFullPath();
        Cache::set("{$file->getFileUniqueIdentifier()}.uploaded_parts", $uploadedParts);
        return $uploadedParts;
    }

    private function forgetUploadedParts($fileUniqueIdentifier): bool
    {
        return Cache::forget("$fileUniqueIdentifier.uploaded_parts");
    }
}
