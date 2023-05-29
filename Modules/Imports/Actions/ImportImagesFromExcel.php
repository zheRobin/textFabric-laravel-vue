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
                $collectionItem = $collection->collectionItemByCoordinates($drawing->getCoordinates());

                if (is_null($collectionItem)) {
                    continue;
                }

                $zipReader = fopen($drawing->getPath(),'r');

                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader,1024);
                }

                fclose($zipReader);

                $collectionItem->putImageIntoCell(
                    $drawing->getCoordinates(),
                    $imageContents,
                    $drawing->getExtension()
                );
            }
        }
    }
}
