<?php

namespace Arturbruno17\LaravelChunkUpload\Exceptions;

use Illuminate\Validation\ValidationException;
use Throwable;

class ChunkUploadPayloadException extends ValidationException
{
    public $status = 400;
}
