<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $login
 * @property string $pass
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public $username;
    public $password;
    public $rememberMe = true;
    
    private $_user = false;
    
    private static $users; 
            
    public static function tableName()
    {
        return 'user';
    }
    
    public static function getAllUsers()
    {
        self::$users = self::find()
            ->all();
    }

    public static function findIdentity($id)
    {
        self::getAllUsers();
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }
    
    public static function findIdentityByAccessToken($token, $type = null)
    {
        self::getAllUsers();
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getAuthKey()
    {
        return $this->authKey;
    }
    
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'pass'], 'required'],
            ['rememberMe', 'boolean'],
            [['login'], 'string', 'max' => 50],
            [['pass'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'pass' => 'Pass',
        ];
    }
    
    public function login()
    {
        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->login);
        }

        return $this->_user;
    }
    
    public static function findByUsername($login)
    {
        self::getAllUsers();
        foreach (self::$users as $user) {
            if (strcasecmp($user['login'], $login) === 0) {
                return new static($user);
            }
        }

        return null;
    }
}
