<?php $this->pageTitle = Yii::app()->name . ' - Новый денежный перевод'; ?>

<?php Yii::app()->clientScript->registerScriptFile('/js/jquery.formatcurrency.js', CClientScript::POS_HEAD) ?>
<?php Yii::app()->clientScript->registerScript('depositCalculator', "

	$('#deposit').data('originDeposit', $('#deposit').val());
	$('#deposit').formatCurrency();
	if ($('#reminder')) {
		$('#reminder').formatCurrency();
	}

	$('#amount').keyup(function() {
		$('#amount').formatCurrency();
		var value = parseInt($('#amount').val().replace(/ /g, ''), 10)
		if (isNaN(value)) value = 0;
		$('#deposit').val(parseInt($('#deposit').data('originDeposit'), 10) + value);
		$('#deposit').formatCurrency();
	});

	$('#submitButton').click(function() {
		var value = parseInt($('#amount').val().replace(/ /g, ''), 10)
		if (isNaN(value)) value = 0;
		$('#amount').val(value);
	});

", CClientScript::POS_READY);
?>

<h3>Новый денежный перевод</h3>

<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>

    <div class="row">
        <label>Кому</label>
        <span style="line-height: 32px"><b><?php echo ($model->receiver->role == User::ROLE_DEALER ? 'Диллер' : 'Терминал') . ' ' . $model->receiver->username . ' (' . ($model->receiver->role == User::ROLE_DEALER ? $model->receiver->organization : $model->receiver->id) . ')' ?></b></span>
    </div>

    <?php if ($model->receiver->role == User::ROLE_DEALER): ?>
        <div class="row">
            <?php echo CHtml::activeLabelEx($model->receiver, "reminder"); ?>
            <?php echo CHtml::activeTextField($model->receiver, "reminder", array("disabled" => "disabled")); ?>
        </div>
    <?php endif ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model->receiver, "deposit"); ?>
        <?php echo CHtml::activeTextField($model->receiver, "deposit", array("disabled" => "disabled", 'id' => 'deposit')); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'amount'); ?>
        <?php echo CHtml::activeTextField($model, 'amount', array('id' => 'amount')); ?>
    </div>

    <div class="row memo">
        <?php echo CHtml::activeLabelEx($model, 'comments'); ?>
        <?php echo CHtml::activeTextArea($model, 'comments'); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Отправить', array('id' => 'submitButton')); ?>
        <?php echo CHtml::button('Отменить', array('onclick' => "history.go(-1)")) ?>
    </div>
</div>
