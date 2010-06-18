<?php $this->pageTitle = Yii::app()->name . ' - Новый Samonline терминал'; ?>

<h3>Добавление нового Samonline терминала</h3>

<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'username'); ?>
        <?php echo CHtml::activeTextField($model, 'username'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'password'); ?>
        <?php echo CHtml::activeTextField($model, 'password'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Добавить'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>
