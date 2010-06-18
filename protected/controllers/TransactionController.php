<?php

class TransactionController extends CController
{

    public function init()
    {

        if (Yii::app()->user->role == User::ROLE_ADMINISTRATOR) {
            $this->layout = 'admin';
        } else if (Yii::app()->user->role == User::ROLE_DISTRIBUTOR) {
            $this->layout = 'distributor';
        } else if (Yii::app()->user->role == User::ROLE_DEALER) {
            $this->layout = 'dealer';
        }
    }

    public function actionIndex()
    {

        $criteria = new CDbCriteria();
        if (Yii::app()->user->role == User::ROLE_ADMINISTRATOR) {
            $criteria->condition = 'terminal IN (SELECT id FROM users WHERE owner IN (SELECT id FROM users WHERE owner IN (SELECT id FROM users WHERE owner = :user)))';
        } else if (Yii::app()->user->role == User::ROLE_DISTRIBUTOR) {
            $criteria->condition = 'terminal IN (SELECT id FROM users WHERE owner IN (SELECT id FROM users WHERE owner = :user))';
        } else if (Yii::app()->user->role == User::ROLE_DEALER) {
            $criteria->condition = 'terminal IN (SELECT id FROM users WHERE owner = :user)';
        }
        $criteria->params = array(':user' => Yii::app()->user->id);
        $criteria->order = 'id DESC';

        $paginator = new CPagination(Transaction::model()->count($criteria));
        $paginator->pageSize = 20;

        $paginator->applyLimit($criteria);
        $transactions = Transaction::model()->findAll($criteria);

        $this->render('index', array(
            'transactions' => $transactions,
            'paginator' => $paginator,
        ));
    }


}
