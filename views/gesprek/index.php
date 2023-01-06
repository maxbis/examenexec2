<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GesprekSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gesprekken';
$this->params['breadcrumbs'][] = $this->title;

// counts[0] heeft aantal wachtende, counts[1] in gesprek, counts[2] klaar 
$counts = array_count_values(array_column($alleGesprekken, 'status'));

if ( !isset($counts[0]) ) $counts[0]=0;
if ( !isset($counts[1]) ) $counts[1]=0;

$barlen1 = max(5,$counts[0]*2);
$barlen2 = max(5,$counts[1]*2);

?>
	
<script>
    function changeStatus(id, status, rolspelerid, statusstudent) {
        // console.log(val, id);
        $.ajax({
        url: "<?= Url::to(['/gesprek/update-status']) ?>",
            data: {id: id, 'status': status, 'rolspelerid': rolspelerid, 'statusstudent': statusstudent},
            cache: false
        }).done(function (html) {
            location.reload();
        });
    }
</script>

<meta http-equiv="refresh" content="55">

<div class="gesprek-index">

    <div class="row">

        <div class="col-8">
            <h1>Gespreksoverzicht</h1>
        </div>

        <div class="col bg-light">
            <font size="2" >
                <table border=0 width="100%" class="table-sm">
                    <tr>

                    <td>&nbsp;</td>
                    <td>Drukte</td><td style="text-align:right">Last update:</td>
                    <td><script> document.write(new Date().toLocaleTimeString('en-GB')); </script></td>
                    </tr>

                    <tr>
                    <td style="width: 100px;">
                        Wachtende:
                    </td>

                    <td colspan=2 style="width: 600px;">
                        <div class="progress-bar bg-info" style="width:<?= $barlen1 ?>%">
                        <font size="1" ><?= $counts[0] ?></font>
                        </div>
                    </td>
                    <td>&nbsp;</td>

                    </tr>

                    <tr>
                    <td style="width: 100px;">
                        loopt:
                        </td>

                        <td colspan=2 style="width: 600px;">
                            <div class="progress-bar bg-success" style="width:<?= $barlen2 ?>%">
                            <?= $counts[1] ?>
                            </div>
                        </td>
                        <td>&nbsp;</td>

                    </tr>
                </table>
            </font>
        </div>
    </div>



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

        $rolspelerList = ArrayHelper::map($rolspeler,'id','naam');
        $statusIcon = ['&#128347;', '&#128172;', '&#128504;'];
        $rolspelerList = [ ''=> '-'] + $rolspelerList;
        //dd($rolspelerList);
        $formlist =  ArrayHelper::map($form,'id','omschrijving');  // gespreksnaam
    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [   'attribute'=>'',
                'contentOptions' => ['style' => 'width:120px;'],
                'format' => 'raw',
                'value' => function ($alleGesprekken) use ($statusIcon) {
                    $date = new DateTime($alleGesprekken->created);
                    $text = $statusIcon[$alleGesprekken->status]."&nbsp;".$date->format('d-m H:i');
                    if ( $alleGesprekken->status == 2 ) {
                        return Html::a($text, ['/vraag/form', 'gesprekid'=>$alleGesprekken->id,'compleet'=>'1']);
                    } else {
                        return $text;
                    }
                   
                }
            ],
            
            [
                'attribute' => 'formid',
                'contentOptions' => ['style' => 'width:240px;'],
                'label' => 'Gespreksnaam',
                'filter' => $formlist,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                ],
                'format' => 'raw',
                'value' => function ($alleGesprekken)  {
                    return $alleGesprekken->form->omschrijving;
                }
            ],

            [
                'attribute' => 'student',
                'contentOptions' => ['style' => 'width:240px;'],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    //return $alleGesprekken->student->naam;
                    return Html::a($alleGesprekken->student->naam, ['/gesprek/student', 'id'=>$alleGesprekken->studentid], ['title'=>'Search ID: '.$alleGesprekken->studentid]);
                }
            ],
            [
                'attribute' => 'locatie',
                'contentOptions' => ['style' => 'width:40px;'],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    if (  $alleGesprekken->student->locatie == '') {
                        return '-';
                    } else {
                        return $alleGesprekken->student->locatie;
                    }
                }
            ],
            [
                'attribute' => 'rolspelerid',
                'contentOptions' => ['style' => 'width:200px;'],
                'filter' => $rolspelerList,
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                ],
                'format' => 'raw',
                'value' => function ($alleGesprekken) use ($rolspelerList) {
                    if ($alleGesprekken->status==0) {
                        return Html::dropDownList('status', $alleGesprekken->rolspelerid, $rolspelerList,
                        ['onchange' => "changeStatus('$alleGesprekken->id', '$alleGesprekken->status', $(this).val(), '$alleGesprekken->statusstudent')"]);
                    } else {
                        if (isset($alleGesprekken->rolspeler->naam)) {
                            return Html::a($alleGesprekken->rolspeler->naam, ['/gesprek/rolspeler', 'id'=>$alleGesprekken->rolspelerid]);
                        } else {
                            return("???");
                        }
                    }
                }
            ],

            [   'attribute' => 'opmerking',
                'contentOptions' => ['style' => 'width:20px;'],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    return substr($alleGesprekken->opmerking, 0, 10);
                }
            ],


            [
                'attribute' => 'status',
                'filter' => Yii::$app->params['gesprekStatus'], // list defined in config/params.php
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                    ],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    //$test = Html::dropDownList('status', 3, $rolspelerList);
                    if ( $alleGesprekken->status == 9 ){ // replace != 9 into ==2 in order to enabel  edit only for status 2
                        return Html::dropDownList('status', $alleGesprekken->status,Yii::$app->params['gesprekStatus'],
                        ['onchange' => "changeStatus('$alleGesprekken->id', $(this).val(), '$alleGesprekken->rolspelerid', '$alleGesprekken->statusstudent')"]);
                    } else {
                        return  Yii::$app->params['gesprekStatus'][$alleGesprekken->status];
                    }
                    
                     }
            ],

            
            [
                'attribute' => 'statusstudent',
                'filter' =>  Yii::$app->params['studentStatus'], // list defined in config/params.php
                'label' => 'Student Status',
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => '...'
                    ],
                'format' => 'raw',
                'value' => function ($alleGesprekken) {
                    if ( $alleGesprekken->status != 2 && $alleGesprekken->rolspelerid ){
                        return Html::dropDownList('statusstudent', $alleGesprekken->statusstudent, Yii::$app->params['studentStatus'],
                        ['onchange' => "changeStatus('$alleGesprekken->id','$alleGesprekken->status', '$alleGesprekken->rolspelerid', $(this).val() )"]);
                    } else {
                        return "-";
                    }

                }
            ],

            [   'class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}',
                'visibleButtons'=>[
                'delete'=> function($alleGesprekken){
                      return 0;
                 },
            ]
            ],


        ],
    ]);
?>


</div>
