<?php

namespace backend\utils;

use yii\base\BaseObject;

/**
 * Class Response
 * @package api\modules\v2\components
 *
 * @author  Haqqi <me@haqqi.net>
 *
 * Basic response class for API version 2
 */
class Response extends BaseObject
{
    /**
     * @var string Response name
     */
    public $name;

    /**
     * @var string Response message
     */
    public $message;

    /**
     * @var integer Response code, based on ApiCode
     */
    public $code;

    /**
     * @var integer Http status code
     */
    public $status;
    
    /**
     * @var string $requestTime Time request of the action
     */
    public $requestTime;

    /**
     * @var mixed array for saving the data
     */
    public $data;
    

    public function validate()
    {
        if ($this->name === null) {
            return 'Response::$name cannot be null';
        }
        if ($this->message === null) {
            return 'Response::$message cannot be null';
        }
        if ($this->code === null) {
            return 'Response::$code cannot be null';
        }
        if ($this->status === null) {
            return 'Response::$status cannot be null';
        }
        
        if(YII_ENV == YII_ENV_DEV || YII_ENV == YII_ENV_TEST) {
            $this->requestTime = \Yii::getLogger()->getElapsedTime() * 1000;
            $this->requestTime .= ' ms';
        }

        return true;
    }
}
