<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Criterium */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="criterium-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'omschrijving')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort_order') ?>

    <?= $form->field($model, 'nul')->textArea(['rows' => 3]) ?>

    <?= $form->field($model, 'een')->textArea(['rows' => 3]) ?>

    <?= $form->field($model, 'twee')->textArea(['rows' => 3]) ?>

    <?= $form->field($model, 'drie')->textArea(['rows' => 3]) ?>

    <?= $form->field($model, 'werkprocesid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cruciaal')->dropDownList( [ 0 => 'niet  cruciaal', 1 => 'cruciaal']); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
