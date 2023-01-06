<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RolspelerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rolspelers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rolspeler-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Rolspeler', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute'=>'naam',
                'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                    //return Html::a($data->naam, ['/gesprek/rolspeler?token='.$data->token],['title' => 'Edit',]);
                    return Html::a($data->naam, ['/rolspeler/update?id='.$data->id],['title' => 'Edit',]);
                },  
            ],
            [
                'attribute'=>'token',
                'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
            ],
            [
                'attribute'=>'actief',
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
                'format' => 'raw',
                'filter' => [''=> 'alles', '0'=>'Inactief','1'=>'Actief'],
                'value' => function ($data) {
                    $status = $data->actief ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-minus"></span>';
                    return Html::a($status, ['/rolspeler/toggle-actief?id='.$data->id], ['title' => 'Actief <-> Inactief',]);
                }
            ],


        ],
    ]); ?>


</div>
