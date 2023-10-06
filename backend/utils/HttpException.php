<?php

namespace backend\utils;

class HttpException extends \yii\web\HttpException
{
    private $_data;

    public function __construct($status, $message = null, $data = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($status, $message, $code, $previous);

        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }
}