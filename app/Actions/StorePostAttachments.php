<?php

namespace App\Actions;

use App\Models\Post;
use App\Models\PostAttachment;
use App\Services\LocalDocumentStorage;
use Illuminate\Http\UploadedFile;

class StorePostAttachments
{
    public function __construct(private LocalDocumentStorage $storage) {}

    /**
     * @param  list<UploadedFile>  $files
     */
    public function handle(Post $post, array $files): void
    {
        foreach ($files as $file) {
            PostAttachment::create([
                'post_id' => $post->id,
                'path' => $this->storage->store($file, 'post-attachments'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                'size' => $file->getSize() ?? 0,
            ]);
        }
    }
}
