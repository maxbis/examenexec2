<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="container">

<hr>
<br>

<h1>Nieuwe beoordeling aanmaken en invullen (admin)
</h1>

<div class="gesprek-form">

    <?php $form = ActiveForm::begin(['action' => 'create',]);?>

    <?php
        $itemList=ArrayHelper::map($rolspelers,'id','naam');
        echo $form->field($gesprek, 'rolspelerid')->dropDownList($itemList,[ 'style'=>'width:400px', 'prompt'=>'Please select'])->label('Beoordelaar');
    ?>

    <?php
        $itemList=ArrayHelper::map($studenten,'id','naam');
        echo $form->field($gesprek, 'studentid')->dropDownList($itemList,[ 'style'=>'width:400px', 'prompt'=>'Please select'])->label('Student');
    ?>

    <?php
        $itemList=ArrayHelper::map($forms,'id','omschrijving');
        echo $form->field($gesprek, 'formid')->dropDownList($itemList,[ 'style'=>'width:400px', 'prompt'=>'Please select'])->label('Formulier');
    ?>

    <br>

    <div class="form-group">
      <?= Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('&nbsp;&nbsp;&nbsp;Go&nbsp;&nbsp;&nbsp;', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
 
</div>