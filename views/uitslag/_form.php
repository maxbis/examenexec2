<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Uitslag */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uitslag-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'studentid')->textInput() ?>

    <?= $form->field($model, 'examenid')->textInput() ?>

    <?= $form->field($model, 'werkproces')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'beoordeelaar1id')->textInput() ?>

    <?= $form->field($model, 'beoordeelaar2id')->textInput() ?>

    <?= $form->field($model, 'commentaar')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ready')->textInput() ?>

    <?= $form->field($model, 'resultaat')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
