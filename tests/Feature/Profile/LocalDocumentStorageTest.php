<?php

namespace Tests\Feature\Profile;

use App\Services\LocalDocumentStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocalDocumentStorageTest extends TestCase
{
    public function test_it_stores_file_on_public_disk(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'resume.pdf',
            100,
            'application/pdf'
        );

        $storage = app(LocalDocumentStorage::class);

        $path = $storage->store($file, 'cv');

        $this->assertTrue(
         Storage::disk('public')->exists($path)
       );
    }
}
