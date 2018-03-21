<?php

namespace app\forms\TransferForms;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Exception;
use app\models\User;
use app\models\Account;
use app\models\Transaction;
use yii\data\ActiveDataProvider;

class TransferForm extends Model
{
	public $transfer_id;
	public $user_id;
    public $to_account;
    public $from_account;
    public $name;
    public $available_balance;
    public $amount;
    public $last_balance;
    public $status;
    public $details;
    public $remark;
    public $pinNumber;
    public $email;
    public $is_deleted;
    public $updated_at;
    public $created_at;

	public function rules()
    {
        return [
            [['to_account', 'from_account', 'amount', 'details'], 'safe'],
            ['amount', 'number', 'min'=>0],
            ['to_account', 'compare', 'compareAttribute' => 'from_account', 'operator' => '!==', 'message'=>'tak boleh sama'],
        ];
    }

    public function pendingSender()
    {
	 	$verifyCode = Yii::$app->security->generateRandomString(6);
    	if (!$this->validate())
    	{
    		throw new  Exception("Error Processing Request Due to Violation of Rules", 1);	
    	}
    	else
    	{
    		$db = Yii::$app->db->beginTransaction();
    		try
    		{
    			$sender = new Transaction();
    			$sender->user_id = Yii::$app->user->identity->id;
    			$sender->to_account = $this->to_account;
    			$sender->from_account = $this->from_account;
    			$sender->amount = $this->amount;
    			$sender->last_balance = $this->getLastBalance($sender->user_id);
    			$sender->status = 'OUT';
    			$sender->details = $this->details;
    			$sender->remark = 'Pending';
    			$sender->verify_code = $verifyCode;
    			$sender->created_at = date('Y-m-d H:i:s');
    			if (!$sender->save())
    			{
    				throw new Exception(current($sender->getFirstErrors()));
    			}
    			else
            	{
            		$db->commit();
            	}    			
    		} catch (Exception $e) {
    			$db->rollback();
    			throw new Exception($e,1);
    		}
    	}
    }    

    public function validateTransfer()
    {
    	$POST_VARIABLE=Yii::$app->request->post('TransferForm');
    	$request = $POST_VARIABLE['pinNumber']; 
    	$account = Account::find()
                    ->where(['user_id' => Yii::$app->user->identity->id])
                    ->one();
    	$match = Transaction::find()
    				->where(['verify_code' => $request])
    				->one();
    	if(!$match)
    	{
    		throw new Exception("Wrong Pin Number",1);
    	}
    	else
    	{
    		$db = Yii::$app->db->beginTransaction();
    		try
    		{
    			$match->remark = 'Success';
    			if (!$match->save())
    			{
    				throw new Exception(current($match->getFirstErrors()));
    			}
    			else
            	{
            		$db->commit();
            		$this->successReceived();
            		$this->changeAccountDetails($account->account_number, $match->to_account, $match->amount);
            	}    			
    		} catch (Exception $e) {
    			$db->rollback();
    			throw new Exception($e,1);
    		}
    	}
    }

    public function successReceived()
    {
    	$db = Yii::$app->db->beginTransaction();
    	try
    	{
    		$receiver = new Transaction();
            $receiver->user_id = $this->user_id;
            $receiver->from_account = $this->from_account; 
            $receiver->to_account = $this->to_account;
            $receiver->amount = $this->amount;
            $receiver->last_balance = $this->getLastBalance($receiver->user_id);
            $receiver->status = 'IN';
            $receiver->details = $this->details;
            $receiver->remark = 'Success';
            $receiver->created_at = date('Y-m-d H:i:s');
            if(!$receiver->save())
            {
            	throw new Exception(current($receiver->getFirstErrors()));
            }
            else
            {
            	$db->commit();
            }
        }catch (Exception $e) {
        	$db->rollback();
    		throw new Exception($e, 1);    		
    	}
    }

    public function changeAccountDetails($senderAccount, $receiverAccount, $transactionAmount)
    {
        $db = Yii::$app->db->beginTransaction();
            try 
            {   
                $sender = Account::find()
                            ->where(['account_number' => $senderAccount])
                            ->one();

                $sender->available_balance = ($sender->available_balance - $transactionAmount);
                $sender->current_balance = $sender->available_balance + 20;
                if(!$sender->save())
                {
                    throw new Exception(current($sender->getFirstErrors()));
                }
                else
                {
                    $receiver = Account::find()
                    ->where(['account_number' => $receiverAccount])
                    ->one();
                    $receiver->available_balance = ($receiver->available_balance + $transactionAmount);
                    $receiver->current_balance = $receiver->available_balance + 20;
                    // throw new Exception(var_export($receiver->current_balance,1));
                    if(!$receiver->save())
                    {
                        throw new Exception(current($receiver->getFirstErrors()));
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

    public function validateBalance($available_balance, $amount)
    {
    	if ($amount > ($available_balance))
    	{
    		throw new Exception("Sorry Your Balance Is Not Enough");
    	}
    	else
    	{
    		return true;
    	}
    }

    public function findReceiver($accountNo)
    {
    	$account = Account::find()
                ->where(['account_number' => $accountNo])
                ->one();

         if (!$account)
         {
         	throw new Exception("Sorry Account Not Found");
         }
         else
         {
         	$user = User::find()
        		->where(['id' => $account->user_id])
        		->one();
        		$this->name = $user->name;
        		$this->user_id = $account->user_id;
        		// throw new Exception(var_export($this->user_id,1));
        		return $user;
         }

         // return [
         // 	'user' => $user,
         // 	'account' => $account->accountname,
         // ];	

         // date['user']['username']
    }

    public function getLastBalance($user_id)
    {
        $balance = Account::find()
                    ->select('available_balance')
                    ->where(['user_id' => $user_id])
                    ->one();

        return $this->available_balance = $balance->available_balance;
    }

    public function getAccount($user_id)
    {
        $account = Account::find()
                    ->where(['user_id' => $user_id])
                    ->one();
        $this->from_account = $account->account_number;
        $this->available_balance = $account->available_balance;
        return $account;
    }
}