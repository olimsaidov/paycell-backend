<?php
$this->pageTitle = Yii::app()->name . ' - Ошибка';
?>

<h2>Ой, ошибка <?php
    echo $code;
    ?></h2>

<div class="error">
    <?php
    echo CHtml::encode($message);
    ?>
</div>
