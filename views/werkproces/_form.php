<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Werkproces */
/* @var $form yii\widgets\ActiveForm */
?>
<br>
<div class="werkproces-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4">
        <?= $form->field($model, 'id')->textInput(['maxlength' => true])->label('Werkproces (ID)') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'titel')->textInput(['maxlength' => true])->label('Werkprocesnaam (voor op examenbeoordelingsformulier)') ?>
        </div>
      </div>

    <div class="row">
        <div class="col-sm-4">
        <?= $form->field($model, 'maxscore')->textInput()->label('Maximaal aantal SPL punten') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
        <?= $form->field($model, 'examen_type')->textInput()->label('Hoort bij examen type') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
