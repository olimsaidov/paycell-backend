<?php $this->pageTitle = Yii::app()->name . ' - Управление сервисами'; ?>

    <h3>Управление сервисами</h3>

<?php if (Yii::app()->user->hasFlash('service')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('service'); ?>
    </div>
<?php endif; ?>

<?php echo CHtml::beginForm() ?>
    <table class="list">
        <tr>
            <th>Имя сервиса</th>
            <th>Шлюз</th>
        </tr>
        <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo $service->humanName ?></td>
                <td>
                    <select name="gateway[<?php echo $service->id ?>]">
                        <option <?php echo $service->gateway == 'Paynet' ? 'selected="selected"' : '' ?>>Paynet</option>
                        <option <?php echo $service->gateway == 'Samonline' ? 'selected="selected"' : '' ?>>Samonline
                        </option>
                        <option <?php echo $service->gateway == 'Off' ? 'selected="selected"' : '' ?> value="Off">
                            Отключен
                        </option>
                    </select>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php echo CHtml::submitButton('Сохранить'); ?>
<?php echo CHtml::endForm() ?>
