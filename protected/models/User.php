<?php

/**
 * @property int $id
 * @property boolean $enabled
 * @property string $role
 * @property string $username
 * @property string $password
 * @property int $deposit
 * @property int $percent
 * @property int $owner
 * @property string $first_name
 * @property string $second_name
 * @property string $telephone
 * @property string $alternate_telephone
 * @property string $address
 * @property string $organization
 * @property string $inn
 * @property string $rs
 * @property string $ss
 * @property string $bs
 * @property string $mfo
 * @property string $okonx
 * @property string $comments
 */
class User extends CActiveRecord
{

    const ROLE_ADMINISTRATOR =				'administrator';
    const ROLE_DISTRIBUTOR =				'distributor';
    const ROLE_DEALER =						'dealer';
    const ROLE_TERMINAL =					'terminal';
    const ROLE_GUEST =						'guest';

    const SCENARIO_LOGIN =					'login';
    const SCENARIO_REGISTER_DISTRIBUTOR =	'registerDistributor';
    const SCENARIO_EDIT_DISTRIBUTOR =		'editDistributor';
    const SCENARIO_REGISTER_DEALER =		'registerDealer';
    const SCENARIO_EDIT_DEALER =			'editDealer';
    const SCENARIO_REGISTER_TERMINAL =		'registerTerminal';
    const SCENARIO_EDIT_TERMINAL =			'editTerminal';
    const SCENARIO_CHANGE_PASSWORD =		'changePassword';
    const SCENARIO_EDIT_TERMINAL_BY_DEALER = 'editTerminalByDealer';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public $second_password = '';

    public function rules()
    {
        return array(
            array('username, password', 'required', 'on' => self::SCENARIO_REGISTER_DISTRIBUTOR . ',' . self::SCENARIO_EDIT_DISTRIBUTOR . ',' . self::SCENARIO_REGISTER_DEALER . ',' . self::SCENARIO_EDIT_DEALER . ',' . self::SCENARIO_REGISTER_TERMINAL . ',' . self::SCENARIO_EDIT_TERMINAL . ',' . self::SCENARIO_LOGIN),
            array('password, second_password', 'required', 'on' => self::SCENARIO_CHANGE_PASSWORD),
            array('password', 'required', 'on' => self::SCENARIO_EDIT_TERMINAL_BY_DEALER),
            array('second_password', 'compare', 'compareAttribute' => 'password', 'on' => self::SCENARIO_CHANGE_PASSWORD),
            array('username', 'length', 'min' => 4),
            array('password', 'length', 'min' => 6),
            array('username', 'unique', 'className' => 'User', 'attributeName' => 'username', 'on' => self::SCENARIO_REGISTER_DISTRIBUTOR . ',' . self::SCENARIO_EDIT_DISTRIBUTOR . ',' . self::SCENARIO_REGISTER_DEALER . ',' . self::SCENARIO_EDIT_DEALER . ',' . self::SCENARIO_REGISTER_TERMINAL . ',' . self::SCENARIO_EDIT_TERMINAL . ',' . SCENARIO_CHANGE_PASSWORD),
            array('username', 'checkForChars', 'on' => self::SCENARIO_REGISTER_DISTRIBUTOR . ',' . self::SCENARIO_EDIT_DISTRIBUTOR . ',' . self::SCENARIO_REGISTER_DEALER . ',' . self::SCENARIO_EDIT_DEALER . ',' . self::SCENARIO_REGISTER_TERMINAL . ',' . self::SCENARIO_EDIT_TERMINAL . ',' . SCENARIO_CHANGE_PASSWORD),
            array('percent, first_name, second_name, telephone, alternate_telephone, address, organization, inn, rs, ss, bs, mfo, okonx, comments', 'safe', 'on' => self::SCENARIO_REGISTER_DISTRIBUTOR . ',' . self::SCENARIO_EDIT_DISTRIBUTOR . ',' . self::SCENARIO_REGISTER_DEALER . ',' . self::SCENARIO_EDIT_DEALER . ',' . self::SCENARIO_REGISTER_TERMINAL . ',' . self::SCENARIO_EDIT_TERMINAL),
        );
    }

