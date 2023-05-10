<?php

namespace Modules\Imports\Traits;

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
                    $cell['image_path'] = $imagePath;
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
     * @param string $name
     * @param $extension
     * @return string
     */
    protected function imagePath(string $extension): string
    {
        $hashed = Str::random(40);

        return "{$this->collection->importFileDirectory()}/images/{$hashed}.{$extension}";
    }
}
