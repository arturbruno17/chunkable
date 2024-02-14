<?php

namespace Posart\Chunkable;

use Illuminate\Contracts\Filesystem\Filesystem;

class UnchunkedFile
{
    private string $path;
    private string $filename;
    private string $extension;
    private Filesystem $disk;

    public function __construct(string $path, string $filename, string $extension, Filesystem $disk)
    {
        $this->path = $path;
        $this->filename = $filename;
        $this->extension = $extension;
        $this->disk = $disk;
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
        $filenameWithoutExtension = "{$this->getPath()}/{$this->getFilename()}";
        return $this->getExtension() ? $filenameWithoutExtension . ".{$this->getExtension()}" : $filenameWithoutExtension;
    }

    public function getDisk(): Filesystem
    {
        return $this->disk;
    }
}