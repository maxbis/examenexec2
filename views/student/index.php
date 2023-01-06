<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
//dd($dataProvider);
?>
<div class="student-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?> &nbsp; <?= Html::a('Set (in)actief', ['active-students'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           
            ['class' => 'yii\grid\SerialColumn', 'contentOptions' => ['style' => 'width:20px; white-space: normal;'],],

            // [   'attribute'=>'actief',
            //     'label' => '',
            //     'contentOptions' => ['style' => 'width:5px;'],
            //     'format' => 'raw',
            //     'value' => function ($data) {
            //         if ($data->actief) {
            //             return '&#10004;';
            //         } else { 
            //             return '&#10060;';
            //         }
            //     }
            // ],

            [
                'attribute'=>'actief',
                'contentOptions' => ['style' => 'width:10px;'],
                'format' => 'raw',
                'value' => function ($data) {
                  $status = $data->actief ? '&#10004' : '&#10060';
                  return Html::a($status, ['/student/toggle-actief?id='.$data->id],['title'=> 'Toggle Status',]);
                }
            ],
            
            [   'attribute'=>'nummer',
                'contentOptions' => ['style' => 'width:60px;'],
            ],
            [   'attribute'=>'klas',
                'contentOptions' => ['style' => 'width:60px;'],
            ],
            [   'attribute'=>'locatie',
                'contentOptions' => ['style' => 'width:60px;'],
            ],
            [
                'attribute'=>'naam',
                'contentOptions' => ['style' => 'width:400px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->naam, ['/gesprek/student?id='.$data->id],['title'=> 'Edit',]);
                    },
            ],
            // [
            //     'attribute'=>'achternaam',
            //     'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
            //     'format' => 'raw',
            // ],

            ['class' => 'yii\grid\ActionColumn', 'contentOptions' => ['style' => 'width:80px; white-space: normal;'],],

            [
                'attribute'=>'id',
                'contentOptions' => ['style' => 'width:10px;color:#A0A0A0;'],
            ],
            
        ],
    ]); ?>


</div>
