<?php $this->pageTitle = Yii::app()->name . ' - Список диллеров' ?>

<h3>Список диллеров</h3>

<?php if (Yii::app()->user->hasFlash('distributor')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('distributor') ?>
    </div>
<?php endif ?>

<a style="float: right" href="<?php echo $this->createUrl('adddealer') ?>">Создать новый диллер</a>
<hr class="space"/>

<table class="list">
    <tr>
        <th>Логин</th>
        <th>Свободный объем</th>
        <th>Остаток</th>
        <th>Терминалы</th>
        <th style="width: 100px">Управление</th>
    </tr>

    <?php foreach ($model as $dealer): ?>
        <tr>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $dealer)) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($dealer->deposit) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($dealer->reminder) ?></td>
            <td><?php echo $dealer->childrenAmount ?></td>
            <td>
                <?php
                echo CHtml::link("", array('createtransfer', 'to' => $dealer->id), array('class' => 'enrich_user', 'title' => 'Изменить депозит'));
                echo CHtml::link("", array('editdealer', 'id' => $dealer->id), array('class' => 'edit_user', 'title' => 'Изменить'));
                if ($dealer->enabled) {
                    echo CHtml::link("", array('disabledealer', 'id' => $dealer->id), array('class' => 'disable_user', 'title' => 'Отключить'));
                } else {
                    echo CHtml::link("", array('enabledealer', 'id' => $dealer->id), array('class' => 'enable_user', 'title' => 'Включить'));
                }
                echo CHtml::link("", array('deletedealer', 'id' => $dealer->id), array('class' => 'delete_user', 'title' => 'Удалить', 'onclick' => "if (!confirm('Вы точно хотите удалить диллер $dealer->username?')) return false;"));
                ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>
