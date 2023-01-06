<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Student Log in';
$action = Url::toRoute(['gesprek/student']);
?>

<div class="gesprek-form">

    <div class="col-sm-4">

        <h1><?= Html::encode($this->title) ?></h1>
        <hr>

        <form action=<?= $action ?> method="post">
            <label for="exampleFormControlSelect1">Studentennummer:</label>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <input class="form-control form-control-lg" type="text" id="nummer" name="nummer">
            <div class="form-group">
            <br>
            <?= Html::submitButton('Login', ['class' => 'btn btn-success']) ?>
            </div>
        </form>

    </div>
</div>