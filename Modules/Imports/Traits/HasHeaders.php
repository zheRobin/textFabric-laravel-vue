<?php

namespace Modules\Imports\Traits;

use Modules\Imports\Enums\HeaderTypeEnum;

trait HasHeaders
{
    /**
     * @param string $name
     * @param HeaderTypeEnum $headerType
     * @return void
     */
    public function addHeader(string $name, HeaderTypeEnum $headerType): void
    {
        $this->forceFill([
            'headers' => [
                ...$this->headers ?? [],
                [
                    'name' => $name,
                    'type' => $headerType->slug()
                ]
            ]
        ])->save();
    }

    /**
     * @return bool
     */
    public function isImageCollection(): bool
    {
        return (count($this->headers) === 1) &&
            (current($this->imageHeaders())['type'] ?? false) === HeaderTypeEnum::IMAGE->slug();
    }

    /**
     * @return array
     */
    public function imageHeaders(): array
    {
        $imageHeaders = [];

        foreach ($this->headers as $header)
        {
            if ($header['type'] === HeaderTypeEnum::IMAGE->slug()) {
                $imageHeaders[] = $header;
            }
        }

        return $imageHeaders;
    }
}
