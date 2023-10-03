<?php

namespace backend\models;

use backend\utils\ReservedCurrency;
use PDO;
use Ulid\Ulid;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\VarDumper;

class UserTransactionModel extends Model
{
    public $amount, $current_price, $currency_source, $currency_target;
    public $type;
    private $_userId = null;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->_userId = Yii::$app->user->identity->getId();
    }

    const SCENARIO_SELL = 'scenarioSell';

    public function rules()
    {
        return [
            [['amount', 'current_price'], 'double'],
            [['currency_source', 'currency_target'], 'in', 'range' => ReservedCurrency::getList()],
            ['type', 'in', 'range' => ['buy', 'sell']],
            ['amount', 'validateAmount', 'on' => self::SCENARIO_SELL]
        ];
    }

    public function validateAmount($attribute, $params)
    {
        $balanceQuery = (new Query())->select(['id', 'amount', 'currency'])
            ->from('balance')
            ->where(['user_id' => $this->_userId])
            ->andWhere(['currency' => $this->currency_source])
            ->one();

        if (empty($balanceQuery)){
            $this->addError('amount', 'insufficient balance');
        } else {
            $amount = (double) $balanceQuery['amount'] - (double) $this->amount;

            if ($amount < 0){
                $this->addError('amount', 'insufficient balance');
            }
        }
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SELL] = [
            'amount',
            'currency_source',
            'currency_target',
            'type',
            'current_price'
        ];
        return $scenarios;
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $ulid = $this->getIdentifier();

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $user_transaction = $this->writeTransaction($db, $ulid);
        if (!$user_transaction){
            return false;
        }

        $writeActivity = $this->writeActivity($db, $ulid);
        if (!$writeActivity){
            return false;
        }

        $calculateBalance = $this->calculateBalance($db);
        if (!$calculateBalance){
            return false;
        }

        try {
            $transaction->commit();
        } catch (Exception $exception){
            $transaction->rollBack();
            $this->addError('amount', 'Problem writing in database');
            return false;
        }

        return true;
    }

    private function calculateBalance($db)
    {
        $balanceQuery = (new Query())->select(['id', 'amount', 'currency'])
            ->from('balance')
            ->where(['user_id' => $this->_userId])
            ->andWhere(['currency' => $this->type == 'sell' ?$this->currency_source : $this->currency_target])
            ->one();

        $amount = $this->amount;

        if (empty($balanceQuery)){
            if ($this->type == 'sell'){
                $this->addError('amount', 'insuffience balance');
                return false;
            }

            $statement = "INSERT INTO balance (user_id, amount, currency, created_at, updated_at) values (:user_id,:amount, :currency,now() ,now())";
            $rawQuery = $db->createCommand($statement);
            $rawQuery->bindParam(':user_id', $this->_userId, PDO::PARAM_STR);
            $rawQuery->bindParam(':currency', $this->currency_target, PDO::PARAM_STR);
        } else {
            $amount = match($this->type){
                'buy' => (double) $balanceQuery['amount'] + (double) $this->amount,
                'sell' => (double) $balanceQuery['amount'] - (double) $this->amount,
            };
            $statement = "UPDATE balance set amount = :amount where id = :id";
            $rawQuery = $db->createCommand($statement);
            $rawQuery->bindParam(':id', $balanceQuery['id'], PDO::PARAM_STR);
        }

        $rawQuery->bindParam(':amount', $amount, PDO::PARAM_STR);
        $user_activity = $rawQuery->execute();

        if ($user_activity <= 0){
            $this->addError('amount', 'failed to write');
            return false;
        }

        return true;
    }

    private function writeActivity($db, $ulid)
    {
        $createdTransaction = (new Query())->select(['id'])
            ->from('user_transaction')
            ->where(['identifier' => $ulid])
            ->one();
        $user = Yii::$app->user->identity;
        $message = "create {$this->type} transaction";

        $statement = "INSERT INTO activity (user_id, user_transaction_id, message, created_at, updated_at) values (:user_id,:user_transaction_id, :message,now() ,now())";
        $rawQuery = $db->createCommand($statement);
        $rawQuery->bindParam(':user_id', $this->_userId, PDO::PARAM_STR);
        $rawQuery->bindParam(':user_transaction_id', $createdTransaction['id'], PDO::PARAM_STR);
        $rawQuery->bindParam(':message', $message, PDO::PARAM_STR);
        $user_activity = $rawQuery->execute();

        if ($user_activity <= 0){
            $this->addError('amount', 'failed to write');
            return false;
        }
        return true;
    }

    private function writeTransaction($db, $ulid)
    {
        $statement = "INSERT INTO user_transaction (identifier, amount, current_price,currency_source,currency_target,type,status, created_at, updated_at) values (:identifier,:amount, :current_price,:currency_source,:currency_target,:type,'complete',now() ,now())";
        $rawQuery = $db->createCommand($statement);
        $rawQuery->bindParam(':identifier', $ulid, PDO::PARAM_STR);
        $rawQuery->bindParam(':amount', $this->amount, PDO::PARAM_STR);
        $rawQuery->bindParam(':current_price', $this->current_price, PDO::PARAM_STR);
        $rawQuery->bindParam(':currency_source', $this->currency_source, PDO::PARAM_STR);
        $rawQuery->bindParam(':currency_target', $this->currency_target, PDO::PARAM_STR);
        $rawQuery->bindParam(':type', $this->type, PDO::PARAM_STR);
        // append to transaction stack, not executing in mysql.
        $user_transaction = $rawQuery->execute();

        if ($user_transaction <= 0){
            $this->addError('amount', 'failed to write');
            return false;
        }

        return true;

    }

    private function getIdentifier()
    {
        $ulid = null;

        while (true):
            $ulid = Ulid::generate();
            $existingTransaction = (new Query())->select(['identifier'])
                ->from('user_transaction')
                ->where(['identifier' => $ulid])
                ->count();
            if ((int)$existingTransaction < 1) {
                break;
            }
        endwhile;
        return $ulid;

    }


}


