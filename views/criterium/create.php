<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Criterium */

$this->title = 'Create Criterium';
$this->params['breadcrumbs'][] = ['label' => 'Criteria', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="criterium-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
