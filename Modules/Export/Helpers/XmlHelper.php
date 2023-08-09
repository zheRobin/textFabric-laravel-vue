<?php

namespace Modules\Export\Helpers;

class XmlHelper
{
    public static function arrayToXml(array $data, $xml = null)
    {
        if ($xml === null) {
            $xml = new \SimpleXMLElement('<dataset/>');
        }

        foreach ($data as $key => $value) {
            $record = $xml->addChild('record');
            foreach ($value as $index => $item) {
                $record->addChild(str_replace(' ', '_', $index), $item);
            }

        }

        return $xml->asXML();
    }
}
