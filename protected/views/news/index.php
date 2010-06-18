<?php $this->pageTitle = Yii::app()->name . ' - Список новостей'; ?>

<h3>Список новостей</h3>

<?php if (Yii::app()->user->hasFlash('news')): ?>
    <div class="success">
        <?php echo Yii::app()->user->getFlash('news'); ?>
    </div>
<?php endif; ?>

<?php echo CHtml::link("Добавить", array('add'), array('style' => 'float: right')) ?>
<hr class="space"/>

<?php foreach ($model as $news): ?>
    <div class="news">
        <h4><?php echo $news->title; ?></h4>
        <div class="date"><?php echo $news->dateTime; ?></div>
        <div class="text"><?php echo $news->text; ?></div>
        <div class="links">
            <?php echo CHtml::link("Редактировать", array('edit', "id" => $news->id)) ?> |
            <?php echo CHtml::link("Удалить", array('delete', "id" => $news->id)) ?>
        </div>
    </div>
<?php endforeach; ?>
