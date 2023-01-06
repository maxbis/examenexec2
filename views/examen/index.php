<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ExamenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Examen(events)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="examen-index">

  <h1><?= Html::encode($this->title) ?></h1>

  <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable">
         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
         <?= Yii::$app->session->getFlash('success') ?>
    </div>
  <?php endif; ?>

  <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissable">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
      <?= Yii::$app->session->getFlash('error') ?>
    </div>
  <?php endif; ?> 
  
  <hr>

  <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'columns' => [
          // ['class' => 'yii\grid\SerialColumn'],
          [
            'attribute'=>'id',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],
          [
            'attribute'=>'actief',
            'contentOptions' => ['style' => 'width:20px;'],
            'format' => 'raw',
            'value' => function ($data) {
              $status = $data->actief ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-minus"></span>';
              return Html::a($status, ['/examen/toggle-actief?id='.$data->id],['title'=> 'Toggle Status',]);
            }
          ],
          [
            'attribute'=>'datum_start',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],
          [
            'attribute'=>'datum_eind',
            'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
          ],
          [
            'attribute'=>'naam',
            'contentOptions' => ['style' => 'width:600px; white-space: normal;'],
            'format' => 'raw',
            'value' => function ($data) {
              return Html::a($data->naam, ['/uitslag/index', 'examenid'=>$data->id], ['title'=> 'Naar Examenuitslag' ] );
            },
          ],

          [
            'attribute'=>'',
            'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
            'format' => 'raw',
            'value' => function ($data) {
              return Html::a('<span class="glyphicon glyphicon-play">wp</span>',
              ['/werkproces/index', 'WerkprocesSearch[examen_type]'=>$data->examen_type], ['title'=> 'Naar Werkprocessen' ]);
            },
          ],

          // [
          //   'attribute'=>'',
          //   'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
          //   'format' => 'raw',
          //   'value' => function ($data) {
          //     return Html::a('<span class="glyphicon glyphicon-copy"></span>',
          //     ['copy-exam', 'id'=>$data->id], ['data-confirm'=>'Weet je zeker dat je een kopie van het examen met onderliggende forms en vragen wilt maken, dit kan niet ongedaan gemaakt worden?','title'=> 'Copy exam' ]);
          //   },
          // ],

          [
            'class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['style' => 'width:80px;'],
            'template' => '{view} {update}', 
            'visibleButtons'=>[
              'delete'=> function($model){
                    return $model->actief!=1;
               },
          ],
            
        ],
      ],
  ]); ?>


</div>
<br>
<p>

  <?= Html::a('Nieuw Examen', ['create'], ['class' => 'btn btn-success']) ?>
  &nbsp;
  <?= Html::a('<span>Planner</span>', ['/gesprek'], ['class' => 'btn btn-primary', 'title' => 'Naar examenplanner']) ?>
  &nbsp;
  <?= Html::a('<span>Forms</span>', ['/form'], ['class' => 'btn btn-primary', 'title' => 'Naar examenplanner']) ?>
</p>

<?php if (Yii::$app->session->hasFlash('error')): ?>

<?php endif; ?>