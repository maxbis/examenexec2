<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UitslagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Uitslags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uitslag-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Uitslag', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'studentid',
            'examenid',
            'werkproces',
            'beoordeelaar1id',
            //'beoordeelaar2id',
            //'commentaar:ntext',
            //'ready',
            //'resultaat:ntext',
            //'timestamps',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>