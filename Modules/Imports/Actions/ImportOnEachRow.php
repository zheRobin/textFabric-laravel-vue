<?php

namespace Modules\Imports\Actions;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Modules\Collections\Models\Collection;
use Modules\Imports\Models\CollectionItem;

class ImportOnEachRow implements OnEachRow, WithHeadingRow, WithEvents
{
    use Importable, RegistersEventListeners;

    public function __construct(
        protected array $columns,
        protected Collection $collection,
    ) {
    }

    public function onRow(Row $row)
    {
        $collectionItem = new CollectionItem();
        $collectionItemFields = [];

        $i = 0;
        foreach ($row->getCellIterator() as $cell) {
            if (isset($this->columns[$i])) {
                $collectionItemFields[] = [
                    'header' => $this->columns[$i],
                    'value' => $cell->getValue(),
                    'external_identifier' => $cell->getCoordinate(),
                ];
            }

            $i++;
        }

        $collectionItem->data = $collectionItemFields;
        $this->collection->items()->save($collectionItem);
    }
}
