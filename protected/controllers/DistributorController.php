<?php

class DistributorController extends CController
{

    public $layout = 'distributor';

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
                'roles' => array(User::ROLE_DISTRIBUTOR)
            ),
            array(
                'deny',
                'users' => array('*'),
            )
        );
    }

    public function actionIndex()
    {
        $dealers = User::model()->findChilds(Yii::app()->user->id)->with('childrenAmount')->findAll();
        $this->render('index', array('model' => $dealers));
    }

    public function actionShowDealer()
    {
        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);
        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        $this->render('showDealer', array('model' => $dealer));
    }

    public function actionAddDealer()
    {
        $dealer = new User(User::SCENARIO_REGISTER_DEALER);

        if (isset ($_POST['User'])) {
            $dealer->attributes = $_POST['User'];

            if ($dealer->validate()) {
                $dealer->save(false);
                Yii::app()->user->setFlash('distributor', "Новый диллер <b>$dealer->username ($dealer->organization)</b> добавлен");
                $this->redirect(array('index'));
            }
        }
        $this->render('addDealer', array('model' => $dealer));
    }

    public function actionEditDealer()
    {
        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);
        $dealer->scenario = USER::SCENARIO_EDIT_DISTRIBUTOR;

        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        if (isset ($_POST['User'])) {
            $dealer->attributes = $_POST['User'];

            if ($dealer->validate()) {
                $dealer->save(false);
                Yii::app()->user->setFlash('distributor', "Диллер <b>$dealer->username ($dealer->organization)</b> изменен");
                $this->redirect(array('index'));
            }
        }
        $this->render('editDealer', array('model' => $dealer));
    }


    public function actionEnableDealer()
    {
        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        $dealer->toggleEnable(true);

        Yii::app()->user->setFlash('distributor', "Диллер <b>$dealer->username ($dealer->organization)</b> включен");
        $this->redirect(array('index'));
    }

    public function actionDisableDealer()
    {
        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        $dealer->toggleEnable(false);

        Yii::app()->user->setFlash('distributor', "Диллер <b>$dealer->username ($dealer->organization)</b> отключен");
        $this->redirect(array('index'));
    }

    public function actionDeleteDealer()
    {
//		throw new CHttpException(400, "Удаление диллеров временно отключено");

        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['id']);

        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        $dealer->delete();

        Yii::app()->user->setFlash('distributor', "Диллер <b>$dealer->username ($dealer->organization)</b> удален");
        $this->redirect(array('index'));
    }

    public function actionDeleteTerminal()
    {

        $terminal = User::model()->findByPk($_GET['id']);
        if ($terminal->parent->parent->id != Yii::app()->user->id) {
            $terminal = null;
        }

        if ($terminal == null) {
            throw new CHttpException(400, "Терминал не существует");
        }

        $terminal->delete();

        Yii::app()->user->setFlash('distributor', "Терминал <b>$terminal->username ($terminal->id)</b> удален");
        $this->redirect(array('index'));
    }

    public function actionAddTerminal()
    {
        $dealer = User::model()->findChilds(Yii::app()->user->id)->findByPk($_GET['for']);

        if ($dealer == null) {
            throw new CHttpException(400, "Диллер не существует");
        }

        $terminal = new User(User::SCENARIO_REGISTER_TERMINAL);
        $terminal->owner = $dealer->id;

        if (isset ($_POST['User'])) {
            $terminal->attributes = $_POST['User'];

            if ($terminal->validate()) {
                $terminal->save(false);
                Yii::app()->user->setFlash('distributor', "Новый терминал <b>$terminal->username ($terminal->id)</b> для диллера <b>$dealer->username ($dealer->organization)</b> добавлен");
                $this->redirect(array('showdealer', 'id' => $dealer->id));
            }
        }
        $this->render('addTerminal', array('model' => $terminal, 'dealer' => $dealer));
    }

    public function actionShowTransfers()
    {
        $dateFrom = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateFrom']) ? $_POST['dateFrom'] : date('Y.m.d');
        $dateTo = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateTo']) ? $_POST['dateTo'] : date('Y.m.d');

        $criteria = new CDbCriteria();
        $criteria->condition = 'DATE(dateTime) >= TIMESTAMP(:dateFrom) AND DATE(dateTime) <= TIMESTAMP(:dateTo) AND (`from` = :user OR `from` IN (SELECT id FROM users WHERE owner = :user) OR `from` IN (SELECT id FROM users WHERE owner IN (SELECT id FROM users WHERE owner = :user)))';
        $criteria->params = array(':dateFrom' => $dateFrom, ':dateTo' => $dateTo, ':user' => Yii::app()->user->id);
        $model = Transfer::model()->findAll($criteria);

        $this->render('showTransfers', array(
            'model' => $model,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
    }

    public function actionCreateTransfer()
    {
        $receiver = User::model()->findByPk($_GET['to']);

        if ($receiver == null) {
            throw new CHttpException(400, 'Пользователь не существует');
        }

        if ($receiver->role == User::ROLE_DEALER) {
            if ($receiver->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав произвести перевод денег на счет этого диллера');
            }
        } else if ($receiver->role == User::ROLE_TERMINAL) {
            if ($receiver->parent->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав произвести перевод денег на счет этого терминала');
            }
        } else {
            throw new CHttpException(403, 'Вы можете переводить денги только своим диллера и терминалам');
        }

        $model = new Transfer();
        $model->to = $receiver->id;

        if (isset ($_POST['Transfer'])) {
            $model->attributes = $_POST['Transfer'];

            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('distributor', "Депозит " . ($receiver->role == User::ROLE_DEALER ? "диллера" : "терминала") . " <b>$receiver->username (" . ($receiver->role == User::ROLE_DEALER ? $receiver->organization : $receiver->id) . ")</b> изменен на " . Yii::app()->currencyFormatter->format($model->amount) . " сумов");
                $this->redirect(array('index'));
            }
        }

        $this->render('createTransfer', array('model' => $model));
    }

    public function actionEditTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->scenario = User::SCENARIO_EDIT_TERMINAL;

        if (isset ($_POST['User'])) {
            $terminal->attributes = $_POST['User'];
            if ($terminal->validate()) {
                $terminal->save(false);
                Yii::app()->user->setFlash('distributor', 'Терминал <b>' . $terminal->username . ' (' . $terminal->id . ')</b> изменен');
                $this->redirect(array('showdealer', 'id' => $terminal->parent->id));
            }
        }

        $this->render('editTerminal', array('model' => $terminal));
    }

    public function actionDisableTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->toggleEnable(false);

        Yii::app()->user->setFlash('distributor', 'Терминал <b>' . $terminal->username . ' (' . $terminal->id . ')</b> отключен');
        $this->redirect(array('showdealer', 'id' => $terminal->parent->id));
    }

    public function actionEnableTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->toggleEnable(true);

        Yii::app()->user->setFlash('distributor', 'Терминал <b>' . $terminal->username . ' (' . $terminal->id . ')</b> включен');
        $this->redirect(array('showdealer', 'id' => $terminal->parent->id));
    }

    public function actionCancelTransfer()
    {
        $transfer = Transfer::model()->findByPk($_GET['id']);

        if ($transfer == null) {
            throw new CHttpException(400, 'Такой перевод денег никогда не произведен');
        }

        if ($transfer->sender->role == User::ROLE_TERMINAL) {
            if ($transfer->sender->parent->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
            }
        } else if ($transfer->sender->role == User::ROLE_DEALER) {
            if ($transfer->sender->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
            }
        } else if ($transfer->sender->role == User::ROLE_DISTRIBUTOR) {
            if ($transfer->sender->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
            }
        } else {
            throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
        }

        if (!$transfer->delete()) {
            throw new CHttpException(400, 'У получателя не достаточно депозита чтобы отменить перевод денег');
        }

        Yii::app()->user->setFlash('distributor', 'Перевод денег отменен');
        $this->redirect(array('showtransfers'));
    }

    public function actionShowTerminals()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'owner IN (SELECT id FROM users WHERE owner = :owner)';
        $criteria->params = array(':owner' => Yii::app()->user->id);


        if (($username = $_POST['username']) != '') {
            $criteria->condition .= ' AND (id = :id OR username LIKE :username)';
            $criteria->params[':id'] = $username;
            $criteria->params[':username'] = '%' . $username . '%';
        }

        $terminals = User::model()->findAll($criteria);
        $this->render('showTerminals', array('model' => $terminals, 'username' => $username));
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
                Yii::app()->user->setFlash('distributor', 'Ваш пароль изменен');
                $this->redirect(array('index'));
            }
        }
        $this->render('changePassword', array('model' => $model));
    }

    public function actionShowTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->role != User::ROLE_TERMINAL) || ($terminal->parent->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $this->render('showTerminal', array('model' => $terminal));
    }
}
