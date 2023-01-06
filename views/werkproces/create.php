<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Werkproces */

$this->title = 'Create Werkproces';
$this->params['breadcrumbs'][] = ['label' => 'Werkproces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="werkproces-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
