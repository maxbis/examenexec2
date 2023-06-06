<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UitslagLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uitslag-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'studentid') ?>

    <?= $form->field($model, 'naam') ?>

    <?= $form->field($model, 'werkproces') ?>

    <?= $form->field($model, 'cijfer') ?>

    <?php // echo $form->field($model, 'old_cijfer') ?>

    <?php // echo $form->field($model, 'resultaat') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
