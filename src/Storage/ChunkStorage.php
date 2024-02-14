<?php

namespace Posart\Chunkable\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Posart\Chunkable\ChunkedFile;
use Posart\Chunkable\UnchunkedFile;

class ChunkStorage implements AbstractStorage
{
    private Filesystem $disk;

    public function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
    }


    public function storageChunkedFile(ChunkedFile $chunkedFile): bool
    {
        return $this->disk->put($chunkedFile->getPath(), $chunkedFile);
    }

    public function joinChunkedFiles(array $files): UnchunkedFile
    {
        /** @var ChunkedFile $firstFile */
        $firstFile = $files[0];
        $fullPath = "{$firstFile->getPath()}/{$firstFile->getFileUniqueIdentifier()}.{$firstFile->getExtension()}";
        $this->disk->put(
            $fullPath,
            ""
        );

        foreach ($files as /** @var ChunkedFile $file*/ $file) {
            $chunkContent = $this->disk->readStream($file->getFullPath());

            while ($buff = fread($chunkContent, 4096)) {
                $this->disk->append($fullPath, $buff);
            }
        }

        return new UnchunkedFile(
            $firstFile->getPath(),
            $firstFile->getFileUniqueIdentifier(),
            $firstFile->getExtension(),
            $this->disk,
            $files
        );
    }

    public function deleteChunkedFiles(UnchunkedFile $unchunkedFile): bool
    {
        $paths = array();
        foreach ($unchunkedFile->getChunkedFiles() as /** @var ChunkedFile $chunkedFile */ $chunkedFile) {
            $paths[] = "{$chunkedFile->getPath()}/{$chunkedFile->getFilename()}.{$chunkedFile->getExtension()}";
        }
        return $this->disk->delete($paths);
    }
}