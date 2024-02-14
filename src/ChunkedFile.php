<?php

namespace Posart\Chunkable;

use Illuminate\Http\File;
use League\Flysystem\Filesystem;

class ChunkedFile extends File
{
    private string $fileUniqueIdentifier;
    private string $path;
    private string $filename;
    private string $mimeType;
    private string $extension;
    private Filesystem $disk;

    public function __construct(string $fileUniqueIdentifier, string $path, string $filename, string $mimeType, string $extension, Filesystem $disk)
    {
        parent::__construct($path . "/{{$filename}.{$extension}", false);
        $this->fileUniqueIdentifier = $fileUniqueIdentifier;
        $this->path = $path;
        $this->filename = $filename;
        $this->mimeType = $mimeType;
        $this->extension = $extension;
        $this->disk = $disk;
    }

    public function getFileUniqueIdentifier(): string
    {
        return $this->fileUniqueIdentifier;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
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
}