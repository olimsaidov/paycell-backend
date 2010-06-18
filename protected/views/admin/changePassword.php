<?php $this->pageTitle = Yii::app()->name . ' - Изменение пароля'; ?>

<h3>Изменение пароля</h3>

<div class="form">
    <?php echo CHtml::beginForm() ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'password'); ?>
        <?php echo CHtml::activePasswordField($model, 'password'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'second_password'); ?>
        <?php echo CHtml::activePasswordField($model, 'second_password'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Изменить'); ?>
        <?php echo CHtml::button('Отменить', array('onclick' => "history.go(-1)")); ?>
    </div>

    <?php echo CHtml::endForm() ?>

</div>
