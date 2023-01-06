<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$action = Url::toRoute(['rolspeler/login']);
?>

<div class="gesprek-form">

    <div class="col-sm-6">

        <h1><?= Html::encode($this->title) ?></h1>
        <hr>

        <form action=<?= $action ?> method="get">
            <label for="exampleFormControlSelect1">RolspelerID:</label>
            <input class="form-control form-control-lg" type="text" id="token" name="token" placeholder="">
            <div class="form-group">
            <br>
            <?= Html::submitButton('Login', ['class' => 'btn btn-success']) ?>
            </div>
        </form>

    </div>
</div>