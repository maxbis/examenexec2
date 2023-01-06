<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Formulieren';

?>
<div class="form-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <i>Alleen formulieren die bij dit examen horen worden getoond.</i><br><br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'nr',
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
            ],
            [
                'attribute'=>'actief',
                'contentOptions' => ['style' => 'width:40px; white-space: normal;'],
                'format' => 'raw',
                'filter' => [''=> 'alles', '0'=>'Inactief','1'=>'Actief'],
                'value' => function ($data) {
                  $status = $data->actief ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-minus"></span>';
                  return Html::a($status, ['toggle-actief?id='.$data->id], ['title' => 'Actief <-> Inactief',]);
                }
            ],
            [
                'attribute'=>'omschrijving',
                'format' => 'raw',
                'value' => function ($data) {
                  return Html::a($data->omschrijving, ['/form/form?id='.$data->id],['title' => 'Show Form',]);
                },  
            ],
            [
                'attribute'=>'examen.naam',
                'label' => 'Examen',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->examen->naam, ['/exmen/form?id='.$data->id],['title' => 'Show Form',]);
                  },  
            ],
            [
                'attribute'=>'werkproces',
            ],
            [
                'attribute'=>'',
                'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                  return Html::a('<span class="glyphicon glyphicon-plus"></span>',
                  ['/vraag/create', 'formid'=>$data->id ],['title'=> 'Nieuwe vraag maken',]);
                },
            ],
            [
                'attribute'=>'',
                'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                  return Html::a('<span class="glyphicon glyphicon-play"></span>',
                  ['/vraag/index?VraagSearch[formid]='.$data->id],['title'=> 'Naar vragen',]);
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

<p>
    <?= Html::a('New Form', ['create'], ['class' => 'btn btn-success']) ?>
    &nbsp;
     <?= Html::a('<span>Planner</span>', ['/gesprek'], ['class' => 'btn btn-primary', 'title' => 'Naar examenplanner']) ?>
    &nbsp;
  <?= Html::a('<span>Examens</span>', ['/examen'], ['class' => 'btn btn-primary', 'title' => 'Naar Examens']) ?>
</p>