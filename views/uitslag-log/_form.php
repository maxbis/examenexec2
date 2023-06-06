<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UitslagLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uitslag-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'studentid')->textInput() ?>

    <?= $form->field($model, 'naam')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'werkproces')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cijfer')->textInput() ?>

    <?= $form->field($model, 'old_cijfer')->textInput() ?>

    <?= $form->field($model, 'resultaat')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
