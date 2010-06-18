<?php $this->pageTitle = Yii::app()->name . ' - Редактирование новостей'; ?>

<h3>Редактирование новостей</h3>
<div class="form">
    <?php echo CHtml::beginForm(); ?>

    <?php echo CHtml::errorSummary($model); ?>


    <div class="row">
        <?php echo CHtml::activeLabelEx($model, 'title'); ?>
        <?php echo CHtml::activeTextField($model, 'title', array('style' => 'width: 770px')); ?>
    </div>

    <div class="row tinymce">
        <?php $this->widget('application.extensions.tinymce.ETinyMce', array(
            'model' => $model,
            'attribute' => 'text',
            'plugins' => array('spellchecker', 'table', 'save', 'emotions', 'insertdatetime', 'preview', 'searchreplace', 'print', 'template', 'contextmenu', 'paste', 'fullscreen', 'noneditable', 'layer', 'visualchars'),
            'options' => array(
                'theme' => 'advanced',
                'theme_advanced_toolbar_location' => 'top',
                'theme_advanced_buttons1' => 'bold,italic,underline,fontselect,fontsizeselect,link,insertdate,inserttime,template,justifyfull,justifyleft,justifycenter,justifyright,pasteword,pastetext,table,image,|,bullist,numlist,|,undo,redo,|,preview,code,fullscreen,|,emotions,print',
                'theme_advanced_buttons2' => '',
                'theme_advanced_buttons3' => '',
                'theme_advanced_toolbar_align' => 'left',
            )
        )); ?>
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Изменить'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div>

