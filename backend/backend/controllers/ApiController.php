<?php

namespace backend\controllers;

use backend\models\LoginModel;
use backend\models\UserTransactionModel;
use backend\utils\ErrorFormatter;
use backend\utils\HttpException;
use backend\utils\ReservedCurrency;
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
        $request = Yii::$app->request;

        $startDate = $request->get('start_date', null);
        $endDate   = $request->get('end_date', null);
        $startDate = ($startDate) ? \Yii::$app->formatter->asDate($startDate, 'php:Y-m-d') . ' 00:00:00' : null;
        $endDate = ($endDate) ? \Yii::$app->formatter->asDate($endDate, 'php:Y-m-d') . ' 23:59:59' : null;

        $userId = Yii::$app->user->identity->getId();
        $querySell = (new Query())
            ->select('sum(amount * current_price) as total, type, currency_source')
            ->from('user_transaction')
            ->leftJoin('activity', 'activity.user_transaction_id = user_transaction.id')
            ->where(['activity.user_id' => $userId])
            ->andWhere(['type' => 'sell'])
            ->andWhere(['in', 'currency_source', ReservedCurrency::getList()])
            ->andFilterWhere(['between', 'user_transaction.created_at', $startDate, $endDate])
            ->groupBy('currency_source, type')
            ->all();

        $queryBuy = (new Query())
            ->select('sum(amount * current_price) as total, type, currency_source')
            ->from('user_transaction')
            ->leftJoin('activity', 'activity.user_transaction_id = user_transaction.id')
            ->where(['activity.user_id' => $userId])
            ->andWhere(['type' => 'buy'])
            ->andWhere(['in', 'currency_source', ReservedCurrency::getList()])
            ->andFilterWhere(['between', 'user_transaction.created_at', $startDate, $endDate])
            ->groupBy('currency_source, type')
            ->all();
        $indexedSell = ArrayHelper::index($querySell, 'currency_source');
        $indexedBuy = ArrayHelper::index($queryBuy, 'currency_source');

        $responseData = [];

        foreach (ReservedCurrency::getList() as $value){
            $dataToSet =[
                'currency' => $value,
                'totalSell' => 0,
                'totalBuy' => 0,
            ];

            if (array_key_exists($value, $indexedSell)){
                $dataToSet['totalSell'] = (int) $indexedSell[$value]['total'];
            }


            if (array_key_exists($value, $indexedBuy)){
                $dataToSet['totalBuy'] = (int) $indexedBuy[$value]['total'];
            }

            $dataToSet['amount'] = $dataToSet['totalBuy'] - $dataToSet['totalSell'];

            $responseData[] = $dataToSet;

        }


        $response          = new Response();
        $response->name    = 'Success';
        $response->message = 'Fetch Summary Success';
        $response->code    = '001';
        $response->status  = 200;
        $response->data    = [
            'results'     => $responseData,
        ];

        return $response;


    }

}