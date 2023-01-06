<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\vraag */

$this->title = 'Update Vraag: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Vraags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vraag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'formModel'=> $formModel,
        'criterium' => $criterium,
    ]) ?>

</div>
