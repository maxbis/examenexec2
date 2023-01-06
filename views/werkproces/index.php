<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WerkprocesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Werkproces';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="werkproces-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Werkproces', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [   'attribute'=>'examen_type',
                'label' => 'Ex. Type',
                'contentOptions' => ['style' => 'width:60px; white-space: normal;'],
            ],

            [   'attribute'=>'id',
                'contentOptions' => ['style' => 'width:90px; white-space: normal;'],
            ],

            [   'attribute'=>'maxscore',
                'label' => 'SPL Max',
                'contentOptions' => ['style' => 'width:90px; white-space: normal;'],
            ],
            [
                'attribute'=>'Max Punten',
                'label' => 'Form Max',
                'contentOptions' => ['style' => 'width:60px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                    $value=0;
                    for($i=0;$i<count($data->vraag);$i++) {
                        $value += max($data->vraag[$i]->ja, $data->vraag[$i]->soms, $data->vraag[$i]->nee);
                    }
                    return $value/10;
                },
            ],

            [
                'attribute'=>'Onderliggende formulieren',
                'contentOptions' => ['style' => 'width:300px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                    $value="";
                    for($i=0;$i<count($data->form);$i++) {
                        $value .= Html::a('&#8226; '.$data->form[$i]->omschrijving.'<br>', ['/form/form','id'=>$data->form[$i]->id]);
                    }
                    // dd($data->form);
                    return $value;
                },
            ],

            [   'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:60px; white-space: normal;'],
            ],
        ],
    ]); ?>


</div>
