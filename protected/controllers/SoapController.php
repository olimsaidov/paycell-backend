<?php

class SoapController extends CController
{
    private $_server;
    private $_rawPostData;
    private $_paycellInstance;

    public function actionIndex()
    {
        $this->_server = new SoapServer(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'definition.wsdl');
        $this->_rawPostData = file_get_contents("php://input");
        $this->_paycellInstance = new Paycell($this->_rawPostData);
        $this->_server->setObject($this->_paycellInstance);
        $this->_server->handle($this->_rawPostData);
    }

    public function actionWsdl()
    {
        header('Content-type: text/xml; charset="utf-8"');
        echo file_get_contents(Yii::getPathOfAlias('application.data') . DIRECTORY_SEPARATOR . 'definition.wsdl');
    }

}
