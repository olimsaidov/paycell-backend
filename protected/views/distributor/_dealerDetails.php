<?php echo CHtml::link("Изменить данные", array('editdealer', 'id' => $model->id), array('title' => 'Изменить', 'style' => 'float: right')) ?>
<hr class="space"/>

<table id="details" class="details">
    <tr>
        <td style="width: 140px"><b>Логин</b></td>
        <td style="width: 250px"><?php echo $model->username ?></td>
        <td style="width: 140px"><b>Адрес</b></td>
        <td><?php echo $model->address ?></td>
    </tr>
    <tr>
        <td><b>Состояние</b></td>
        <td><?php echo $model->enabled ? 'Включен' : 'Отключен' ?></td>
        <td><b>ИНН</b></td>
        <td><?php echo $model->inn ?></td>
    </tr>
    <tr>
        <td><b>Свободный объем</b></td>
        <td><?php echo Yii::app()->currencyFormatter->format($model->deposit) ?> сумов</td>
        <td><b>Расчетный счет</b></td>
        <td><?php echo $model->rs ?></td>
    </tr>
    <tr>
        <td><b>Остаток</b></td>
        <td><?php echo Yii::app()->currencyFormatter->format($model->reminder) ?> сумов</td>
        <td><b>Спецсчет</b></td>
        <td><?php echo $model->ss ?></td>
    </tr>
    <tr>
        <td><b>Терминалы</b></td>
        <td><?php echo $model->childrenAmount ?></td>
        <td><b>Банк</b></td>
        <td><?php echo $model->bs ?></td>
    </tr>
    <tr>
        <td><b>Фамилия Имя</b></td>
        <td><?php echo $model->second_name . ' ' . $model->first_name ?></td>
        <td><b>МФО</b></td>
        <td><?php echo $model->mfo ?></td>
    </tr>
    <tr>
        <td><b>Организация</b></td>
        <td><?php echo $model->organization ?></td>
        <td><b>ОКОНХ</b></td>
        <td><?php echo $model->okonx ?></td>
    </tr>
    <tr>
        <td><b>Телефон</b></td>
        <td><?php echo $model->telephone . ($model->alternate_telephone ? ', ' . $model->alternate_telephone : '') ?></td>
        <td><b>Комментарии</b></td>
        <td><?php echo $model->comments ?></td>
    </tr>
</table>
