<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalDocumentStorage
{
    public function store(UploadedFile $file, string $directory): string
    {
        return Storage::disk('public')->putFile($directory, $file);
    }
}