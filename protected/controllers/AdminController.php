<?php

class AdminController extends CController
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
        $distibutors = User::model()->findChilds(Yii::app()->user->id)->with('childrenAmount')->findAll();
        $this->render('index', array('model' => $distibutors));
    }

    public function actionAdd()
    {
        $distributor = new User(User::SCENARIO_REGISTER_DISTRIBUTOR);

        if (isset ($_POST['User'])) {
            $distributor->attributes = $_POST['User'];

            if ($distributor->validate()) {
                $distributor->save(false);
                Yii::app()->user->setFlash('admin', "Новый дистрибютор <b>$distributor->username ($distributor->organization)</b> добавлен");
                $this->redirect(array('index'));
            }
        }
        $this->render('add', array('model' => $distributor));
    }

    public function actionEdit()
    {
        $distributor = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);
        $distributor->scenario = USER::SCENARIO_EDIT_DISTRIBUTOR;

        if ($distributor == null) {
            throw new CHttpException(400, "Дистрибютор не существует");
        }

        if (isset ($_POST['User'])) {
            $distributor->attributes = $_POST['User'];

            if ($distributor->validate()) {
                $distributor->save(false);
                Yii::app()->user->setFlash('admin', "Дистрибютор <b>$distributor->username ($distributor->organization)</b> изменен");
                $this->redirect(array('index'));
            }
        }
        $this->render('edit', array('model' => $distributor));
    }

    public function actionEnable()
    {
        $distributor = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($distributor == null) {
            throw new CHttpException(400, "Дистрибютор не существует");
        }

        $distributor->toggleEnable(true);

        Yii::app()->user->setFlash('admin', "Дистрибютор <b>$distributor->username ($distributor->organization)</b> включен");
        $this->redirect(array('index'));
    }

    public function actionDisable()
    {
        $distributor = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($distributor == null) {
            throw new CHttpException(400, "Дистрибютор не существует");
        }

        $distributor->toggleEnable(false);

        Yii::app()->user->setFlash('admin', "Дистрибютор <b>$distributor->username  ($distributor->organization)</b> отключен");
        $this->redirect(array('index'));
    }

    public function actionDelete()
    {
        throw new CHttpException(400, "Удаление дистрибюторов временно отключено");

        $distributor = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($distributor == null) {
            throw new CHttpException(400, "Дистрибютор не существует");
        }

        $distributor->delete();

        Yii::app()->user->setFlash('admin', "Дистрибютор <b>$distributor->username ($distributor->organization)</b> удален");
        $this->redirect(array('index'));
    }

    public function actionShowDistributor()
    {
        $distributor = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);
        if ($distributor == null) {
            throw new CHttpException(400, "Дистрибютор не существует");
        }

        $this->render('showDistributor', array('model' => $distributor));
    }

    public function actionShowTransfers()
    {
        $dateFrom = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateFrom']) ? $_POST['dateFrom'] : date('Y.m.d');
        $dateTo = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateTo']) ? $_POST['dateTo'] : date('Y.m.d');

        $criteria = new CDbCriteria();
        $criteria->condition = 'DATE(dateTime) >= TIMESTAMP(:dateFrom) AND DATE(dateTime) <= TIMESTAMP(:dateTo) AND `from` = :from';
        $criteria->params = array(':dateFrom' => $dateFrom, ':dateTo' => $dateTo, ':from' => Yii::app()->user->id);

        $user = null;
        $receiver = $_POST['receiver'];
        if ($receiver) {
            if (is_numeric($receiver)) {
                $user = User::model()->findByPk($receiver);
            } else {
                $user = User::model()->find('username = :username', array(':username' => $receiver));
            }

            if ($user == null) {
                throw new CHttpException(400, "Такого пользователья не существует");
            } else {
                $criteria->condition .= ' AND `to` = :to';
                $criteria->params[':to'] = $user->id;
            }
        }

        $model = Transfer::model()->with('receiver')->findAll($criteria);

        $username = $_POST['username'];
        if ($username != '') {
            $filteredModel = array();
            foreach ($model as $transaction) {
                if (eregi($username, $transaction->receiver->username)) {
                    $filteredModel[] = $transaction;
                }
            }
            $model = $filteredModel;
        }

        $this->render('showTransfers', array(
            'model' => $model,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'username' => $username,
            'receiver' => $receiver,
        ));
    }

    public function actionCreateTransfer()
    {
        $receiver = User::model()->findByPk($_GET['to']);

        if ($receiver == null) {
            throw new CHttpException(400, 'Пользователь не существует');
        }

        if ($receiver->role != User::ROLE_DISTRIBUTOR) {
            throw new CHttpException(400, 'Вы можете переводить денги только своим дистрибюторам');
        }

        if ($receiver->parent->id != Yii::app()->user->id) {
            throw new CHttpException(403, 'У вас нет прав произвести перевод денег на счет этого дистрибютора');
        }

        $model = new Transfer();
        $model->to = $receiver->id;

        if (isset ($_POST['Transfer'])) {
            $model->attributes = $_POST['Transfer'];

            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('admin', "Депозит дистрибютора <b>$receiver->username ($receiver->organization)</b> изменен на " . Yii::app()->currencyFormatter->format($model->amount) . " сумов");
                $this->redirect(array('index'));
            }
        }

        $this->render('createTransfer', array('model' => $model));
    }

    public function actionCancelTransfer()
    {
        $transfer = Transfer::model()->findByPk($_GET['id']);

        if (($transfer == null) || ($transfer->sender->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Такой перевод денег никогда не произведен или у вас нет прав отменить его');
        }

        if (!$transfer->delete()) {
            throw new CHttpException(400, 'У получателя не достаточно депозита чтобы отменить перевод денег');
        }

        Yii::app()->user->setFlash('admin', 'Перевод денег отменен');
        $this->redirect(array('showtransfers'));
    }

    public function actionChangePassword()
    {
        $model = User::model()->findByPk(Yii::app()->user->id);
        $model->scenario = User::SCENARIO_CHANGE_PASSWORD;
        $model->password = '';

        if (isset ($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('admin', 'Ваш пароль изменен');
                $this->redirect(array('index'));
            }
        }

        $this->render('changePassword', array('model' => $model));
    }

}
