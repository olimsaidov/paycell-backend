<?php

class Samonline extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'samonlines';
    }

    public function rules()
    {
        return array(
            array('username, password', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
        );
    }
}
