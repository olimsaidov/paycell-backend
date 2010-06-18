<?php

class DealerController extends CController
{

    public $layout = 'dealer';

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
                'roles' => array(User::ROLE_DEALER)
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
//		$criteria->condition = 'owner IN (SELECT id FROM users WHERE owner = :owner)';
//		$criteria->params = array (':owner' => Yii::app()->user->id);


        if (($username = $_POST['username']) != '') {
            $criteria->condition .= '(id = :id OR username LIKE :username)';
            $criteria->params = array(':id' => $username, ':username' => '%' . $username . '%');
        }

        $terminals = User::model()->findChilds(Yii::app()->user->id)->findAll($criteria);
        $this->render('index', array('model' => $terminals, 'username' => $username));
    }

    public function actionShowTransfers()
    {
        $dateFrom = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateFrom']) ? $_POST['dateFrom'] : date('Y.m.d');
        $dateTo = ereg('[0-9][0-9][0-9][0-9]\.[0-9][0-9]\.[0-9][0-9]', $_POST['dateTo']) ? $_POST['dateTo'] : date('Y.m.d');

        $criteria = new CDbCriteria();
        $criteria->condition = 'DATE(dateTime) >= TIMESTAMP(:dateFrom) AND DATE(dateTime) <= TIMESTAMP(:dateTo) AND (`from` = :user OR `from` IN (SELECT id FROM users WHERE owner = :user) OR `to` IN (SELECT id FROM users WHERE owner = :user))';
        $criteria->params = array(':dateFrom' => $dateFrom, ':dateTo' => $dateTo, ':user' => Yii::app()->user->id);
        $criteria->order = 'dateTime';
        $model = Transfer::model()->findAll($criteria);

        $this->render('showTransfers', array(
            'model' => $model,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ));
    }

    public function actionCancelTransfer()
    {
        $transfer = Transfer::model()->findByPk($_GET['id']);

        if ($transfer == null) {
            throw new CHttpException(400, 'Такой перевод денег никогда не произведен');
        }
        if ($transfer->sender->role == User::ROLE_TERMINAL) {
            if ($transfer->sender->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
            }
        } else if ($transfer->sender->role == User::ROLE_DEALER) {
            if ($transfer->sender->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
            }
        } else {
            throw new CHttpException(403, 'У вас нет прав отменить этот перевод');
        }

        if (!$transfer->delete()) {
            throw new CHttpException(400, 'У получателя не достаточно депозита чтобы отменить перевод денег');
        }

        Yii::app()->user->setFlash('dealer', 'Перевод денег отменен');
        $this->redirect(array('showtransfers'));
    }

    public function actionCreateTransfer()
    {
        $receiver = User::model()->findByPk($_GET['to']);

        if ($receiver == null) {
            throw new CHttpException(400, 'Пользователь не существует');
        }

        if ($receiver->role == User::ROLE_TERMINAL) {
            if ($receiver->parent->id != Yii::app()->user->id) {
                throw new CHttpException(403, 'У вас нет прав произвести перевод денег на счет этого терминала');
            }
        } else {
            throw new CHttpException(403, 'Вы можете переводить денги только своим терминалам');
        }

        $model = new Transfer();
        $model->to = $receiver->id;

        if (isset ($_POST['Transfer'])) {
            $model->attributes = $_POST['Transfer'];

            if ($model->validate()) {
                $model->save(false);
                Yii::app()->user->setFlash('dealer', "Депозит терминала <b>$receiver->username ($receiver->id)</b> изменен на " . Yii::app()->currencyFormatter->format($model->amount) . " сумов");
                $this->redirect(array('index'));
            }
        }

        $this->render('createTransfer', array('model' => $model));
    }

    public function actionDisableTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->toggleEnable(false);

        Yii::app()->user->setFlash('dealer', 'Терминал <b>' . $terminal->username . ' (' . $terminal->id . ')</b> отключен');
        $this->redirect(array('index'));
    }

    public function actionEnableTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->toggleEnable(true);

        Yii::app()->user->setFlash('dealer', 'Терминал <b>' . $terminal->username . ' (' . $terminal->id . ')</b> включен');
        $this->redirect(array('index'));
    }

    public function actionEditTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $terminal->scenario = User::SCENARIO_EDIT_TERMINAL_BY_DEALER;

        if (isset ($_POST['User'])) {
            $terminal->attributes = $_POST['User'];
            if ($terminal->validate()) {
                $terminal->save(false);
                Yii::app()->user->setFlash('dealer', "Терминал $terminal->username ($terminal->id) изменен");
                $this->redirect(array('index'));
            }
        }

        $this->render('editTerminal', array('model' => $terminal));
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
                Yii::app()->user->setFlash('dealer', 'Ваш пароль изменен');
                $this->redirect(array('index'));
            }
        }
        $this->render('changePassword', array('model' => $model));
    }

    public function actionShowTerminal()
    {
        $terminal = User::model()->findByPk($_GET['id']);

        if (($terminal == null) || ($terminal->role != User::ROLE_TERMINAL) || ($terminal->parent->id != Yii::app()->user->id)) {
            throw new CHttpException(400, 'Терминал не существует');
        }

        $this->render('showTerminal', array('model' => $terminal));
    }
}
