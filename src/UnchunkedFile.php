<?php

namespace Posart\Chunkable;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\File;

class UnchunkedFile extends File
{
    private string $path;
    private string $filename;
    private string $extension;
    private Filesystem $disk;
    private array $chunkedFiles;

    public function __construct(string $path, string $filename, string $extension, Filesystem $disk, array $chunkedFiles)
    {
        parent::__construct($path . "/{{$filename}.{$extension}", false);
        $this->path = $path;
        $this->filename = $filename;
        $this->extension = $extension;
        $this->disk = $disk;
        $this->chunkedFiles = $chunkedFiles;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getFullPath(): string
    {
        return "{$this->getPath()}/{$this->getFilename()}.{$this->getExtension()}}";
    }

    public function getDisk(): Filesystem
    {
        return $this->disk;
    }

    public function getChunkedFiles(): array
    {
        return $this->chunkedFiles;
    }
}