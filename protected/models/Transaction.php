<?php


class Transaction extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'transactions';
    }

    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'terminal'),
        );
    }
}
