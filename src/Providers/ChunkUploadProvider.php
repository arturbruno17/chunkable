<?php

namespace Arturbruno17\LaravelChunkUpload\Providers;

use Arturbruno17\LaravelChunkUpload\Requests\ChunkUploadRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ChunkUploadProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        //
    }

    public function register(): void
    {
        $this->app->bind(ChunkUploadProvider::class, function (Application $app) {
            /** @var Request $request */
            $request = $app->make('request');
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