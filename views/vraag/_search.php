<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\VraagSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vraag-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'formid') ?>

    <?= $form->field($model, 'volgnr') ?>

    <?= $form->field($model, 'vraag') ?>

    <?= $form->field($model, 'ja') ?>

    <?php // echo $form->field($model, 'soms') ?>

    <?php // echo $form->field($model, 'nee') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
