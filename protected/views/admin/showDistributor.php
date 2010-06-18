<?php $this->pageTitle = Yii::app()->name . ' - Подробная информация о дистрибюторе'; ?>

<?php Yii::app()->clientScript->registerScript('cellFiller', "
	$('#details td').filter(function(index, item) {
		return item.innerHTML == '';
	}).append($('<span class=\"quiet\">нет данных</span>'));
	
", CClientScript::POS_READY);
?>

    <h3>Дистрибютор</h3>

<?php $this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs' => array(
        'Данные о дистрибюторе' => $this->renderPartial('_distributorDetails', array('model' => $model), true),
        'Список диллеров дистрибютора' => $this->renderPartial('_distributorDealers', array('model' => $model), true),
    )
)) ?>
