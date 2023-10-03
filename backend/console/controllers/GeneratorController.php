<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class GeneratorController extends Controller
{
    public function actionData()
    {
        $user = 'admin@example.com';
        $password = '123456';

        $user = new User([
            'username' => $user,
            'auth_key' => null,
            'password_reset_token' => null,
            'email' => $user,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $user->setPassword($password);

        if(!$user->save()){
            var_dump($user->errors);
        }


    }

}