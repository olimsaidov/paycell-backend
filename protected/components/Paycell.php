<?php

class Paycell
{
    private $_username;
    private $_password;
    private $_terminal;
    private $_deposit;
    private $_percent;
    private $_enabled;
    private $_rawPostData;

    const SERVICE_MTS = 1;
    const SERVICE_BEELINE = 2;
    const SERVICE_UCELL = 3;
    const SERVICE_PERFECTUM_MOBILE = 4;
    const SERVICE_UZMOBILE = 5;

    const STATUS_TRANSACTION_CONNECTION_ERROR = -3; // Ошибка соединения
    const STATUS_TRANSACTION_SENDING = -2;          // Платеж отправляется
    const STATUS_TRANSACTION_PENDING = -1;          // Платеж в обработке
    const STATUS_TRANSACTION_ACCEPTED = 0;          // Проплачен
    const STATUS_TRANSACTION_NOT_EXISTS = 1;        // Нет такой транзации
    const STATUS_TRANSACTION_ANNULATED = 2;         // Транзакция была отменена
    const STATUS_INCORRECT_NUMBER = 3;              // Не найден номер клиента
    const STATUS_NOT_ENOUGH_DEPOSIT = 4;            // Баланс пользователя меньше необходимого
    const STATUS_PROVIDER_NOT_AVAILABLE = 5;        // Провайдер/оператор выключен
    const STATUS_SERVER_ERROR = 6;                  // Ошибка системы
    const STATUS_ANNULATION_ERROR = 9;              // Ошибка аннулирования
    const STATUS_PRECHECKING = 10;                  // Ведутся профилактические работы
    const STATUS_INCORRECT_AMOUNT = 11;             // Неправильная сумма
    const STATUS_TERMINAL_NOT_EXISTS = 12;          // Терминал не существует
    const STATUS_ACCESS_DENIED = 13;                // Доступ запрешен
    const STATUS_UNKNOWN_STATUS = 1000000;          // Неизвестный статус

    public function __construct($rawPostData)
    {
        $this->_rawPostData = $rawPostData;
    }

    private function getSoapHeaders()
    {
        try {
            $xmlReader = new SimpleXMLElement($this->_rawPostData);
            $xmlReader->registerXPathNamespace('xsd1', 'http://paycell.uz/schema');
            $xmlReader->registerXPathNamespace('tns', 'http://paycell.uz');
            $terminal = $xmlReader->xpath("/SOAP-ENV:Envelope/SOAP-ENV:Header/xsd1:credHeader/*[local-name()='terminal']");
            $username = $xmlReader->xpath("/SOAP-ENV:Envelope/SOAP-ENV:Header/xsd1:credHeader/*[local-name()='userName']");
            $password = $xmlReader->xpath("/SOAP-ENV:Envelope/SOAP-ENV:Header/xsd1:credHeader/*[local-name()='password']");
            $terminal = $terminal[0];
            $username = $username[0];
            $password = $password[0];

            if (($terminal != null) && ($username != null) && ($password != null)) {
                $terminal = (int)$terminal[0];
                $username = (string)$username[0];
                $password = (string)$password[0];
            } else {
                throw new Exception("header is not well formed");
            }
            $result = array(
                "terminal" => $terminal,
                "username" => $username,
                "password" => $password);
            return $result;
        } catch (Exception $exception) {
            throw new SoapFault("Client", "Не загаловка авторизации или оно не правильно сформировано");
        }
    }

    private function authenticate()
    {
        $header = $this->getSoapHeaders();

        $this->_username = $header["username"];
        $this->_password = $header["password"];
        $this->_terminal = $header["terminal"];

        $sql = "SELECT deposit, percent, enabled FROM users WHERE id = :terminal AND password = :password AND username = :username AND role = 'terminal'";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":terminal", $this->_terminal, PDO::PARAM_INT);
        $command->bindParam(":username", $this->_username, PDO::PARAM_STR);
        $command->bindParam(":password", $this->_password, PDO::PARAM_STR);
        $row = $command->queryRow();

