<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="language" content="en"/>

    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css"
          media="screen, projection"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css"
          media="print"/>
    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css"
          media="screen, projection"/>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css"/>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

    <div id="header">
        <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
        <div id="userinfo"><?php echo Yii::app()->controller->clips['userinfo']; ?></div>
        <hr class="space"/>
    </div><!-- header -->

    <div id="mainmenu">
        <?php $this->widget('zii.widgets.CMenu', array(
            'items' => array(
                array('label' => 'Главная', 'url' => array('/site')),
                array('label' => 'Администрация', 'url' => array(
                    Yii::app()->user->role == User::ROLE_ADMINISTRATOR ? '/admin' :
                        (Yii::app()->user->role == User::ROLE_DISTRIBUTOR ? '/distributor' :
                            (Yii::app()->user->role == User::ROLE_DEALER ? '/dealer' : 'index')))),
                array('label' => 'О нас', 'url' => array('/site/page', 'view' => 'about')),
                array('label' => 'Вход', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest),
                array('label' => 'Выход', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest)
            ),
        )); ?>
    </div><!-- mainmenu -->

    <div id="content">
        <?php echo $content; ?>
    </div><!-- content -->

    <div id="footer">
        Copyright &copy; <?php echo date('Y'); ?>. PayCell.<br/>
        Все права защищены. <br/>
    </div><!-- footer -->

</div><!-- page -->

</body>
</html>