    public function checkForChars($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!ereg('^[0-9a-z_]*$', $this->username)) {
                $this->addError('amount', 'Логин может состоится только из маленьких латинскых букв (a-z), чисел (0-9) и из нижной черточки (_)');
                return;
            }
        }
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'Терминал',
            'username' => 'Логин',
            'password' => 'Пароль',
            'deposit' => 'Свободный объем',
            'percent' => 'Комиссионные',
            'first_name' => 'Имя',
            'second_name' => 'Фамилия',
            'organization' => 'Организация',
            'address' => 'Адрес',
            'telephone' => 'Телефон',
            'alternate_telephone' => 'Доп. телефон',
            'inn' => 'ИНН',
            'rs' => 'Расчетный счет',
            'ss' => 'Спецсчет',
            'bs' => 'Банк',
            'mfo' => 'МФО',
            'okonx' => 'ОКОНХ',
            'comments' => 'Комментарии',
            'overallTransactionSum' => 'Общая сумма транзакции',
            'reminder' => 'Остаток',
            'second_password' => 'Повторите пароль'
        );
    }

    public function relations()
    {
        return array(
            'transactions' => array(self::HAS_MANY, 'Transaction', 'terminal'),
            'children' => array(self::HAS_MANY, 'User', 'owner'),
            'parent' => array(self::BELONGS_TO, 'User', 'owner'),
            'overallTransactionSum' => array(self::STAT, 'Transaction', 'terminal', 'select' => 'SUM(amount)'),
            'childrenAmount' => array(self::STAT, 'User', 'owner'),
        );
    }

    public function filterRole($role)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "role = :role",
            'params' => array(':role' => $role)
        ));
        return $this;
    }

    public function findChilds($owner)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "owner = :owner",
            'params' => array(':owner' => $owner)
        ));
        return $this;
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                if ($this->scenario == self::SCENARIO_REGISTER_DISTRIBUTOR) {
                    $this->role = self::ROLE_DISTRIBUTOR;
                    $this->owner = Yii::app()->user->id;
                }
                if ($this->scenario == self::SCENARIO_REGISTER_DEALER) {
                    $this->role = self::ROLE_DEALER;
                    $this->owner = Yii::app()->user->id;
                }
                if ($this->scenario == self::SCENARIO_REGISTER_TERMINAL) {
                    $this->role = self::ROLE_TERMINAL;
                }
            }
            return true;
        }
        return false;
    }

    protected function getReminder()
    {
        if ($this->role == self::ROLE_DEALER) {
            $terminals = self::model()->findAll(array(
                'select' => 'deposit',
                'condition' => 'owner = :owner',
                'params' => array(':owner' => $this->id)
            ));

            $reminder = 0;
            foreach ($terminals as $terminal) {
                $reminder += $terminal->deposit;
            }

            return $reminder;
        } else if ($this->role == self::ROLE_DISTRIBUTOR) {
            $dealers = self::model()->findAll(array(
                'select' => 'id, deposit, role',
                'condition' => 'owner = :owner',
                'params' => array(':owner' => $this->id)
            ));

            $reminder = 0;
            foreach ($dealers as $dealer) {
                $reminder += ($dealer->reminder + $dealer->deposit);
            }

            return $reminder;
        } else if ($this->role == self::ROLE_ADMINISTRATOR) {
            $distributors = self::model()->findAll(array(
                'select' => 'id, deposit, role',
                'condition' => 'owner = :owner',
                'params' => array(':owner' => $this->id)
            ));

            $reminder = 0;
            foreach ($distributors as $distributor) {
                $reminder += ($distributor->reminder + $distributor->deposit);
            }

            return $reminder;
        } else { // terminal
            return 0;
        }
    }

    public function toggleEnable($enabled)
    {
        if ($this->role == self::ROLE_TERMINAL) {
            $this->enabled = $enabled ? 1 : 0;
            $this->save(false);
        } else if ($this->role == self::ROLE_DEALER) {
            $terminals = self::model()->findAll(array(
                'select' => 'id, role',
                'condition' => 'owner = :owner',
                'params' => array(':owner' => $this->id)
            ));

            foreach ($terminals as $terminal) {
                $terminal->toggleEnable($enabled);
            }

            $this->enabled = $enabled ? 1 : 0;
            $this->save(false);
        } else if ($this->role == self::ROLE_DISTRIBUTOR) {
            $dealers = self::model()->findAll(array(
                'select' => 'id, role',
                'condition' => 'owner = :owner',
                'params' => array(':owner' => $this->id)
            ));

            foreach ($dealers as $dealer) {
                $dealer->toggleEnable($enabled);
            }

            $this->enabled = $enabled ? 1 : 0;
            $this->save(false);
        }
    }
}
