<?php

class SamonlineController extends CController
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
        $samonlines = Samonline::model()->findAll();
        $this->render('index', array('model' => $samonlines));
    }

    public function actionAdd()
    {
        $samonline = new Samonline();

        if (isset ($_POST['Samonline'])) {
            $samonline->attributes = $_POST['Samonline'];
            if ($samonline->validate()) {
                $samonline->save(false);
                Yii::app()->user->setFlash('samonline', 'Новый Samonline терминал добавлен');
                $this->redirect(array('index'));
            }
        }

        $this->render('add', array('model' => $samonline));
    }

    public function actionEdit()
    {
        $samonline = Samonline::model()->findByPk($_GET['id']);

        if (isset ($_POST['Samonline'])) {
            $samonline->attributes = $_POST['Samonline'];
            if ($samonline->validate()) {
                $samonline->save(false);
                Yii::app()->user->setFlash('samonline', 'Samonline терминал изменен');
                $this->redirect(array('index'));
            }
        }

        $this->render('edit', array('model' => $samonline));
    }

    public function actionDelete()
    {
        if (Samonline::model()->deleteByPk($_GET['id']) == 1) {
            Yii::app()->user->setFlash('samonline', 'Samonline терминал удален');
            $this->redirect(array('index'));
        }
    }
}
