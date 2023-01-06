<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BeoordelingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Query Output';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Query">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
        echo $output;
    ?>


</div>
