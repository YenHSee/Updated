<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use Exception;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\Transaction;
	
class SearchForm extends Model
{
	public $id;
	public $username;
	public $role;
    public $name;
    public $email;
    public $status;

    public function rules()
    {
        return [
            // ... more stuff here
            [['username', 'role', 'name', 'email', 'status'], 'safe'],
            // ... more stuff here
        ];
    }

	public function search($params)
	{
	    $query = User::find()
	    ->where(['is_deleted' => 0])
	    ->orderBy('id');

	   	 $dataProvider = new ActiveDataProvider([
	        'query' => $query,
	    ]);

	    $this->load($params);

	    $query->andFilterWhere(['like', 'username', $this->username ])
	        ->orFilterWhere(['like ', 'role', $this->username])
	        ->orFilterWhere(['like', 'name', $this->username])
	        ->orFilterWhere(['like', 'email', $this->username])
	        ->orFilterWhere(['like', 'status', $this->username]);

	    return new ActiveDataProvider([
	    	'query' => $query,
	    ]);
	}

	public function checkHistory($params)
	{
		$query = Transaction::find(['user_id' => $params]);
		$dataProvider = new ActiveDataProvider([
			'query' =>$query,
		]);
		$this->load($params);
	    $query->andFilterWhere(['like', 'to_account', $this->status ])
	        ->orFilterWhere(['like ', 'from_account', $this->status])
	        ->orFilterWhere(['like', 'amount', $this->status])
	        ->orFilterWhere(['like', 'last_balance', $this->status])
	        ->orFilterWhere(['like', 'status', $this->status])
	        ->orFilterWhere(['like', 'details', $this->status])
	        ->orFilterWhere(['like', 'remark', $this->status]);
		return new ActiveDataProvider([
			'query' => $query,
 		]);
	}

	// public function checkHistory1($id)
	// {
	// 	$query = Transaction::find(['user_id' => $id]);
	// 	$dataProvider = new ActiveDataProvider([
	// 		'query' =>$query,
	// 	]);
	// 	$this->load($id);
	//     $query->andFilterWhere(['like', 'to_account', $this->status ])
	//         ->orFilterWhere(['like ', 'from_account', $this->status])
	//         ->orFilterWhere(['like', 'amount', $this->status])
	//         ->orFilterWhere(['like', 'last_balance', $this->status])
	//         ->orFilterWhere(['like', 'status', $this->status])
	//         ->orFilterWhere(['like', 'details', $this->status])
	//         ->orFilterWhere(['like', 'remark', $this->status]);
	// 	return new ActiveDataProvider([
	// 		'query' => $query,
 // 		]);
	// }
}
          // $query = User::find()
          //   ->where(['is_deleted' => 0])
          //   ->orderBy('id');
          //   $dataProvider = new ActiveDataProvider([
          //       'query' => $query,
          //       ]);