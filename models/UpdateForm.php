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
            ['account_number', 'safe'],
            ['current_balance', 'safe'],
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
            $db = Yii::$app->db->beginTransaction();
            try 
            {   
                $user = User::findOne($id);
                $user->name = $this->name;
                $user->email = $this->email;
                $user->status = $this->status;
                $user->updated_at = new Expression('NOW()');
                if (!$user->save()) {
                    throw new Exception(current($user->getFirstErrors()));
                }
                else {
                    // $db->commit();
                    $account = new Account();
                    $account->user_id = $user->id;
                    $account->account_number = $this->account_number;
                    $account->available_balance = $this->current_balance-20;
                    $account->current_balance = $this->current_balance;
                    $account->created_at = date('Y-m-d H:i:s');
                    $account->update_at = date('Y-m-d H:i:s');
                    $account->is_deleted = 0;
                    if(!$account->save())
                    {
                        throw new Exception(current($account->getFirstErrors()), 1);
                    }
                    else
                    {
                        $db->commit();
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