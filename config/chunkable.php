<?php

use Posart\Chunkable\Exceptions\ChunkUploadPayloadException;

return [
    'storage' => [
        'disk' => 'local',
//        'pattern' => '{}'
    ],
    'payload' => [
        'exception' => ChunkUploadPayloadException::class
    ]
];