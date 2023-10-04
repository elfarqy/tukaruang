<?php

namespace backend\controllers;

use backend\models\LoginModel;
use backend\models\UserTransactionModel;
use backend\utils\ErrorFormatter;
use backend\utils\HttpException;
use backend\utils\Response;
use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\rest\Controller;

class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];

        $behaviors['authenticator'] = [
            'class'       => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class
            ],
            'except' => [
                'login'
            ]
        ];


        return $behaviors;
    }

//    protected function verbs()
//    {
//        return [
//            'login' => ['post'],
//            'buy'   => ['post'],
//            'sell'   => ['post'],
//        ];
//    }

    public function actionLogin()
    {
        try {
            $loginForm        = new LoginForm();
        } catch (InvalidConfigException $e) {
            throw new HttpException(400, [$e->getMessage()]);
        }

        // load the submitted data
        $loginForm->load(\Yii::$app->request->post(), '');

        if ($loginForm->login()) {
            $user = \Yii::$app->user->identity;

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = 'Login to application success';
            $response->code    = '001';
            $response->status  = 200;
            $response->data    = [
                'user' => ArrayHelper::toArray($user, [
                    User::class        => [
                        'username',
                        'email',
                        'token' => function ($model) {
                            return $model->auth_key;
                        }
                    ],
                ])
            ];

            return $response;
        }

        $errMessage = implode(",", ErrorFormatter::flat($loginForm->errors));

        throw new HttpException(400, "Failed to Login: {$errMessage}");

    }

    public function actionBuy()
    {
        $model = new UserTransactionModel();
        $request = Yii::$app->request;
        $model->load($request->post(), '');
        $model->type = 'buy';

        if ($model->save()){

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = 'Performing buy success';
            $response->code    = '001';
            $response->status  = 200;
            $response->data    = [];

            return $response;

        }

        $errMessage = implode(",", ErrorFormatter::flat($model->errors));

        throw new HttpException(400, "Failed to Buy: {$errMessage}");
    }

    public function actionSell()
    {
        $model = new UserTransactionModel();
        $model->setScenario(UserTransactionModel::SCENARIO_SELL);
        $request = Yii::$app->request;
        $model->load($request->post(), '');
        $model->type = 'sell';

        if ($model->save()){

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = 'Performing sell success';
            $response->code    = '001';
            $response->status  = 200;
            $response->data    = [];

            return $response;

        }

        $errMessage = implode(",", ErrorFormatter::flat($model->errors));

        throw new HttpException(400, "Failed to sell: {$errMessage}");

    }

    public function actionSummary()
    {
        $query = (new Query())->select(['user_id', 'user_transaction_id', 'message', 'created_at'])->from('activity')
            ->where(['is not', 'user_transaction_id', null]);

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'defaultPageSize' => 10]);

        $objects = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $pageCount = $pagination->pageCount;
        $currentPage = $pagination->page + 1;

        $response          = new Response();
        $response->name    = 'Success';
        $response->message = 'Fetch Summary Success';
        $response->code    = '001';
        $response->status  = 200;
        $response->data    = [
            'total_page'  => $pageCount,
            'lastPage' => $pageCount,
            'nextPage'    => $currentPage < $pageCount ? $currentPage + 1 :  $pageCount,
            'currentPage' => $currentPage,
            'prevPage'    => $currentPage >= 2 ? $currentPage - 1 : $currentPage,
            'results'     => ArrayHelper::toArray($objects),
        ];

        return $response;


    }

}