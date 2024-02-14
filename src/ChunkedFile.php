<?php

namespace Posart\Chunkable;


use Illuminate\Contracts\Filesystem\Filesystem;

class ChunkedFile
{
    const CHUNKED_FILE_EXTENSION = 'chunk';

    private string $fileUniqueIdentifier;
    private int $chunkPart;
    private int $parts;
    private string $path;
    private string $filename;
    private Filesystem $disk;

    public function __construct(string $fileUniqueIdentifier, int $chunkPart, int $parts, string $path, string $filename, Filesystem $disk)
    {
        $this->fileUniqueIdentifier = $fileUniqueIdentifier;
        $this->chunkPart = $chunkPart;
        $this->parts = $parts;
        $this->path = $path;
        $this->filename = $filename;
        $this->disk = $disk;
    }

    public function getFileUniqueIdentifier(): string
    {
        return $this->fileUniqueIdentifier;
    }

    public function getChunkPart(): int
    {
        return $this->chunkPart;
    }

    public function getParts(): int
    {
        return $this->parts;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getFullPath(): string
    {
        return "{$this->getPath()}/{$this->getFilename()}." . self::CHUNKED_FILE_EXTENSION;
    }

    public function getDisk(): Filesystem
    {
        return $this->disk;
    }
}