<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Gesprek */

$this->title = "Gesprek";
$this->params['breadcrumbs'][] = ['label' => 'Gespreks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gesprek-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'formid',
            'form.omschrijving',
            'rolspelerid',
            'rolspeler.naam',
            'studentid',
            'student.naam',
            'opmerking',
        ],
    ]) ?>

</div>

<br>

<?php if ( isset($beoordeling) ): ?>

<div class="beoordeling-view">

    <h1>Beoordeling</h1>

    <?= DetailView::widget([
        'model' => $beoordeling,
        'attributes' => [
            'id',
            'gesprekid',
            'formid',
            'studentid',
            'student.naam',
            'rolspelerid',
            'rolspeler.naam',
            'resultaat',
            'opmerking:ntext',
            'timestamp',
        ],
    ]) ?>

</div>
<br>

    <?php endif; ?>

<?php
    echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Verwijder alles wat er op deze pagina staat?',
                'method' => 'post',
            ],
    ]);

    echo " &nbsp;&nbsp;&nbsp;";

    echo Html::a('Cancel', ['/gesprek'], ['class'=>'btn btn-primary']);
 
?>

<br><br><i>(delete verwijdert <b>Gespreksaanvraag</b> en indien aanwezig de <b>Beoordeling</b>, het resultaat/score wordt niet verwijderd!)</i>