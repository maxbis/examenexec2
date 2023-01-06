<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Student */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nummer')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Studentnummer') ?>

    <?= $form->field($model, 'naam')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Naam') ?>

    <?= $form->field($model, 'klas')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Klas') ?>

    <?= $form->field($model, 'locatie')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Locatie') ?>

    <?= $form->field($model, 'message')->textInput(['maxlength' => true, 'style'=>'width:250px'])->label('Boodschap') ?>

    <?= $form->field($model, 'actief')->checkbox() ?>

    <?= HTMLInclude('formSave') ?>
    
    <?php ActiveForm::end(); ?>

</div>
