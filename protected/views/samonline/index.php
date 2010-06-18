<?php $this->pageTitle = Yii::app()->name . ' - Список Samonline терминалов'; ?>

<h3>Список Samonline терминалов</h3>

<?php if (Yii::app()->user->hasFlash('samonline')) { ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('samonline'); ?>
    </div>
<?php } ?>

<a style="float: right" href="<?php echo $this->createUrl('add'); ?>">Создать новый Samonline терминал</a>
<hr class="space"/>

<table class="list">
    <tr>
        <th>Имя пользователя</th>
        <th>Пароль</th>
        <th>Управление</th>
    </tr>

    <?php foreach ($model as $samonline) { ?>
        <tr>
            <td><?php echo $samonline->username; ?></td>
            <td><?php echo $samonline->password; ?></td>
            <td style="width: 120px; text-align: center">
                <a href="<?php echo $this->createUrl('edit', array('id' => $samonline->id)); ?>">Изменить</a> |
                <a href="<?php echo $this->createUrl('delete', array('id' => $samonline->id)); ?>"
                   onclick="javascript: if (!confirm('Вы точно хотите удалить Samonline терминал <?php echo $samonline->username; ?>?')) return false;">Удалить</a>
            </td>
        </tr>
    <?php } ?>
</table>

