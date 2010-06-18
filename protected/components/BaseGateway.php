<?php

class BaseGateway
{

    public function pay($id, $service, $number, $amount, $distributor)
    {
        $service = Service::model()->findByPk($service);

        if ($service == null) {
            return array(
                "status" => Paycell::STATUS_SERVER_ERROR,
                "message" => "Не удалось определить шлюз для сервиса. Сервис не сущесвует.",
                "dateTime" => time(),
                "id" => -1
            );
        }

        $class = $service->gateway;

        if ($class == 'Off') {
            return array(
                "status" => Paycell::STATUS_PROVIDER_NOT_AVAILABLE,
                "message" => "Сервис отлючен",
                "dateTime" => time(),
                "id" => -1
            );
        }

        $_amount = floor($amount * (100 - SamonlineGateway::PERCENT) / 100);

        if (($_amount < 1000) && ($class != 'Off')) {
            $class = 'Paynet';
        }

        $class .= 'Gateway';
        $gateway = null;

        if ($class == 'PaynetGateway') {
            $gateway = new $class($distributor);
        } else {
            $gateway = new $class();
        }

        return $gateway->pay($id, $service->id, $number, $amount);
    }
}

