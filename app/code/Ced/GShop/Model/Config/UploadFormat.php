<?php


namespace Ced\GShop\Model\Config;


class UploadFormat
{
    public function toOptionArray()
    {
        return [
            [
                'label' => 'API Format',
                'value' => 'api'
            ],
            [
                'label' => 'CSV Format',
                'value' => 'csv'
            ]
        ];
    }

}
