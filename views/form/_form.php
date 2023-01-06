<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Form */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nr')->textInput(['style'=>'width:50px'])->label('Volgnummer') ?> 
    
    <?php
        $itemList=ArrayHelper::map($examenModel,'id','naam');
        echo $form->field($model, 'examenid')->dropDownList($itemList,['prompt'=>'Please select', 'style'=>'width:250px'])->label('Examen');
    ?>

    <?= $form->field($model, 'omschrijving')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Naam van deze beoordeling (of gesprek)')  ?>

    <?= $form->field($model, 'instructie')->textInput(['maxlength' => true])->textArea( ['style'=>'width:800px; height:200px'] ) ?>

    <?php
        $itemList=ArrayHelper::map($werkprocesModel,'id','id');
        echo $form->field($model, 'werkproces')->dropDownList($itemList,['prompt'=>'Please select', 'style'=>'width:250px'])->label('Werkproces');
    ?>

    <?= HTMLInclude('formSave') ?>

    <?php ActiveForm::end(); ?>

</div>
