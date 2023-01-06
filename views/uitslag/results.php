<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CriteriumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $werkproces['id'].' '.$werkproces['titel'];
$this->params['breadcrumbs'][] = $this->title;
//dd($student);

$form = ActiveForm::begin(['action' => 'update',]);
$rolspelerList=ArrayHelper::map($rolspelers,'id','naam');

?>

<div class="criterium-index">

    <h1><?= substr($werkproces['id'],0,5).' '.$examen['titel']; ; ?></h1>
    <h1><?= Html::encode($this->title) ?></h1>

    <br>

    <div class="card" style="width: 40rem;">
        <div class="card-body">
            <h4 class="card-header">Persoonsgegevens</h4>
        </div>
        <ul class="list-group">
            <li class="list-group-item">
                <div class="col-sm-5">Datum</div>
                <div class="col-sm-5"><?= $examen['datum_start'].' t/m '.$examen['datum_eind'] ?></div>
            </li>
            <li class="list-group-item">
                <div class="col-sm-5">Kandidaat</div>
                <div class="col-sm-5"><?= $student['naam'] ?></div>
            </li>
            <li class="list-group-item">
                <div class="col-sm-5">Leerlingnummer</div>
                <div class="col-sm-5"><?= $student['nummer'] ?></div>
            </li>
            <li class="list-group-item">
                <div class="col-sm-5">Klas</div>
                <div class="col-sm-5"><?= $student['klas'] ?></div>
            </li>
            <li class="list-group-item">
                <div class="col-sm-5">Beoordelaar 1</div>
                <div class="col-sm-5">Beoordelaar 2</div>
            </li>
            <li class="list-group-item">
                <div class="col-sm-5"><?= $form->field($model, 'beoordeelaar1id')->dropDownList($rolspelerList,['prompt'=>'Please select'])->label(false) ?></div>
                <div class="col-sm-5"><?= $form->field($model, 'beoordeelaar2id')->dropDownList($rolspelerList,['prompt'=>'Please select'])->label(false) ?></div>
            </li>
        </ul>
    </div>
    
    <br><hr>

    <table border=0 class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>punten</th>
                    <th class="text-center">0</th>
                    <th class="text-center">1</th>
                    <th class="text-center">2</th>
                    <th class="text-center">3</th>
                </tr> 
            </thead>

        <?php
            $total=0;
            $result=[];
            foreach($results as $item) {

                $uitslag=round($item['score']/10);
                $total+=$uitslag;
                $uitslag=max(0,$uitslag);
                $resultaat[ $item['mappingid'] ]=$uitslag;

                if ($item['cruciaal']) {
                    $bgcolor=['#F0F0F0','#F0F0F0','#F0F0F0','#F0F0F0','#F0F0F0'];
                } else {
                    $bgcolor=['#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF'];
                }
                if ( $item['score'] != null ) {
                    if ($item['cruciaal'] && $uitslag==0 ) {
                        $bgcolor[$uitslag]='#ff9e9e'; 
                    } else {
                        $bgcolor[$uitslag]='#d5f7ba'; 
                    }
                }
             
                echo "<tr>";
                echo "<td width=30px class=\"text-muted\"><small>".$item['score']."</small></td>";
                // echo "<td width=80px bgcolor=".$bgcolor[4].">".$item['cnaam']."<hr>".$item['fnaam'].$item['formid'].'-'.$item['studentid']."</td>";
                if (isset($item['score'])) {
                    echo "<td width=80px bgcolor=".$bgcolor[4].">".Html::a( $item['cnaam'], ['uitslag/get-form', 'studentid'=>$item['studentid'], 'formid'=>$item['formid'], 'mappingid'=>$item['mappingid'] ])."</td>";
                } else {
                    echo "<td width=80px bgcolor=".$bgcolor[4].">".$item['cnaam']."</td>";
                }
                
                echo "<td width=80px bgcolor=".$bgcolor[0]." >".$item['nul']."</td>";
                echo "<td width=80px bgcolor=".$bgcolor[1]." >".$item['een']."</td>";
                echo "<td width=80px bgcolor=".$bgcolor[2]." >".$item['twee']."</td>";
                echo "<td width=80px bgcolor=".$bgcolor[3]." >".$item['drie']."</td>";
                echo "</tr>";
            }
            $model->resultaat=json_encode($resultaat);
        ?>
   </table>

    <hr>
    
    <div class="card" style="width: 18rem;"><div class="card-body">
    <h5 class="card-header"><u>Cijfertabel</u></h5>
    <table border=0 class="table">
        <thead>
            <tr>
                <th>punten</th>
                <th>cijfer</th>
            </tr> 
        </thead>
        
        <?php

        $total=max(0,$total);
        for($i=0; $i<=$werkproces['maxscore']; $i++) {
            if ( $total == $i) {
                $bgcolor="#d5f7ba";
            } else {
                $bgcolor="#FFFFFF";
            }
            $cijfer=number_format(intval(10.99+90*$i/$werkproces['maxscore'])/10,1);
            echo "<tr>";
            echo "<td width=80px bgcolor=".$bgcolor.">".$i."</td>";
            echo "<td width=80px bgcolor=".$bgcolor.">".$cijfer."</td>";
            echo "</tr>";
        }

        ?>
    </table>
    </div></div>

    <br><br>

    <div class="uitslag-form">

        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'commentaar')->textarea(['rows' => 6])->label('Motivatie') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'ready')->checkbox() ?>
            </div>
        </div>

        <?= $form->field($model, 'studentid')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'werkproces')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'examenid')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'resultaat')->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('&nbsp;&nbsp;Save&nbsp;&nbsp;', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    </div>

</div>

<br><br>
