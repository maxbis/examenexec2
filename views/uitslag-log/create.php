<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UitslagLog */

$this->title = 'Create Uitslag Log';
$this->params['breadcrumbs'][] = ['label' => 'Uitslag Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uitslag-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
