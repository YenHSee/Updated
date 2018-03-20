<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $remark
 * @property string $security_question
 * @property string $security_answer
 * @property string $last_logging
 * @property string $created_at
 * @property string $updated_at
 * @property int $is_deleted
 *
 * @property Account[] $accounts
 * @property Transaction[] $transactions
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $auth_key;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    // public function rules()
    // {
    //     return [
    //         [['email'], 'required'],
    //         [['last_logging', 'created_at', 'updated_at'], 'safe'],
    //         [['username', 'password', 'role', 'name', 'email', 'status', 'remark', 'security_question', 'security_answer'], 'string', 'max' => 255],
    //         [['is_deleted'], 'string', 'max' => 1],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'role' => 'Role',
            'name' => 'Name',
            'email' => 'Email',
            'status' => 'Status',
            'remark' => 'Remark',
            'security_question' => 'Security Question',
            'security_answer' => 'Security Answer',
            'last_logging' => 'Last Logging',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['user_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
          return static::findOne(['access_token' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findByPasswordResetToken($token)
    {
        $expire = \Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    { 
        // $this->password === crypt($password, 'DontTry') ? true : false;
        if ($this->password === crypt($password, 'DontTry')) {
            return true;
        }
        else {
            return false;
        }
    }

    public function validateStatus($status)
    {
        // $this->status === 'Activated' ? true : false;

        if ($this->status === 'Activated') {
            return true;
        }
        else {
            return false;
        }
    }

    public function setPassword($password)
    {
        $this->password_hash = Security::generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Security::generateRandomKey();
    }



}

