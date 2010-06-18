<?php

class ServiceController extends CController
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
        if (isset ($_POST['gateway'])) {
            foreach ($_POST['gateway'] as $id => $gateway) {
                $service = Service::model()->findByPk($id);
                if ($service == null) {
                    throw new CHttpException(400, "Сервис не существует");
                }
                $service->gateway = $gateway;
                if (!$service->validate()) {
                    throw new CHttpException(400, "Нет такого шлюзя");
                }
                $service->save(false);
            }
            Yii::app()->user->setFlash('service', 'Настройки сохранены');
            $this->redirect(array('service/index'));
        } else {
            $services = Service::model()->findAll();
            $this->render('index', array('services' => $services));
        }
    }
}
