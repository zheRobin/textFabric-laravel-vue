<?php
namespace Modules\Export\Traits;

trait CustomBatchable
{
    public $batchId;

    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;
        return $this;
    }

    public function getBatchId()
    {
        return $this->batchId;
    }
}
