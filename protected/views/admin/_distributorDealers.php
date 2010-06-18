<table class="list">
    <tr>
        <th>Логин</th>
        <th>Огранизация</th>
        <th>Состояние</th>
        <th>Свободный объем</th>
        <th>Остаток</th>
        <th>Терминалы</th>
    </tr>

    <?php foreach ($model->children as $dealer): ?>
        <tr>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $dealer)) ?></td>
            <td><?php echo $dealer->organization ?></td>
            <td><?php echo $dealer->enabled ? 'Включен' : 'Отключен' ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($dealer->deposit) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($dealer->reminder) ?></td>
            <td><?php echo $dealer->childrenAmount ?></td>
        </tr>
    <?php endforeach ?>
</table>
