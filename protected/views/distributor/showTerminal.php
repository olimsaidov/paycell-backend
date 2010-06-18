<?php $this->pageTitle = Yii::app()->name . ' - Список терминалов'; ?>

<h3>Терминал</h3>

<?php echo CHtml::link("Изменить данные", array('editterminal', 'id' => $model->id), array('title' => 'Изменить', 'style' => 'float: right')) ?>
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
        <td><b>Депозит</b></td>
        <td><?php echo Yii::app()->currencyFormatter->format($model->deposit) ?> сумов</td>
        <td><b>Расчетный счет</b></td>
        <td><?php echo $model->rs ?></td>
    </tr>
    <tr>
        <td><b>Диллер</b></td>
        <td><?php $this->widget('application.widgets.UserDetails', array('model' => $model->parent)) ?></td>
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
        <td><b>Спецсчет</b></td>
        <td><?php echo $model->ss ?></td>
    </tr>
    <tr>
        <td><b>Комментарии</b></td>
        <td><?php echo $model->comments ?></td>
    </tr>
</table>
