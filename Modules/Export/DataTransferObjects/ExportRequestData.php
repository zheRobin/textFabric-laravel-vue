<?php
namespace Modules\Export\DataTransferObjects;

class ExportRequestData
{
    public $compilations;

    public function __construct($compilations)
    {
        $this->compilations = $compilations;
    }
}
