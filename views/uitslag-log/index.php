<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UitslagLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Uitslag Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uitslag-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Uitslag Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'studentid',
            'naam',
            'werkproces',
            'cijfer',
            'old_cijfer',
            'resultaat:ntext',
            'timestamp',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
