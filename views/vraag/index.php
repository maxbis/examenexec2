<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VraagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vragen';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vraag-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <i>De getoonde vragen zijn alle vragen uit het actieve examen</a>.</i><br><br>

    <?php // echo $this->render('_search', ['model' => $searchModel]);
        $formList =  ArrayHelper::map($formModel,'id','omschrijving');
    //d($formList);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [   'attribute' => 'volgnr',
                'label' => '#',
                'contentOptions' => ['style' => 'width:20px;'],
                'value' => function ($model) use ($formList) {
                    return $model->form->nr.'-'.$model->volgnr;
                }
            ],

            [
                'attribute' => 'formid',
                'filter' => $formList,
                'label' => 'Formulier',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'Select'
                    ],
                'format' => 'raw',
                'value' => function ($model) use ($formList) {
                    return Html::a($formList[$model->formid],['/form/form','id'=>$model->formid],['title'=> 'Show Form',]);
                }
            ],

            [
                'attribute' => 'vraag',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->vraag, ['update?id='.$data->id],['title' => 'Edit',]);
                },
            ],
            [   'attribute' => 'ja',
                'contentOptions' => ['style' => 'width:40px;'],
            ],
            [   'attribute' => 'soms',
                'label' => '+/-',
                'contentOptions' => ['style' => 'width:40px;'],
                'value' => function ($data) {
                    return $data->soms ? $data->soms : '-';
                },
            ],
            [   'attribute' => 'nee',
                 'contentOptions' => ['style' => 'width:40px;'],
            ],
            [   'attribute' => 'mappingid',
                'label' => 'Rubic',
                'contentOptions' => ['style' => 'width:40px;'],
                'format' => 'raw',
                'value' => function ($data) {
                    if ( isset($data->criterium) ) {
                        return Html::a($data->mappingid,
                            ['/criterium/index', 'CriteriumSearch[id]'=>$data->mappingid],['title'=> 'Toon: '. $data->criterium->omschrijving ]);
                    } else {
                        return '<div class="p-3 mb-2 bg-danger text-white">??</div>';
                    }
                },
            ],
            [
                'attribute'=>'',
                'contentOptions' => ['style' => 'width:20px; white-space: normal;'],
                'format' => 'raw',
                'value' => function ($data) {
                  return Html::a('<span class="glyphicon glyphicon-th-list"></span>',
                  ['/vraag/renumber?formid='.$data->formid],['title'=> 'Renumber',]);
                },
            ],
            [   'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:80px;'],
            ],
        ],
    ]); ?>


</div>

<p>
    <?= Html::a('Create Vraag', ['/vraag/create', 'formid'=>$formid], ['class' => 'btn btn-success']) ?>
</p>
