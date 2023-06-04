<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Uitslag */

$this->title = 'Create Uitslag';
$this->params['breadcrumbs'][] = ['label' => 'Uitslags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uitslag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
