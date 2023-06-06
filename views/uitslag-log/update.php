<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UitslagLog */

$this->title = 'Update Uitslag Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Uitslag Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="uitslag-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
