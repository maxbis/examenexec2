<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Student */

$this->title = $model->naam;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="container">
    <div class="row">
        <div class="col-sm-6">

            <h1><?= Html::encode($this->title) ?></h1>
            <br>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [   'label'=>'Studentennummer',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data->nummer, ['/gesprek/student?nummer='.$data->nummer],['title'=> 'Edit',]);
                            },
                    ],
                    'klas',
                    'aantalGesprekken',
                ],
            ]) ?>


            <br>

            <p>
                <?= Html::a('Cancel', ['index'], ['class'=>'btn btn-primary']) ?>
                &nbsp;&nbsp;&nbsp;
                <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
                &nbsp;&nbsp;&nbsp;
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>
    </div>
</div>