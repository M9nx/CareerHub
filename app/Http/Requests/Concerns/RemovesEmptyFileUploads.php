<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Http\UploadedFile;

trait RemovesEmptyFileUploads
{
    /**
     * Drop browser-submitted empty file inputs so nullable File rules do not fail.
     *
     * @param  list<string>  $fields
     */
    protected function removeEmptyFileUploads(array $fields): void
    {
        foreach ($fields as $field) {
            $file = $this->files->get($field);

            if ($file instanceof UploadedFile && $file->getError() === UPLOAD_ERR_NO_FILE) {
                $this->files->remove($field);
            }
        }

        $this->convertedFiles = null;
    }

    /**
     * @param  list<string>  $fields
     */
    protected function removeEmptyFileUploadArrays(array $fields): void
    {
        foreach ($fields as $field) {
            $files = $this->files->get($field);

            if (! is_array($files)) {
                continue;
            }

            $kept = array_values(array_filter(
                $files,
                fn (mixed $file): bool => $file instanceof UploadedFile
                    && $file->getError() !== UPLOAD_ERR_NO_FILE
            ));

            if ($kept === []) {
                $this->files->remove($field);

                continue;
            }

            $this->files->set($field, $kept);
        }

        $this->convertedFiles = null;
    }
}
