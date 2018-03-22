<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Exception;


class UpdateForm extends Model
{
    public $name;
    public $email;
    public $status;
    public $account_number;
    public $current_balance;
    public $updated_at;

    protected $_user;

	public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Please Enter a Name'],
            ['email', 'email'],
            ['status', 'safe'],
            // ['account_number', 'safe'],
            // ['current_balance', 'safe'],
            // [['available_balance'], 'number'],
            // [['account_number'], 'number',],
            // [['available_balance'], 'required'],
            // [['account_number'], 'required',],
        ];
    }

    public function update($id)
    {
        if(!$this->validate()) 
        {
            throw new  Exception("Error Processing Request Due to Violation of Rules", 1);
        } 
        else
        {
            $user = User::findOne($id);
            $db = Yii::$app->db->beginTransaction();
            try 
            {   
                $user->name = $this->name;
                $user->email = $this->email;
                $user->status = $this->status;
                $user->is_deleted = 0;
                $user->updated_at = new Expression('NOW()');
                if (!$user->save()) {
                    throw new Exception(current($user->getFirstErrors()));
                }
                else {
                    $account = Account::findOne(['user_id' => $id]);
                    if ($account === null) {
                        $newAccount = new Account();
                        $newAccount->user_id = $user->id;
                        $newAccount->account_number = $this->account_number;
                        $newAccount->available_balance = $this->current_balance-20;
                        $newAccount->current_balance = $this->current_balance;
                        $newAccount->created_at = date('Y-m-d H:i:s');
                        $newAccount->update_at = date('Y-m-d H:i:s');
                        $newAccount->is_deleted = 0;
                        if (!$newAccount->save()) {
                            throw new Exception(current($newAccount->getFirstErrors()), 1);
                        } else {
                            $db->commit();
                            Yii::$app->response->redirect(['site/view']);
                        }
                    } else {
                        $account->user_id = $user->id;
                        $account->account_number = $this->account_number;
                        $account->available_balance = $this->current_balance-20;
                        $account->current_balance = $this->current_balance;
                        $account->created_at = date('Y-m-d H:i:s');
                        $account->update_at = date('Y-m-d H:i:s');
                        $account->is_deleted = 0;       
                        if(!$account->save()) {
                            throw new Exception(current($account->getFirstErrors()), 1);
                        } else {
                          $db->commit();
                        }
                    }
                }
            } catch (Exception $e) 
              {
                $db->rollback();
                throw new Exception($e, 1);
              }
        }
    }

    public function findAccount($id)
    {
        $model = Account::find()
                    ->where(['user_id' => $id])
                    ->one();

        if ($model === null)
        {
            $this->account_number = '';
            $this->current_balance = '';
        }
        else 
        {
              
            $this->account_number = $model->account_number;
            $this->current_balance = $model->current_balance;
        }
    }
    
    public function findUser($id)
    {
        if (($model = User::findOne($id)) !== null)
            {
                $this->name = $model->name;
                $this->email = $model->email;
                $this->status = $model->status;
            }
            return false;
    }

    public function randomGenerate()
    {
       $this->account_number = rand(100000000, 999999999);
       $this->current_balance = 1000;
    }
}