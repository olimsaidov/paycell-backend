<?php $this->pageTitle = Yii::app()->name . ' - История денежных переводов'; ?>

<h3>История денежных переводов</h3>

<?php if (Yii::app()->user->hasFlash('distributor')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('distributor') ?>
    </div>
<?php endif ?>


<?php

echo CHtml::beginForm();
echo 'Показать от ';

$this->widget('zii.widgets.jui.CJuiDatePicker', array(
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
));

echo ' до ';

$this->widget('zii.widgets.jui.CJuiDatePicker', array(
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
));

echo ' ' . CHtml::submitButton('Показать');
echo CHtml::endForm();

?>

<hr class="space"/>

<table class="list">
    <tr>
        <th style="width: 40px">ID</th>
        <th>Отправитель</th>
        <th>Получатель</th>
        <th>Сумма</th>
        <th style="width: 120px">Дата</th>
        <th>Комментарии</th>
        <th style="width: 50px;">Отмена</th>
    </tr>

    <?php $sum = 0 ?>
    <?php foreach ($model as $transfer): ?>
        <tr>
            <td style="text-align: center"><?php echo $transfer->id ?></td>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $transfer->sender)) ?></td>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $transfer->receiver)) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($transfer->amount) ?></td>
            <?php $sum += $transfer->amount; ?>
            <td><?php echo $transfer->dateTime ?></td>
            <td><?php echo $transfer->comments ?></td>
            <td><?php echo CHtml::link("", $this->createUrl('canceltransfer', array('id' => $transfer->id)), array('class' => 'cancel_transfer', 'title' => 'Отменить')) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<div>
    Общая сумма: <b><?php echo $sum; ?> сумов</b>
</div>
