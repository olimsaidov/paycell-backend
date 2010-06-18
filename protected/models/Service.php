<?php

class Service extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'services';
    }

    public function rules()
    {
        return array(
            array('id, gateway', 'required'),
            array('gateway', 'in', 'range' => array('Paynet', 'Samonline', 'Off')),
            array('id', 'in', 'range' => array(1, 2, 3, 4, 5)),
        );
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Имя сервиса',
            'gateway' => 'Шлюз'
        );
    }

    protected function getHumanName()
    {
        $names = array(
            1 => 'МТС',
            2 => 'Beeline',
            3 => 'UCell',
            4 => 'Perfectum Mobile',
            5 => 'UZMobile',
        );
        return $names[$this->id];
    }

}
