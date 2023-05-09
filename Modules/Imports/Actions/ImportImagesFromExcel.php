<?php

namespace Modules\Imports\Actions;

use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\ImportsImagesFromExcel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ImportImagesFromExcel implements ImportsImagesFromExcel
{
    public function import(Collection $collection): void
    {
        $spreadsheet = IOFactory::load($collection->importFilePath());

        $i = 0;
        foreach ($spreadsheet->getActiveSheet()->getDrawingCollection() as $drawing) {
            if ($drawing instanceof Drawing &&
                $drawing->getPath() &&
                $drawing->getCoordinates()) {
                $collectionItem = $this->collectionItemByCoordinates($collection, $drawing->getCoordinates());

                if (is_null($collectionItem)) {
                    continue;
                }

                $zipReader = fopen($drawing->getPath(),'r');

                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }

                fclose($zipReader);

                $collectionItem->updateCellWithImage(
                    $drawing->getCoordinates(),
                    $imageContents,
                    $drawing->getExtension()
                );
            }
        }
    }

    protected function collectionItemByCoordinates(Collection $collection, string $coordinates)
    {
        return $collection->items()
            ->whereJsonContains('data', ['external_identifier' => $coordinates])
            ->first();
    }
}
