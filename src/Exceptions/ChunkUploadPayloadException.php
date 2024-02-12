<?php

namespace Posart\Chunkable\Exceptions;

use Illuminate\Validation\ValidationException;

class ChunkUploadPayloadException extends ValidationException
{
    public $status = 400;
}
