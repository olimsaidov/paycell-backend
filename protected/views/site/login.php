<?php
$this->pageTitle = Yii::app()->name . ' - Вход';
?>

<h1>Вход</h1>

<p>Пожалуйста, введите ваши данные для авторизации:</p>

<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'username'); ?>
        <?php echo CHtml::activeTextField($model, 'username'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'password'); ?>
        <?php echo CHtml::activePasswordField($model, 'password'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Вход'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->
