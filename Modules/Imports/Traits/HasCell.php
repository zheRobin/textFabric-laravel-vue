<?php

namespace Modules\Imports\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasCell
{
    /**
     * @param string $externalCoordinates
     * @param $imageContent
     * @param string $extension
     * @return void
     */
    public function putImageIntoCell(string $externalCoordinates, $imageContent, string $extension): void
    {
        $this->load('collection');
        $cells = [];

        foreach ($this->data as $cell) {
            if (isset($cell['external_identifier']) &&
                $cell['external_identifier'] === $externalCoordinates) {

                $imagePath = $this->imagePath($extension);

                if ($this->uploadImage($imagePath, $imageContent)) {
                    $cell['path'] = $imagePath;
                }
            }
            $cells[] = $cell;
        }

        $this->forceFill(['data' => $cells])->save();
    }

    /**
     * @param string $filePath
     * @param $imageContent
     * @return bool
     */
    protected function uploadImage(string $filePath, $imageContent): bool
    {
        return Storage::disk($this->collection->importFileDisk())
            ->put($filePath, $imageContent);
    }

    /**
     * @param UploadedFile $image
     * @return string
     */
    public function putImage(UploadedFile $image): string
    {
        return Storage::disk($this->collection->importFileDisk())
            ->putFile($this->imageDirectory(), $image);
    }

    /**
     * @param UploadedFile $image
     * @return bool
     */
    public function replaceByImageReference(UploadedFile $image): bool
    {
        if (empty($this->data) && !is_array($this->data)) {
            return false;
        }

        $imageHeaders = array_column($this->collection->imageHeaders(), 'name');

        $cells = [];

        foreach ($this->data as $cell) {
            if (in_array($cell['header'], $imageHeaders) &&
                isset($cell['value']) &&
                $cell['value'] === $image->getClientOriginalName()) {
                $cell['path'] = $this->putImage($image);
                unset($cell['value']);
            }
            $cells[] = $cell;
        }

        if ($cells !== $this->data) {
            $this->forceFill(['data' => $cells])->save();

            return true;
        }

        return false;
    }

    /**
     * @param string $extension
     * @return string
     */
    protected function imagePath(string $extension): string
    {
        $hashed = Str::random(40);

        return "{$this->imageDirectory()}/{$hashed}.{$extension}";
    }

    /**
     * @return string
     */
    protected function imageDirectory(): string
    {
        return "{$this->collection->importFileDirectory()}/images";
    }
}
