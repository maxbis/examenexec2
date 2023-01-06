<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Beoordeling */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="beoordeling-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'formid')->textInput() ?>

    <?= $form->field($model, 'studentid')->textInput() ?>

    <?= $form->field($model, 'rolspelerid')->textInput() ?>

    <?= $form->field($model, 'resultaat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'opmerking')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
