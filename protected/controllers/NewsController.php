<?php

class NewsController extends CController
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
        $model = News::model()->findAll(array(
            'order' => 'dateTime DESC'
        ));
        $this->render('index', array('model' => $model));
    }

    public function actionAdd()
    {
        $model = new News();

        if (isset ($_POST['News'])) {
            $model->attributes = $_POST['News'];
            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('news', 'Новость добавлена');
                $this->redirect(array('index'));
            }
        }
        $this->render('add', array('model' => $model));
    }

    public function actionDelete()
    {
        $model = News::model()->findByPk($_GET['id']);

        if ($model == null) {
            throw new CHttpException(400, "Новость не существует");
        }

        $model->delete();
        Yii::app()->user->setFlash('news', 'Новость удалена');

        $this->redirect(array('index'));
    }

    public function actionEdit()
    {
        $model = News::model()->findByPk($_GET['id']);

        if ($model == null) {
            throw new CHttpException(400, "Новость не существует");
        }

        if (isset ($_POST['News'])) {
            $model->attributes = $_POST['News'];
            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('news', 'Новость изменена');
                $this->redirect(array('index'));
            }
        }
        $this->render('edit', array('model' => $model));
    }
}
