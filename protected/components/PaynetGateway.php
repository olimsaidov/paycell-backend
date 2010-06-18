<?php

class PaynetGateway
{
    const PERCENT								= 2;

    const SERVICE_MTS							= 1;
    const SERVICE_BEELINE						= 2;
    const SERVICE_PERFECTUM_MOBILE				= 64;
    const SERVICE_UCELL							= 104;
    const SERVICE_UZMOBILE						= 108;

    const STATUS_TRANSACTION_SERVER_DOWN		= -4;		// TransactionServerIsDown
    const STATUS_DATABASE_ERROR					= -1;		// Error to access DB
    const STATUS_SUCCEEDED						= 0;		// Платеж проведен успешно
    const STATUS_PRECHECKING					= 3;		// Ведутся профилактические работы
    const STATUS_INCORRECT_AMOUNT				= 5;		// Неправильная сумма
    const STATUS_NOT_ENOUGH_DEPOSIT				= 7;		// Нет депозита
    const STATUS_SERVICE_TEMP_UNAVAILABLE		= 8;		// Провайдер недоступен
    const STATUS_INCORRECT_NUMBER				= 9;		// Не найден номер клиента
    const STATUS_ACCESS_DENIED					= 10;		// Доступ запрещен
    const STATUS_OWNER_UNIDENTIFIED				= 13;		// Невозможно определить владельца
    const STATUS_TIME_LIMIT						= 998;		// Ограничение по времени
    const STATUS_SERVICE_UNAVAILABLE			= 99999;	// Провайдер недоступен

    private $rootUrl = "https://client.paynet.uz:8443"; //without ending slash
    private $username;
    private $password;
    private $terminal;
    private $percent;
    private $cookieFile;
    private $curl;
    private $distributor;

    public function __construct($distributor)
    {
        $this->distributor = $distributor;

        $this->loadUserCredentials();
        $this->initCurl();
        libxml_use_internal_errors(true);
    }

    private function loadUserCredentials()
    {
        $sql = "SELECT username, password, terminal, percent FROM paynets WHERE user_id = :user_id AND enabled = 1 ORDER BY RAND() LIMIT 1";

        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(':user_id', $this->distributor->id);

        $row = $command->queryRow();
        if ($row) {
            $this->username = $row["username"];
            $this->password = $row["password"];
            $this->terminal = $row["terminal"];
            $this->percent = $row['percent'];
            Yii::log("account " . $this->username . " randomly selected", "info", "application.paynet");
        }
    }

