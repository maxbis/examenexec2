<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use nex\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Examen */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="examen-form">

    <?php $form = ActiveForm::begin(); ?>

      <div class="row">
        <div class="col-sm-4">
          <?= $form->field($model, 'naam')->textInput(['maxlength' => true]) ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'titel')->textInput(['maxlength' => true])->label('Kerntaaknaam (voor op examenbeoordelingsformulier)') ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-2">
          <?= $form->field($model, 'datum_start')->widget(
              DatePicker::className(), [
                'clientOptions' => [
                  'format' => 'Y-MM-D',
                  'stepping' => 30,
                  'minDate' => '2020-01-01',
                  'maxDate' => '2025-12-31',
                ],
              ]);
          ?>
        </div>

        <div class="col-sm-2">
          <?= $form->field($model, 'datum_eind')->widget(
              DatePicker::className(), [
                'clientOptions' => [
                  'format' => 'Y-MM-D',
                  'stepping' => 30,
                  'minDate' => '2020-01-01',
                  'maxDate' => '2025-12-31',
                ],
              ]);
          ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-4">
          <?php $model->otherid=0 ?>
          <?= $form->field($model, 'otherid')->textInput(['maxlength' => true])->label('Examenid for export to KTB (obsolete)') ?>
          </div>
      </div>
      
      <div class="row">
        <div class="col-sm-4">
          <?= $form->field($model, 'examen_type')->textInput(['maxlength' => true])->label('Examen Type (link naar groep werkprocessen)') ?>
          </div>
      </div>
    
    <?= $form->field($model, 'actief')->hiddenInput()->label(false); ?>

    <div class="form-group">
      <?= Html::a('Cancel', [Url::toRoute(['index'])], ['class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>

      <?php if ( isset($model->id) ) { ?>
        &nbsp;&nbsp;&nbsp;
        <?= Html::a('Copy',['copy-exam', 'id'=>$model->id], ['class'=>'btn btn-warning',
          'data-confirm'=>'Weet je zeker dat je een kopie van het examen met onderliggende forms en vragen wilt maken, dit kan niet ongedaan gemaakt worden?','title'=> 'Copy exam' ]); ?>
      <?php } ?>
      
    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>

<p><hr>
<h4>Uitleg</h4>
  <ul>
    <li>Naam van het examen is korte naam, bijv. 'KT-1 januari 2019'.</li>
    <li>Kerntaaknaam is de officiële naam, deze wordt afgedrukt op het SPL examen formulier (eerste regel).</li>
    <li>Datum start en eind geeft aan wanneer het examen plaatsvind.</li>
    <li>Examenid is oud (was voor koppeling 'Marieke-tool') en meot er uit.</li>
    <li>Examen Type is een link naar een groep van werkprocessen. Bijv.  KT-1 -> 1, KT-2 ->2. Een groep werprocessen wordt zo gekoppeld aan dit examen.</li>
  </ul>
<br>
<p>