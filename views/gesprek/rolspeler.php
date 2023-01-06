<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$status = Yii::$app->params['gesprekStatus'];

// counts[0] heeft aantal wachtende, counts[1] in gesprek, counts[2] klaar 
$counts = array_count_values(array_column($alleGesprekken, 'status'));
if ( !isset($counts[0]) ) $counts[0]=0;
if ( !isset($counts[1]) ) $counts[1]=0;
$barlen1 = max(5,$counts[0]*2);
$barlen2 = max(5,$counts[1]*2);

$this->title="Gesprekken ".$rolspeler->naam;

$waar=Yii::$app->params['studentStatus']; // defined in config/params.php

?>

<meta http-equiv="refresh" content="50">

<div class="gesprek-overzicht">
    <div class="row"> <!-- Header with Drukte monitor -->
        <div class="col-8">
            <h1><?= $this->title ?></h1>
            <?php if( $unassigned ) { // show button call student only if students are waiting
                echo Html::a('Call Student', ['/gesprek/call-student', 'id'=>$rolspeler->id], ['class' => 'btn btn-success']);
                echo "&nbsp;&nbsp;".$unassigned." beschikbaar";
            } else {
                echo "<button type=\"button\" class=\"btn btn-secondary\" disabled>Call Student</button>";
            }
            ?>
        </div>

        <div class="col bg-light">
            <font size="2" >
                <table class="table-sm">
                    <tr>
                        <td>&nbsp;</td>
                        <td>Drukte</td>
                        <td><script> document.write(new Date().toLocaleTimeString('en-GB')); </script></td>
                    </tr>

                    <tr>
                        <td style="width: 100px;">
                            Wachtende:
                        </td>

                        <td style="width: 600px;">
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

                        <td style="width: 600px;">
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

    <br>

    <table class="table">

    <tr>
      <th scope="col" style="width: 15rem;">Student</th>
      <th scope="col" style="width: 15rem;">Gesprek</th>
      <th scope="col" style="width: 15rem;">Opmerking</th>
      <th scope="col" style="width: 20rem;">Status</th>
      <th scope="col" style="width: 20rem;">Waar</th>
      <th scope="col" style="width: 20rem;">Time</th>
      <th scope="col" style="width: 10rem;">&nbsp;</th>
    </tr>
    
    <?php foreach ($gesprekken as $item): ?>
        <tr>
        <td><?= $item->student->naam ?> </td>
        <td><?= $item->form->omschrijving ?> </td>

        <td><?= substr($item->opmerking,0,10) ?> </td>

        <td><?php if ($item->status == 2) { //TODO klaar, show filled in form....?
                    echo Html::a('Klaar', ['/vraag/form', 'gesprekid' => $item->id,
                    'formid' => $item->form->id, 'studentid' => $item->student->id,
                    'rolspelerid' => $rolspeler->id, 'compleet' => 1]);
                } else {
                    echo $status[$item->status];
                }
            ?>      
        </td>
        
        <td>
        <?php
            if ( isset($item->statusstudent) && $item->status!=2 ) {
                echo $waar[$item->statusstudent];
            } else {
                echo "";
            }
        ?>
        </td>

        <?php
            $text=['Start Gesprek', 'Herstart'];
        ?>

        <td><?= Yii::$app->formatter->asTime($item->created) ?> </td>
        <td><?php if ($item->status != 2): ?>
                <?= Html::a($text[$item->status], ['/vraag/form', 'gesprekid' => $item->id,
                    'formid' => $item->form->id, 'studentid' => $item->student->id,
                    'rolspelerid' => $rolspeler->id, 'compleet' => 0]) ?>
            <?php else: ?>
                &#128504;
            <?php endif; ?> </td>
        </tr>
    <?php endforeach; ?>

    </table>
</div>
