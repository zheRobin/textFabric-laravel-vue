<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Row;
use Modules\Collections\Models\Collection;
use Modules\Imports\Models\CollectionItem;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Illuminate\Http\Request;

class ImportOnEachRow implements OnEachRow, WithHeadingRow, WithEvents
{
    use Importable;

    public function __construct(
        protected User $user,
        protected array $columns,
        protected Collection $collection
    ) {
    }

    public function onRow(Row $row)
    {
        if ($row->isEmpty()) {
            return;
        }

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

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $totalItems = $event->getReader()->getActiveSheet()->getHighestRow() + count($this->user->currentCollection?->items()->get()) - 1;
                $planSubscription = $this->user->currentTeam->planSubscription;

                if (!$planSubscription->featureAllowsValue(
                    SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT, $totalItems)
                    ) {
                    $this->collection->removeImportedFile();
                    throw ValidationException::withMessages([
                        'upload' => [__('import.validation.max-items', [
                            'number' => $planSubscription->getFeatureValue(
                                SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT)
                        ])],
                    ])->errorBag('importFile');
                }
            },
        ];
    }
}
