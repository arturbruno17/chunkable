<?php

namespace Posart\Chunkable\Requests;

use Illuminate\Http\Request;
use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\UnchunkedFile;

class ChunkUploadRequest
{
    public Request $request;
    public ChunkedFile $chunk;
    public ?UnchunkedFile $finalFile;

    public function __construct(Request $request, ChunkedFile $chunk, ?UnchunkedFile $finalFile)
    {
        $this->request = $request;
        $this->chunk = $chunk;
        $this->finalFile = $finalFile;
    }

}
