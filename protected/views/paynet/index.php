<?php $this->pageTitle = Yii::app()->name . ' - Список Paynet терминалов'; ?>

<h3>Список Paynet терминалов</h3>

<?php if (Yii::app()->user->hasFlash('paynet')) { ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('paynet'); ?>
    </div>
<?php } ?>

<a style="float: right" href="<?php echo $this->createUrl('add'); ?>">Создать новый Paynet терминал</a>
<hr class="space"/>

<table class="list">
    <tr>
        <th>Имя пользователя</th>
        <th>Пароль</th>
        <th>Терминал</th>
        <th>Процент</th>
        <th>Дистрибютор</th>
        <th style="width: 80px">Управление</th>
    </tr>

    <?php foreach ($model as $paynet) { ?>
        <tr>
            <td><?php echo $paynet->username; ?></td>
            <td><?php echo $paynet->password; ?></td>
            <td><?php echo $paynet->terminal; ?></td>
            <td><?php echo $paynet->percent; ?></td>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $paynet->distributor)) ?></td>
            <td>
                <?php
                echo CHtml::link("", array('edit', 'id' => $paynet->id), array('class' => 'edit_paynet', 'title' => 'Изменить'));
                if ($paynet->enabled) {
                    echo CHtml::link("", array('disable', 'id' => $paynet->id), array('class' => 'disable_user', 'title' => 'Отключить'));
                } else {
                    echo CHtml::link("", array('enable', 'id' => $paynet->id), array('class' => 'enable_user', 'title' => 'Включить'));
                }
                echo CHtml::link("", array('delete', 'id' => $paynet->id), array('class' => 'delete_paynet', 'onclick' => "javascript: if (!confirm('Вы точно хотите удалить этот Paynet терминал?')) return false;", 'title' => 'Удалить'));
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

