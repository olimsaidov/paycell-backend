<?php

class Transfer extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->from = Yii::app()->user->id;
        }
    }

    public function tableName()
    {
        return 'transfers';
    }

    public function attributeLabels()
    {
        return array(
            'amount' => 'Сумма'
        );
    }

    public function relations()
    {
        return array(
            'sender' => array(self::BELONGS_TO, 'User', 'from'),
            'receiver' => array(self::BELONGS_TO, 'User', 'to'),
        );
    }

    public function rules()
    {
        return array(
            array('amount', 'required'),
            array('comments', 'safe'),
            array('amount', 'validateAmount'),
        );
    }

    public function validateAmount($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!eregi('^-?[0-9]+$', $this->amount)) {
                $this->addError('amount', 'Сумма должна быть целым числом');
                return;
            }

            if ($this->sender->deposit < $this->amount) {
                $this->addError('amount', "Сумма должна быть не больше {$this->sender->deposit} сумов");
                return;
            }

            $sum = $this->receiver->reminder + $this->receiver->deposit;
            if (($sum + $this->amount) < 0) {
                $this->addError('amount', 'Сумма должна быть меньше -' . $sum . ' сумов');
                return;
            }
        }
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->dateTime = new CDbExpression("CURRENT_TIMESTAMP()");

                $transaction = Yii::app()->db->beginTransaction();

                $sql = 'UPDATE users SET deposit = deposit - :amount WHERE id = :id';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindValue(':amount', $this->amount + $this->amount * Yii::app()->params['transferPercent'] / 100);
                $command->bindParam(':id', $this->sender->id);
                $command->execute();

                $sql = 'UPDATE users SET deposit = deposit + :amount WHERE id = :id';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(':amount', $this->amount);
                $command->bindParam(':id', $this->receiver->id);
                $command->execute();

                $sql = 'UPDATE users SET deposit = deposit + :amount WHERE id = :id';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindValue(':amount', $this->amount * Yii::app()->params['transferPercent'] / 100);
                $command->bindParam(':id', Yii::app()->params['koshelok']);
                $command->execute();

                $transaction->commit();

            }
            return true;
        }
        return false;
    }

    protected function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if (($this->receiver->deposit + $this->receiver->reminder - $this->amount) < 0) {
                return false;
            }

            $transaction = Yii::app()->db->beginTransaction();

            $sql = 'UPDATE users SET deposit = deposit + :amount WHERE id = :id';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(':amount', $this->amount);
            $command->bindParam(':id', $this->sender->id);
            $command->execute();

            $sql = 'UPDATE users SET deposit = deposit - :amount WHERE id = :id';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(':amount', $this->amount);
            $command->bindParam(':id', $this->receiver->id);
            $command->execute();

            $transaction->commit();

            return true;
        }
        return false;
    }
}
