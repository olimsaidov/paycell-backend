<?php

class UserIdentity extends CUserIdentity
{

    const ERROR_USER_DISABLED = 25;

    protected $_id;

    public function authenticate()
    {
        $user = User::model()->find('username = :username AND role != :role', array(':username' => $this->username, ':role' => User::ROLE_TERMINAL));

        if (($user === null) || ($this->password !== $user->password)) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if (($user->enabled == false)) {
            $this->errorCode = self::ERROR_USER_DISABLED;
        } else {
            $this->_id = $user->id;
            $this->username = $user->username;
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId()
    {
        return $this->_id;
    }
}
