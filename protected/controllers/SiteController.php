<?php

class SiteController extends CController
{

    public function actions()
    {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;
        if ($error != null) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        } else {
            throw new CHttpException(404, "Станица не существует");
        }
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            if ($model->validate()) {
                if (Yii::app()->user->role == User::ROLE_ADMINISTRATOR) {
                    $this->redirect(array('admin/index'));
                } else if (Yii::app()->user->role == User::ROLE_DISTRIBUTOR) {
                    $this->redirect(array('distributor/index'));
                } else if (Yii::app()->user->role == User::ROLE_DEALER) {
                    $this->redirect(array('dealer/index'));
                }

                $this->redirect(Yii::app()->user->returnUrl);
            }
        }

        $model->password = '';
        $this->render('login', array('model' => $model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionTest()
    {
        echo Yii::app()->currencyFormatter->format(12423451241.123);
    }

}
