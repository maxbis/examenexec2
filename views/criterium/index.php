<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CriteriumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SPL Rubics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="criterium-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Criterium', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'label'=>'Rubic',
                'format'=>'raw',
                'value'=> function ($data) {
                    return Html::a($data->id, ['/vraag/index','VraagSearch[mappingid]'=>$data->id]);
                } 
                

            ],
            'omschrijving',
            'nul',
            'een',
            'twee',
            'drie',
            'werkprocesid',
            'cruciaal',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
