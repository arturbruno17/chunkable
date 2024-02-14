<?php

namespace Posart\Chunkable\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use InvalidArgumentException;
use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\UnchunkedFile;

abstract class AbstractStorage
{
    protected Filesystem $disk;

    public function storageChunkedFile(ChunkedFile $chunkedFile): ChunkedFile|bool
    {
        throw new InvalidArgumentException();
    }

    public function joinChunkedFiles(array $paths, string $fileUniqueIdentifier, string $fileExtension): UnchunkedFile
    {
        throw new InvalidArgumentException();
    }

    public function deleteChunkedFiles(array $unchunkedPaths): bool
    {
        throw new InvalidArgumentException();
    }

    public function getDisk(): Filesystem
    {
        return $this->disk;
    }
}