        if ($row == null) {
            return false;
        } else {
            $this->_deposit = $row['deposit'];
            $this->_percent = $row['percent'];
            $this->_enabled = $row['enabled'];
            return true;
        }
    }

    private function defaultFilter()
    {
        if (!$this->authenticate()) {
            throw new SoapFault("Client", "Нет доступа");
        }
    }

    public function isAuthenticated()
    {
        return $this->authenticate();
    }

    public function getNews($lastNewsId)
    {
        $this->defaultFilter();

        $sql = "SELECT * FROM news WHERE id > :lastNewsId ORDER BY id ASC";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":lastNewsId", $lastNewsId);
        $dataReader = $command->query();

        $newsArray = array();
        foreach ($dataReader as $row) {
            $newsArray[] = array(
                "id" => $row["id"],
                "title" => $row["title"],
                "text" => $row["text"],
                "dateTime" => date(DATE_ISO8601, strtotime($row["dateTime"])));
        }
        return $newsArray;
    }

    public function getServices()
    {
        $this->defaultFilter();

        $services = implode("", file("./protected/data/services.xml"));
        return $services;
    }

    public function checkDeposit()
    {
        $this->defaultFilter();

        return $this->_deposit;
    }

    public function getPercentValue()
    {
        $this->defaultFilter();

        return $this->_percent;
    }

    public function changePassword($newPassword)
    {
        $this->defaultFilter();

        $sql = "UPDATE users SET password = :password WHERE id = :terminal";
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":password", $newPassword, PDO::PARAM_STR);
        $command->bindParam(":terminal", $this->_terminal, PDO::PARAM_INT);
        return $command->execute() == 1;
    }

    public function transferMoney($terminal, $amount)
    {
        $this->defaultFilter();

        if ($this->_enabled == 0) {
            return array("code" => self::STATUS_ACCESS_DENIED, "dateTime" => date(DATE_ISO8601, time()));
        }

        $self = User::model()->findByPk($this->_terminal);
        $receiver = User::model()->findByPk($terminal);

        if (($receiver == null) || ($receiver->role != User::ROLE_TERMINAL)) {
            return array("code" => self::STATUS_TERMINAL_NOT_EXISTS, "dateTime" => date(DATE_ISO8601, time()));
        }

        if ($receiver->parent->id != $self->parent->id) {
            return array("code" => self::STATUS_ACCESS_DENIED, "dateTime" => date(DATE_ISO8601, time()));
        }

        if ($amount <= 0) {
            return array("code" => self::STATUS_INCORRECT_AMOUNT, "dateTime" => date(DATE_ISO8601, time()));
        }

        if ($amount > $this->_deposit) {
            return array("code" => self::STATUS_NOT_ENOUGH_DEPOSIT, "dateTime" => date(DATE_ISO8601, time()));
        }

        $transfer = new Transfer();
        $transfer->from = $this->_terminal;
        $transfer->to = $receiver->id;
        $transfer->amount = $amount;

        $transfer->save(false);

        return array("code" => self::STATUS_TRANSACTION_ACCEPTED, "dateTime" => date(DATE_ISO8601, time()));
    }

    public function payMobileOperator($service, $number, $amount, $id)
    {
        $this->defaultFilter();

        if ($this->_enabled == 0) {
            return array("code" => self::STATUS_ACCESS_DENIED, "dateTime" => date(DATE_ISO8601, time()));
        }

        if (!in_array($service, array(
            self::SERVICE_BEELINE,
            self::SERVICE_MTS,
            self::SERVICE_PERFECTUM_MOBILE,
            self::SERVICE_UCELL,
            self::SERVICE_UZMOBILE
        ))
        ) {
            throw new SoapFault("Client", "Такого сервиса не существует");
        }

        $amount = (int)($amount * (100 - $this->_percent) / 100);

        if ($amount > $this->_deposit) {
            return array("code" => self::STATUS_NOT_ENOUGH_DEPOSIT, "dateTime" => date(DATE_ISO8601, time()));
        }

        $self = User::model()->findByPk($this->_terminal);
        if (($self->parent->deposit + $self->parent->reminder - $amount) < 0) {
            return array("code" => self::STATUS_ACCESS_DENIED, "dateTime" => date(DATE_ISO8601, time()));
        }

        if (($self->parent->parent->deposit + $self->parent->parent->reminder - $amount) < 0) {
            return array("code" => self::STATUS_ACCESS_DENIED, "dateTime" => date(DATE_ISO8601, time()));
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $sql = 'UPDATE users SET deposit = deposit - :amount WHERE id = :terminal';
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":amount", $amount);
            $command->bindParam(":terminal", $this->_terminal);
            $command->execute();
            unset($command);

            $sql = "INSERT INTO transactions (id, client_transaction_id, terminal, service, number, amount)
				VALUES (0, :client_transaction_id, :terminal, :service, :number, :amount)";

            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":client_transaction_id", $id, PDO::PARAM_INT);
            $command->bindParam(":terminal", $this->_terminal, PDO::PARAM_INT);
            $command->bindParam(":service", $service, PDO::PARAM_INT);
            $command->bindParam(":number", $number, PDO::PARAM_INT);
            $command->bindParam(":amount", $amount, PDO::PARAM_INT);
            $command->execute();
            unset($command);

            $insertID = Yii::app()->db->lastInsertID;

            $gateway = new BaseGateway();
            $result = $gateway->pay($insertID, $service, $number, $amount, $self->parent->parent);

            $sql = "UPDATE transactions SET status = :status, message = :message, dateTime = :dateTime, paynet_transaction_id = :paynet_transaction_id WHERE id = :id";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":status", $result['status'], PDO::PARAM_INT);
            $command->bindParam(":message", $result['message'], PDO::PARAM_STR);
            $command->bindParam(":dateTime", date("Y:m:d H:i:s", $result['dateTime']), PDO::PARAM_STR);
            $command->bindParam(":paynet_transaction_id", $result['id'], PDO::PARAM_INT);
            $command->bindParam(":id", $insertID, PDO::PARAM_INT);
            $command->execute();
            unset($command);

            if ($result['status'] != self::STATUS_TRANSACTION_ACCEPTED) {
                $sql = 'UPDATE users SET deposit = deposit + :amount WHERE id = :terminal';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(":amount", $amount);
                $command->bindParam(":terminal", $this->_terminal);
                $command->execute();
                unset($command);
            } else {
                $sql = 'UPDATE users SET deposit = deposit - :amount WHERE id = :terminal';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(":amount", $result['fee']);
                $command->bindParam(":terminal", $this->_terminal);
                $command->execute();
                unset($command);

                $sql = 'UPDATE users SET deposit = deposit + :amount WHERE id = :terminal';
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(":amount", $result['fee']);
                $command->bindParam(':terminal', Yii::app()->params['koshelok']);
                $command->execute();
                unset($command);
            }
            $transaction->commit();

            return array('code' => $result['status'], 'dateTime' => date(DATE_ISO8601, $result['dateTime']));
        } catch (Exception $exception) {
            $transaction->rollBack();
            return array('code' => self::STATUS_SERVER_ERROR, 'dateTime' => date(DATE_ISO8601, time()));
        }
    }

    public function getTransactionStatus($id)
    {
        $this->defaultFilter();

        $sql = 'SELECT status, dateTime FROM transactions WHERE terminal = :terminal AND client_transaction_id = :id';
        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(":id", $id, PDO::PARAM_INT);
        $command->bindParam(":terminal", $this->_terminal, PDO::PARAM_INT);
        $result = $command->queryRow();

        if ($result) {
            return array('code' => $result['status'], 'dateTime' => date(DATE_ISO8601, $result['dateTime']));
        }

        return array('code' => self::STATUS_TRANSACTION_NOT_EXISTS, 'dateTime' => date(DATE_ISO8601, time()));
    }

    public function getStatusCodes()
    {
        $this->defaultFilter();

        return file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'statuses.xml');
    }

    public function getDollarCourse()
    {
        $this->defaultFilter();

        return Yii::app()->params['dollarCourse'];
    }
}

