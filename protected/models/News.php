<?php

class News extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'news';
    }

    public function rules()
    {
        return array(
            array('title, text', 'required'),
        );
    }


    public function attributeLabels()
    {
        return array(
            'title' => 'Загаовок',
            'text' => 'Текст'
        );
    }
}

