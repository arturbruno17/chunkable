<?php

namespace Posart\Chunkable\Storage;

use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\UnchunkedFile;

interface AbstractStorage
{
    public function storageChunkedFile(ChunkedFile $chunkedFile): bool;
    public function joinChunkedFiles(array $files): UnchunkedFile;
    public function deleteChunkedFiles(UnchunkedFile $unchunkedFile): bool;
}