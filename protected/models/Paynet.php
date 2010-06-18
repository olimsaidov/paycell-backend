<?php

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $terminal
 */
class Paynet extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'paynets';
    }

    public function relations()
    {
        return array(
            'distributor' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function rules()
    {
        return array(
            array('username, password, terminal, user_id, percent', 'required'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'terminal' => 'Терминал',
            'user_id' => 'Дистрибютор',
            'enabled' => 'Состояние',
            'percent' => 'Процент',
        );
    }
}

