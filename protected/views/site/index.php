<?php
$this->pageTitle = Yii::app()->name;
?>

<h1>Добро пожаловать</h1>
<p>Мы рады видеть вас на нашем сайте.</p>

<table border="0">
    <tr>
        <td style="width: 50%">
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/logo128.jpg"
                 style="float: left; margin: 10px; width: 115px; padding-bottom: 40px"/>
            <h2>Paycell Client 1.2.1</h2>
            <p>Клиентская программа для проведения платежей через системы PayCell.</p>
            <p>Размер: 701 КБ<br/><a
                        href="<?php echo Yii::app()->request->baseUrl; ?>/downloads/PayCell_Client_Setup.rar">Скачать</a>
            </p>
        </td>
        <td>
            <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/air_logo.jpg"
                 style="float: left; margin: 10px; padding-bottom: 40px"/>
            <h2>Adobe AIR 2.0.2</h2>
            <p>Adobe AIR позволяет пользоваться вашими любимыми веб-приложениями в любое время и в любом месте.</p>
            <p>Размер: 11.56 MB<br/><a
                        href="http://airdownload.adobe.com/air/win/download/latest/AdobeAIRInstaller.exe">Скачать</a>
            </p>
        </td>
    </tr>
</table>
