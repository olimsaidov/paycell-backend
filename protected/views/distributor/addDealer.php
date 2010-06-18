<?php $this->pageTitle = Yii::app()->name . ' - Новый диллер'; ?>

<h3>Добавление нового диллера</h3>

<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>

    <fieldset title="Основные данные">
        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'username'); ?>
            <?php echo CHtml::activeTextField($model, 'username'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'password'); ?>
            <?php echo CHtml::activeTextField($model, 'password'); ?>
        </div>

    </fieldset>

    <fieldset title="Дополнительные данные" class="extended-info">
        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'first_name'); ?>
            <?php echo CHtml::activeTextField($model, 'first_name'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'second_name'); ?>
            <?php echo CHtml::activeTextField($model, 'second_name'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'organization'); ?>
            <?php echo CHtml::activeTextField($model, 'organization'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'address'); ?>
            <?php echo CHtml::activeTextField($model, 'address'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'telephone'); ?>
            <?php echo CHtml::activeTextField($model, 'telephone'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'alternate_telephone'); ?>
            <?php echo CHtml::activeTextField($model, 'alternate_telephone'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'inn'); ?>
            <?php echo CHtml::activeTextField($model, 'inn'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'rs'); ?>
            <?php echo CHtml::activeTextField($model, 'rs'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'ss'); ?>
            <?php echo CHtml::activeTextField($model, 'ss'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'bs'); ?>
            <?php echo CHtml::activeTextField($model, 'bs'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'mfo'); ?>
            <?php echo CHtml::activeTextField($model, 'mfo'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'okonx'); ?>
            <?php echo CHtml::activeTextField($model, 'okonx'); ?>
        </div>

        <div class="row memo">
            <?php echo CHtml::activeLabelEx($model, 'comments'); ?>
            <?php echo CHtml::activeTextArea($model, 'comments'); ?>
        </div>
    </fieldset>

    <div class="row submit">
        <?php echo CHtml::submitButton('Добавить'); ?>
        <?php echo CHtml::button('Отменить', array('onclick' => "history.go(-1)")); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>
