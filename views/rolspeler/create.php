<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Rolspeler */

$this->title = 'Create Rolspeler';
$this->params['breadcrumbs'][] = ['label' => 'Rolspelers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rolspeler-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
