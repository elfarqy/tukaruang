<?php

namespace backend\utils;

use yii\base\BaseObject;

class ReservedCurrency extends BaseObject
{
    public static function getList()
    {
        return ['USD', 'IDR', 'JPY', 'SGD', 'MYR'];
    }

}