<?php $this->pageTitle = Yii::app()->name . ' - Транзакции'; ?>

    <h3>Транзакции</h3>

    <!--<?php echo CHtml::beginForm(); ?>

<table>
	<tr>
		<td style="width: 100px;">Дата</td>
		<td>от <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => 'dateFrom',
        // additional javascript options for the date picker plugin
        'options' => array(
            'showAnim' => 'fade',
            'dateFormat' => 'yy.mm.dd'
        ),
        'htmlOptions' => array(
            'style' => 'width: 70px;'
        ),
        'language' => 'ru',
        'value' => $dateFrom
    )); ?> до <?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => 'dateTo',
        // additional javascript options for the date picker plugin
        'options' => array(
            'showAnim' => 'fade',
            'dateFormat' => 'yy.mm.dd'
        ),
        'htmlOptions' => array(
            'style' => 'width: 70px;'
        ),
        'language' => 'ru',
        'value' => $dateTo
    )); ?></td>
	</tr>
	<tr>
		<td>Номер</td>
		<td><?php echo CHtml::textField('number', $number); ?></td>
	</tr>
	<tr>
		<td>Терминал</td>
		<td><?php echo CHtml::textField('terminal', $terminal); ?></td>
	</tr>
	<tr>
		
	</tr>
	<tr>
		<td></td>
		<td><?php echo CHtml::submitButton('Показать') ?></td>
	</tr>
</table>-->

<?php

function numberFormatter($number)
{
    $matches = array();
    preg_match('/(\d\d)(\d\d\d)(\d\d)(\d\d)/', $number, $matches);
    return $matches[1] . ' ' . $matches[2] . '-' . $matches[3] . '-' . $matches[4];
}

function serviceFormatter($service)
{
    switch ($service) {
        case 1:
            return 'МТС';
        case 2:
            return 'Билайн';
        case 3:
            return 'UCell';
        case 4:
            return 'Perfectum Mobile';
        case 5:
            return 'UZMobile';
    }
}

function statusFormatter($status)
{
    switch ($status) {
        case 0:
            return 'Проплачен';
        case -1:
            return 'В обработке';
        case 1:
            return 'Оплата не произведена';
        case 3:
            return 'Номер не найден';
        case 4:
            return 'Недостаточно депозита';
        case 5:
            return 'Провайдер не доступен';
        case 6:
            return 'Ошибка системы';
        case 10:
            return 'Ведутся профилактические работы';
        case 11:
            return 'Неправильная сумма';
        case 12:
            return 'Терминал не существует';
        case 13:
            return 'Доступ запрещен';
    }
}

?>

    <table class="list">
        <tr>
            <th>Терминал</th>
            <th>Провайдер</th>
            <th>Номер</th>
            <th>Сумма</th>
            <th>Статус</th>
            <th>Время</th>
        </tr>

        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php $this->widget('application.widgets.UserDetails', array('model' => $transaction->user)) ?></td>
                <td><?php echo serviceFormatter($transaction->service) ?></td>
                <td><?php echo numberFormatter($transaction->number) ?></td>
                <td><?php echo Yii::app()->currencyFormatter->format($transaction->amount) ?></td>
                <td><?php echo statusFormatter($transaction->status) ?></td>
                <td><?php echo $transaction->dateTime ?></td>
            </tr>
        <?php endforeach ?>
    </table>

<?php $this->widget('CLinkPager', array('pages' => $paginator)); ?>
