<?php

class SamonlineGateway
{

    const PERCENT = 2;

    const OPERATOR_MTS = 1;
    const OPERATOR_UCELL = 2;
    const OPERATOR_BEELINE = 3;
    const OPERATOR_PERFECTUM = 5;

    const STATUS_SUCCEEDED = 0;

    private $username = '';
    private $password = '';

    private $cookieFile;
    private $curl;
    private $rootUrl = 'https://srv.samonline.uz/?';

    public function __construct()
    {
        $this->loadUserCredentials();
        $this->cookieFile = YiiBase::getPathOfAlias("application.runtime") . DIRECTORY_SEPARATOR . md5($this->username) . ".txt";
        $this->initCurl();
    }

    private function loadUserCredentials()
    {
        $sql = "SELECT username, password FROM samonlines ORDER BY RAND()";
        $command = Yii::app()->db->createCommand($sql);
        $row = $command->queryRow();
        $this->username = $row["username"];
        $this->password = $row["password"];
        Yii::log("account " . $this->username . " randomly selected", "info", "application.paynet");
    }

    private function initCurl()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2) Gecko/20100115 MRA 5.6 (build 03278) Firefox/3.6");
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function pay($id, $service, $number, $amount)
    {

        $number = '998' . $number;

        $status = 0;

        switch ($service) {
            case Paycell::SERVICE_MTS:
                $service = self::OPERATOR_MTS;
                break;
            case Paycell::SERVICE_BEELINE:
                $service = self::OPERATOR_BEELINE;
                break;
            case Paycell::SERVICE_UCELL:
                $service = self::OPERATOR_UCELL;
                break;
            case Paycell::SERVICE_PERFECTUM_MOBILE:
                $service = self::OPERATOR_PERFECTUM;
                break;
            default:
                return array("status" => Paycell::STATUS_PROVIDER_NOT_AVAILABLE, "message" => "Оператор не доступен", "dateTime" => time(), "id" => -1);
        }


        $amount = floor($amount * (100 - SamonlineGateway::PERCENT) / 100);

        if ($amount < 1000) {
            return array("status" => Paycell::STATUS_INCORRECT_AMOUNT, "message" => "Неправилная сумма", "dateTime" => time(), "id" => -1);
            return;
        }

        $url = "{$this->rootUrl}login={$this->username}&pass={$this->password}&act=1&usluga={$service}&summa={$amount}&abon={$number}&v=3";
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $response = curl_exec($this->curl);
        if ($response == false) {
            return array("status" => Paycell::STATUS_SERVER_ERROR, "message" => "Не удалось соеденится с SamOnline", "dateTime" => time(), "id" => -1);
        }

        $xml = new DOMDocument();
        $xml->loadXML($response);
        $xpath = new DOMXPath($xml);

        Yii::log("transaction repsponse: \n" . $response, 'info', 'application.samonline');

        $status = $xpath->evaluate('/result/code')->item(0)->nodeValue;
        $message = $xpath->evaluate('/result/msg')->item(0)->nodeValue;

        $status = $status == self::STATUS_SUCCEEDED ? Paycell::STATUS_TRANSACTION_ACCEPTED : Paycell::STATUS_TRANSACTION_NOT_EXISTS;

        return array("status" => $status, "message" => $message, "dateTime" => time(), "id" => -1);
    }
}
