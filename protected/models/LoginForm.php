<?php

class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $verifyCode;

    public function rules()
    {
        return array(
            array('username, password', 'required'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
        );
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $identity = new UserIdentity($this->username, $this->password);
            $identity->authenticate();
            switch ($identity->errorCode) {
                case UserIdentity::ERROR_NONE:
                    $duration = 60 * 60 * 5;
                    Yii::app()->user->login($identity, $duration);
                    break;
                case UserIdentity::ERROR_USER_DISABLED:
                    $this->addError(null, 'Пользовател отключен от системы');
                    break;
                default:
                    $this->addError(null, 'Имя пользователя или пароль не правилный');
                    break;
            }
        }
    }
}