    private function initCurl()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2) Gecko/20100115 MRA 5.6 (build 03278) Firefox/3.6");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, YiiBase::getPathOfAlias("application.runtime") . DIRECTORY_SEPARATOR . md5($this->username) . ".txt");
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, YiiBase::getPathOfAlias("application.runtime") . DIRECTORY_SEPARATOR . md5($this->username) . ".txt");
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_CONNECTIONTIMEOUT, 25);
    }

    private function login()
    {
        if ($this->username == '') {
            return false;
        }

        $url = "$this->rootUrl/PaymentServer?ACT=7&USERNAME=$this->username&PASSWORD=$this->password";
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);

        Yii::log("login response: \n" . $response, "info", "application.paynet");

        if ($response == false) {
            return false;
        }

        $xml = new DOMDocument();
        if (!$xml->loadXML($response)) {
            throw new Exception('Ошибка системы');
        }

        $xpath = new DOMXPath($xml);

        $succeeded = false;

        $status = $xpath->evaluate('/response/user_details/status');
        if (($status->length != 0) && ($status->item(0)->nodeValue == 0)) {
            $succeeded = true;
        }

        $status = $xpath->evaluate('/response/status');
        if (($status->length != 0) && ($status->item(0)->nodeValue == 1)) {
            $succeeded = true;
        }

        return $succeeded;
    }

    public function pay($id, $service, $number, $amount)
    {

//		return array (
//				'status' => 0,
//				'message' => 'Line 98',
//				'dateTime' => time(),
//				'id' => 17,
//				'fee' => (int) ($amount * $this->percent / 100)
//		);

        if ($this->login() == false) {
            Yii::log("authorization failed", "warning", "application.paynet");
            return array("status" => Paycell::STATUS_SERVER_ERROR, "message" => "Не удалось соединится с Paynet", "dateTime" => time(), "id" => -1);
        }

        switch ($service) {
            case Paycell::SERVICE_MTS:
                $service = self::SERVICE_MTS;
                break;
            case Paycell::SERVICE_BEELINE:
                $service = self::SERVICE_BEELINE;
                break;
            case Paycell::SERVICE_PERFECTUM_MOBILE:
                $service = self::SERVICE_PERFECTUM_MOBILE;
                break;
            case Paycell::SERVICE_UCELL:
                $service = self::SERVICE_UCELL;
                break;
            case Paycell::SERVICE_UZMOBILE:
                $service = self::SERVICE_UZMOBILE;
                break;
            default:
                return array("status" => Paycell::STATUS_SERVER_ERROR, "message" => "Получен неизвестный id провайдера", "dateTime" => time(), "id" => -1);
        }

        if (($service == self::SERVICE_PERFECTUM_MOBILE) || ($service == self::SERVICE_UZMOBILE)) {
            $number = substr($number, 2);
        }

        $url = "$this->rootUrl/PaymentServer?ACT=2&USERNAME=$this->username&PASSWORD=$this->password&TERMINAL_ID=$this->terminal&LANG=uz&EK_REQUEST_ID=$id&SERVICE_ID=$service&phone_number=$number&summa=$amount";
        curl_setopt($this->curl, CURLOPT_URL, $url);

        if (($response = curl_exec($this->curl)) == false) {
            return array("status" => Paycell::STATUS_SERVER_ERROR, "message" => "Не удалось соединится с Paynet", "dateTime" => time(), "id" => -1);
        }

        Yii::log("received response: \n" . $response, "info", "application.paynet");

        $xml = new DOMDocument();
        if (!$xml->loadXML($response)) {
            return array("status" => Paycell::STATUS_PRECHECKING, "message" => "Сервер Payneta перегружен", "dateTime" => time(), "id" => -1);
        }

        $xpath = new DOMXPath($xml);

        $status = $xpath->evaluate('/response/transaction/status');
        $message = $xpath->evaluate('/response/transaction/status_text');
        $transactionId = $xpath->evaluate('/response/transaction/receipt/transaction_id');

        if (($status->length == 0) || ($message->length == 0)) {
            return array("status" => Paycell::STATUS_PRECHECKING, "message" => "Сервер Payneta перегружен", "dateTime" => time(), "id" => -1);
        }

        $status = $status->item(0)->nodeValue;
        $message = $message->item(0)->nodeValue;

        if ($transactionId->length == 0) {
            $transactionId = -1;
        } else {
            $transactionId = $transactionId->item(0)->nodeValue;
        }

        $result = array(
            'status' => $status,
            'message' => $message,
            'dateTime' => time(),
            'id' => $transactionId,
            'fee' => (int)($amount * $this->percent / 100)
        );

        switch ($result['status']) {
            case self::STATUS_NOT_ENOUGH_DEPOSIT:
            case self::STATUS_ACCESS_DENIED:
            case self::STATUS_TIME_LIMIT:
                $result['status'] = Paycell::STATUS_SERVER_ERROR;
                break;

            case self::STATUS_DATABASE_ERROR:
                $result['status'] = Paycell::STATUS_SERVER_ERROR;
                break;

            case self::STATUS_INCORRECT_AMOUNT:
                $result['status'] = Paycell::STATUS_INCORRECT_AMOUNT;
                break;

            case self::STATUS_INCORRECT_NUMBER:
            case self::STATUS_OWNER_UNIDENTIFIED:
                $result['status'] = Paycell::STATUS_INCORRECT_NUMBER;
                break;

            case self::STATUS_SERVICE_UNAVAILABLE:
            case self::STATUS_SERVICE_TEMP_UNAVAILABLE:
            case self::STATUS_TRANSACTION_SERVER_DOWN:
                $result['status'] = Paycell::STATUS_PROVIDER_NOT_AVAILABLE;
                break;

            case self::STATUS_PRECHECKING:
                $result['status'] = Paycell::STATUS_PRECHECKING;
                break;

            case self::STATUS_SUCCEEDED:
                $result['status'] = Paycell::STATUS_TRANSACTION_ACCEPTED;
                break;

            default:
                $result['status'] = Paycell::STATUS_UNKNOWN_STATUS;
                break;
        }

        return $result;
    }
}

