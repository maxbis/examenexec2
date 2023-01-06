<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\vraag */

$this->title = 'Create Vraag';
$this->params['breadcrumbs'][] = ['label' => 'Vraags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vraag-create">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= $this->render('_form', [
        'model' => $model,
        'formModel' => $formModel,
        'criterium' => $criterium,
    ]) ?>

</div>
