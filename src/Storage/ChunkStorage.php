<?php

namespace Posart\Chunkable\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\UnchunkedFile;

class ChunkStorage extends AbstractStorage
{
    protected Filesystem $disk;
    private const STORAGE_PREFIX = 'chunkable';

    public function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
    }

    public function storageChunkedFile(ChunkedFile $chunkedFile): ChunkedFile|bool
    {
        $diskPath = self::STORAGE_PREFIX . "/{$chunkedFile->getFileUniqueIdentifier()}";
        $diskFilename = "{$chunkedFile->getFileUniqueIdentifier()}_{$chunkedFile->getChunkPart()}_{$chunkedFile->getParts()}";
        $diskFullPath = $diskPath . '/' . $diskFilename . "." . ChunkedFile::CHUNKED_FILE_EXTENSION;
        if (!$this->disk->put($diskFullPath, fopen($chunkedFile->getFullPath(), 'r'))) return false;

        return new ChunkedFile(
            $chunkedFile->getFileUniqueIdentifier(),
            $chunkedFile->getChunkPart(), $chunkedFile->getParts(),
            $diskPath, $diskFilename,
            $this->disk
        );
    }

    public function joinChunkedFiles(array $paths, string $fileUniqueIdentifier, string $fileExtension): UnchunkedFile
    {
        $pathInfo = pathinfo($paths[0]);
        $fullPath = "{$pathInfo['dirname']}/$fileUniqueIdentifier.$fileExtension";
        foreach ($paths as $path) {
            $chunkContent = $this->disk->readStream($path);

            while ($buff = fread($chunkContent, 4096)) {
                if ($this->disk->exists($fullPath)) {
                    $this->disk->append($fullPath, $buff);
                } else {
                    $this->disk->put($fullPath, $buff);
                }
            }
        }

        $this->deleteChunkedFiles($paths);

        return new UnchunkedFile(
            $pathInfo['dirname'],
            $pathInfo['filename'],
            $pathInfo['extension'],
            $this->disk
        );
    }

    public function deleteChunkedFiles(array $unchunkedPaths): bool
    {
        Log::debug($unchunkedPaths);
        return $this->disk->delete($unchunkedPaths);
    }
}
