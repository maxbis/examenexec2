<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Examen */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Examens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="examen-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'naam',
            'titel',
            'actief',
            'datum_start',
            'datum_eind',
            'examen_type',
        ],
    ]) ?>

</div>

<p>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Examens kunnen alleen worden verwijderd als er alle gekoppelde forms zijn verwijderd. Doorgaan?',
            'method' => 'post', // 
            ],
        ])
    ?>
</p>
