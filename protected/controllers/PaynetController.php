<?php

class PaynetController extends CController
{

    public $layout = "admin";

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'roles' => array(User::ROLE_ADMINISTRATOR)
            ),
            array(
                'deny',
                'users' => array('*'),
            )
        );
    }

    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'enabled DESC';
        $paynets = Paynet::model()->findAll($criteria);
        $this->render('index', array('model' => $paynets));
    }

    public function actionAdd()
    {
        $paynet = new Paynet();
        $distibutors = User::model()->findChilds(Yii::app()->user->id)->findAll();

        if (isset ($_POST['Paynet'])) {
            $paynet->attributes = $_POST['Paynet'];
            if ($paynet->validate()) {
                $paynet->save(false);
                Yii::app()->user->setFlash('paynet', 'Новый Paynet терминал добавлен');
                $this->redirect(array('index'));
            }
        }

        $this->render('add', array('model' => $paynet, 'distributors' => $distibutors));
    }

    public function actionEdit()
    {
        $paynet = Paynet::model()->findByPk($_GET['id']);
        $distibutors = User::model()->findChilds(Yii::app()->user->id)->findAll();

        if (isset ($_POST['Paynet'])) {
            $paynet->attributes = $_POST['Paynet'];
            if ($paynet->validate()) {
                $paynet->save(false);
                Yii::app()->user->setFlash('paynet', 'Paynet терминал изменен');
                $this->redirect(array('index'));
            }
        }

        $this->render('edit', array('model' => $paynet, 'distributors' => $distibutors));
    }

    public function actionDelete()
    {
        if (Paynet::model()->deleteByPk($_GET['id']) == 1) {
            Yii::app()->user->setFlash('paynet', 'Paynet терминал удален');
            $this->redirect(array('index'));
        }
    }


    public function actionEnable()
    {
        $paynet = Paynet::model()->findByPk($_GET['id']);
        if ($paynet) {
            $paynet->enabled = 1;
            $paynet->save(false);
            $this->redirect(array('index'));
        }
    }

    public function actionDisable()
    {
        $paynet = Paynet::model()->findByPk($_GET['id']);
        if ($paynet) {
            $paynet->enabled = 0;
            $paynet->save(false);
            $this->redirect(array('index'));
        }
    }
}
