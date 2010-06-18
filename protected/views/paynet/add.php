<?php $this->pageTitle = Yii::app()->name . ' - Новый Paynet терминал'; ?>

<h3>Добавление нового Paynet терминала</h3>

<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'username'); ?>
        <?php echo CHtml::activeTextField($model, 'username'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'terminal'); ?>
        <?php echo CHtml::activeTextField($model, 'terminal'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'password'); ?>
        <?php echo CHtml::activeTextField($model, 'password'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'percent'); ?>
        <?php echo CHtml::activeTextField($model, 'percent'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'user_id'); ?>
        <?php echo CHtml::activeDropDownList($model, 'user_id', CHtml::listData($distributors, 'id', 'username')); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Добавить'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>
