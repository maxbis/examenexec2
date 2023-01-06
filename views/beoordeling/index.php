<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BeoordelingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Beoordelingen';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="beoordeling-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Beoordeling', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'formid',
            'form.omschrijving',
            'studentid',
            'student.naam',
            'rolspelerid',
            'rolspeler.naam',
            'resultaat',
            //'opmerking:ntext',
            //'timestamp',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
