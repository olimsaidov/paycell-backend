<?php $this->pageTitle = Yii::app()->name . ' - Изменение терминала'; ?>

<h3>Изменение терминала</h3>

<div class="form">
    <?php echo CHtml::beginForm() ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'password'); ?>
        <?php echo CHtml::activePasswordField($model, 'password'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Изменить'); ?>
        <?php echo CHtml::button('Отменить', array('onclick' => "history.go(-1)")); ?>
    </div>

    <?php echo CHtml::endForm() ?>

</div>
