<?php $this->beginContent('/layouts/main'); ?>

<div class="submenu">
    <ul>
        <li><a href="<?php echo $this->createUrl('distributor/index'); ?>">Диллеры</a></li>
        <li><?php echo CHtml::link('Терминалы', array('distributor/showterminals')) ?></li>
        <li><a href="<?php echo $this->createUrl('distributor/showtransfers'); ?>">История денежных переводов</a></li>
        <li><?php echo CHtml::link('Транзакции', array('transaction/index')) ?></li>
        <li><?php echo CHtml::link('Изменить пароль', array('distributor/changepassword')) ?></li>
    </ul>
</div>

<?php $model = User::model()->findByPk(Yii::app()->user->id) ?>
<?php $this->beginWidget('system.web.widgets.CClipWidget', array('id' => 'userinfo')); ?>
<table>
    <tr>
        <td><b>Логин</b></td>
        <td><?php echo $model->username ?></td>
    </tr>
    <tr>
        <td><b>Свободный объем</b></td>
        <td><?php echo Yii::app()->currencyFormatter->format($model->deposit) ?> сумов</td>
    </tr>
    <tr>
        <td><b>Остаток</b></td>
        <td><?php echo Yii::app()->currencyFormatter->format($model->reminder) ?> сумов</td>
    </tr>
</table>
<?php $this->endWidget('system.web.widgets.CClipWidget'); ?>

<hr/>
<?php echo $content; ?>

<?php $this->endContent(); ?>
