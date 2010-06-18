<?php $this->beginContent('/layouts/main'); ?>

    <div class="submenu">
        <ul>
            <li><a href="<?php echo $this->createUrl('admin/index'); ?>">Дистрибюторы</a></li>
            <li><a href="<?php echo $this->createUrl('paynet/index'); ?>">Paynet терминалы</a></li>
            <!--<li><a href="<?php echo $this->createUrl('samonline/index'); ?>">Samonline терминалы</a></li>-->
            <li><?php echo CHtml::link('Денежные переводы', array('admin/showtransfers')) ?></li>
            <li><?php echo CHtml::link('Транзакции', array('transaction/index')) ?></li>
            <li><a href="<?php echo $this->createUrl('news/index'); ?>">Новости</a></li>
            <li><?php echo CHtml::link('Шлюзы', array('service/index')) ?></li>
            <li><?php echo CHtml::link('Изменить пароль', array('admin/changepassword')) ?></li>
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
