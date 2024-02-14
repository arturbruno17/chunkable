<?php

namespace Posart\Chunkable\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Posart\Chunkable\Requests\ChunkUploadRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Posart\Chunkable\Storage\AbstractStorage;

class ChunkUploadProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $this->app->when(AbstractStorage::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk(config("chunkable.storage.disk"));
            });

        $this->app->bind(ChunkUploadProvider::class, function (Application $app) {
            /** @var Request $request */
            $request = $app->make('request');

            Validator::make($request->all(), [
                'file_unique_identifier' => ['required'],
                'chunk_size' => ['required', 'integer'],
                'file_offset' => ['required', 'integer'],
                'file_size' => ['required', 'integer'],
                'file' => ['required', 'file'],
            ])->setException(config('chunkable.payload.execption'))->validate();

            return new ChunkUploadRequest($request);
        });
    }

    public function provides(): array
    {
        return [
            ChunkUploadRequest::class
        ];
    }
}
