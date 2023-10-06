<?php 

namespace backend\utils;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use yii\base\BaseObject;

class ErrorFormatter extends BaseObject
{
    public static function flat($errors)
    {
        $iter = new RecursiveIteratorIterator(new RecursiveArrayIterator($errors));
        $new_arr = [];
        foreach($iter as $v) {
            $new_arr[]=$v;
        }

        return $new_arr;
    }
    
}
