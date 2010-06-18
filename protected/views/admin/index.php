<?php $this->pageTitle = Yii::app()->name . ' - Список дистрибюторов' ?>

<h3>Список дистрибюторов</h3>

<?php if (Yii::app()->user->hasFlash('admin')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('admin') ?>
    </div>
<?php endif ?>

<a style="float: right" href="<?php echo $this->createUrl('add') ?>">Создать новый дистрибютор</a>
<hr class="space"/>

<table class="list">
    <tr>
        <th>Логин</th>
        <th>Свободный объем</th>
        <th>Остаток</th>
        <th>Диллеры</th>
        <th style="width: 100px">Управление</th>
    </tr>

    <?php foreach ($model as $distributor): ?>
        <tr>
            <td><?php $this->widget('application.widgets.UserDetails', array('model' => $distributor)) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($distributor->deposit) ?></td>
            <td><?php echo Yii::app()->currencyFormatter->format($distributor->reminder) ?></td>
            <td><?php echo $distributor->childrenAmount ?></td>
            <td>
                <?php
                echo CHtml::link("", array('createtransfer', 'to' => $distributor->id), array('class' => 'enrich_user', 'title' => 'Изменить депозит'));
                echo CHtml::link("", array('edit', 'id' => $distributor->id), array('class' => 'edit_user', 'title' => 'Изменить'));
                if ($distributor->enabled) {
                    echo CHtml::link("", array('disable', 'id' => $distributor->id), array('class' => 'disable_user', 'title' => 'Отключить'));
                } else {
                    echo CHtml::link("", array('enable', 'id' => $distributor->id), array('class' => 'enable_user', 'title' => 'Включить'));
                }
                echo CHtml::link("", array('delete', 'id' => $distributor->id), array('class' => 'delete_user', 'title' => 'Удалить', 'onclick' => "if (!confirm('Вы точно хотите удалить дистрибютор $distributor->username?')) return false;"));
                ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>

