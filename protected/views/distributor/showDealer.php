<?php $this->pageTitle = Yii::app()->name . ' - Подробная информация о диллере'; ?>

    <h3>Диллер</h3>

<?php Yii::app()->clientScript->registerScript('cellFiller', "
	$('#details td').filter(function(index, item) {
		return item.innerHTML == '';
	}).append($('<span class=\"quiet\">нет данных</span>'));
	
", CClientScript::POS_READY);
?>

    <h3></h3>

<?php if (Yii::app()->user->hasFlash('distributor')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('distributor') ?>
    </div>
<?php endif ?>

<?php $this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs' => array(
        'Данные о диллере' => $this->renderPartial('_dealerDetails', array('model' => $model), true),
        'Список терминалов диллера' => $this->renderPartial('_dealerTerminals', array('model' => $model), true),
    )
));
?>
