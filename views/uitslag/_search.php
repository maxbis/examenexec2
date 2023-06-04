<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UitslagSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uitslag-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'studentid') ?>

    <?= $form->field($model, 'examenid') ?>

    <?= $form->field($model, 'werkproces') ?>

    <?= $form->field($model, 'beoordeelaar1id') ?>

    <?php // echo $form->field($model, 'beoordeelaar2id') ?>

    <?php // echo $form->field($model, 'commentaar') ?>

    <?php // echo $form->field($model, 'ready') ?>

    <?php // echo $form->field($model, 'resultaat') ?>

    <?php // echo $form->field($model, 'timestamps') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
