<?php $this->pageTitle = Yii::app()->name . ' - Список терминалов'; ?>

<h3>Список терминалов</h3>

<?php if (Yii::app()->user->hasFlash('distributor')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('distributor') ?>
    </div>
<?php endif ?>

<?php

echo CHtml::beginForm();
echo 'Терминал или логин ';
echo CHtml::textField('username', $username);
echo ' ' . CHtml::submitButton('Показать');
echo CHtml::endForm();

?>

<hr class="space"/>

<table class="list">
    <tr>
        <th>Логин / Терминал</th>
        <th>Депозит</th>
        <th>Диллер</th>
        <th style="width: 100px">Управление</th>
    </tr>

    <?php foreach ($model as $terminal): ?>
        <tr>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $terminal)) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($terminal->deposit) ?></td>
            <td><?php $this->widget('application.widget.UserDetails', array('model' => $terminal->parent)) ?></td>
            <td>
                <?php
                echo CHtml::link("", array('createtransfer', 'to' => $terminal->id), array('class' => 'enrich_user', 'title' => 'Изменить депозит'));
                echo CHtml::link("", array('editterminal', 'id' => $terminal->id), array('class' => 'edit_user', 'title' => 'Изменить'));
                echo $terminal->enabled ? CHtml::link("", array('disableterminal', 'id' => $terminal->id), array('class' => 'disable_user', 'title' => 'Отключить')) : CHtml::link("", array('enableterminal', 'id' => $terminal->id), array('class' => 'enable_user', 'title' => 'Включить'));
                echo CHtml::link("", array('deleteterminal', 'id' => $terminal->id), array('class' => 'delete_user', 'title' => 'Удалить', 'onclick' => "if (!confirm('Вы точно хотите удалить дистрибютор $terminal->username?')) return false;"));
                ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>
