<?php
use yii\helpers\Url;
use yii\helpers\Html;

$nr=0;

$colspan = count($wp)+1;
$numberOfColumns=$colspan*3+5;

?>

<style>
.uneven {
    text-align:center;
    background-color:#F0F0F0;
    width:50px;
}
.even {
    text-align:center;
    background-color:#F4F4F4;
    width: 50px;
}
.total {
    text-align:center;
    background-color:#FAfAfA;
}
</style>

<h1>Uitslagen <?= $examen['naam']; ?></h1>
<small>Alleen wanneer een examen op actief staat, kan er worden gewijzigd door op de cijfers te klikken</small>

<p></p>

<div class="card" style="width: 1200px">
    <div class="card-body">
        <table class="table table-sm" border=0>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan=<?=$colspan?>>Cijfers</th>
                    <th colspan=<?=$colspan?>>Resultaten</th>
                    <th colspan=<?=$colspan?>>Print Ready</th>
                    <th><?= Html::a('', ['/uitslag/index','sortorder'=>$sortorder*-1,'examenid'=>$examenid, 'export'=>'1'],['title'=> 'Export uitslagen','class'=>'glyphicon glyphicon-list-alt']); ?></th>
                    <th></th>
                </tr>    
                <tr>
                <?php
                    echo "<th></th>";
                    echo "<th></th>";
                    echo "<th>".Html::a('Kandidaat', ['/uitslag/index','sortorder'=>$sortorder*-1,'examenid'=>$examenid],['title'=> 'Sort achternaam/voornaam',])."</th>";
                   
                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>&nbsp;</th>";

                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>&nbsp;</th>";
                    $teller=1;
                    foreach($wp as $thisWp) {
                        echo "<th class=\"even\">W".$teller++."</th>";
                    }
                    echo "<th>&nbsp;</th>";
                    echo "<th>";
                    echo Html::a("<span style=\"color:#D0D0F0\" class=\"glyphicon glyphicon-print\"></span>",
                        ['print/print-all-zip', 'id'=>$examenid ],
                        [   'title' => 'Download all in zip',
                            'data' => [
                            'confirm' => 'Let op ALLE examens worden in één ZIP gezet. Dit kan even duren. Weet je het zeker?',
                            'method' => 'post',
                            ],
                        ] );
                    echo "</th>";
                    echo "<th>X</th>";
                ?>
                </tr>    
            </thead>
            
            <?php
                $gezakt=0;
                $geslaagd=0;
                $gemaakt=0;

                foreach($wp as $thisWp) {
                    $sum[$thisWp]['O']=0;$sum[$thisWp]['V']=0;$sum[$thisWp]['G']=0; // init total array
                }

                foreach($dataSet as $naam => $value) {
                    if ($value['studentid']=='') continue; // if beoordeling is not yet specified skip this record
                    $nr++;
                    echo "<tr>";
                    echo "<td class=\"text-muted\">".$nr."</td>";
                    echo "<td>";
                    echo Html::a( $value['groep'], ['/uitslag/result-all', 'studentid'=>$value['studentid'] ] );
                    echo "</td>";
                    $onvoldoende=false;
                    
                    foreach($wp as $thisWp) {
                        if ( $value[$thisWp]['result'][1] == 'O' || isset($cruciaalList[$value['studentid'].$thisWp]) ) {
                            $onvoldoende=true;
                            break;
                        }
                    }
                    if ($onvoldoende ) {
                        echo "<td style=\"color:red\">";
                        $gezakt++;
                    } else {
                        echo "<td>";
                        $geslaagd++;
                    }
                    echo $naam."</td>";

                    $dezeGemaakt=false;
                    foreach($wp as $thisWp) { // cijfers afdrukken
                        if (isset($sum[$thisWp][ $value[$thisWp]['result'][1] ]) ) {
                            $sum[$thisWp][ $value[$thisWp]['result'][1] ]++; // total array $sum[werkproces][uitslag]
                        }
                       
                        echo "<td class=\"even\">"; 

                        if ( $examen['actief'] == 1 ) {
                            echo Html::a($value[$thisWp]['result'][0], ['/uitslag/result', 'studentid'=>$value['studentid'], 'wp'=>$thisWp ] );
                        } else {
                            echo $value[$thisWp]['result'][0];
                        }
                        
                        if ( $value[$thisWp]['result'][0] >= 1.05 ) {
                            $dezeGemaakt=true;
                        }
                        echo "</td>";
                    }
                    echo "<td>&nbsp;</td>";
                    if ( $dezeGemaakt ) $gemaakt++;

                    foreach($wp as $thisWp) { // Resultaten (O, V, G)
                        // $cruciaalList[studentid.werkprocess]=1 if crucial criteria is not met (crucial criteria en 0 punten op dit mappingid)
                        // put a red O when the O casued by crucial (without crucial the person would have passed)
                        if ( isset($cruciaalList[$value['studentid'].$thisWp]) ) {
                            echo "<td class=\"even\" style=\"color:red;\">O</td>";
                        } else {
                            echo "<td class=\"even\">".$value[$thisWp]['result'][1]."</td>";
                        } 
                    }
                    echo "<td>&nbsp;</td>";

                    foreach($wp as $thisWp) { // Print Ready
                        echo "<td class=\"even\">"; 
                        if ( $value[$thisWp]['status']==$formWpCount[$thisWp] ) echo  Html::a( "<div class=\"text-success\"><b>".$value[$thisWp]['status']."</b></div>" , ['/uitslag/result', 'studentid'=>$value['studentid'], 'wp'=>$thisWp ] );
                        elseif ( $value[$thisWp]['status']==99 )  echo Html::a("<span class=\"glyphicon glyphicon-check\"></span>", ['/print/index', 'id'=>$dataSet[$naam]['studentid'], 'examenid'=>$examenid, 'onlyWerkproces'=>$thisWp ]);
                        else echo "<div class=\"text-info\">".$value[$thisWp]['status']."</div>";
                    }
                    echo "</td>";
                    echo "<td>&nbsp;</td>";
                    echo "<td>";
                    $print=true;
                    foreach($wp as $thisWp) {
                        if ( $value[$thisWp]['status'] != 99 ) {
                            $print=false;
                            break;
                        }
                    }

                    if ( $print ) {
                        echo Html::a("<span class=\"glyphicon glyphicon-print\"></span>", ['/print/index', 'id'=>$dataSet[$naam]['studentid'], 'examenid'=>$examenid ]);
                    } else {
                        echo "<span title=\"Print beschikbaar als alle vier de werkprocessen print-klaar zijn.\" class=\"glyphicon glyphicon-print text-muted\"></span>";
                    }
                    echo "</td>";
                  
                    echo "<td>";
                    echo Html::a("<span class=\"glyphicon glyphicon-trash\"></span>", ['/uitslag/remove', 'studentid'=>$dataSet[$naam]['studentid'], 'examenid'=>$examenid ], ['data-confirm' => 'Delete all results from '.$naam.' from this exam?', 'title'=> 'Verwijder uitslag']);
                    echo "</td>";

                    echo "</tr>";
                }

                echo "<tr><td colspan=".$numberOfColumns."></td></tr>";

                foreach(['O','V','G'] as $resultaat) {
                    echo "<tr>"; 
                        echo "<td  class=\"total\"></td>";
                        echo "<td  class=\"total\"></td>";
                        echo "<td  class=\"total\"></td>";
                        for($i=1;$i<count($wp);$i++) echo "<td  class=\"total\"></td>"; // 1 less than number of workproces
                        echo "<td  class=\"total\">".$resultaat."</td>";
                        echo "<td class=\"total\"></td>";

                        foreach($wp as $thisWp) {
                            echo "<td class=\"total\">";
                            echo $sum[$thisWp][$resultaat];
                            echo "</td>";
                        }
                        echo "<td  class=\"total\"></td>";

                        echo "<td  class=\"total\" colspan=".$colspan."></td>";

                        echo "<td  class=\"total\"></td>";
                        echo "<td  class=\"total\"></td>";
                    echo "</tr>";
                }
            ?> 


        </table>

        <?php if ( $gemaakt != 0 && ($geslaagd+$gezakt) != 0 ): ?>
            <hr>
            
            <div class="card" style="width: 450px;">
                <div class="card-header">
                    <h3>Totalen</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                             Totaal aantal kandidaten
                        </div>
                        <div class="col-lg-3">
                            <?=$geslaagd+$gezakt?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-6">
                            Niets ingeleverd (alle 1.0)
                        </div>
                        <div class="col-lg-3">
                            <?=$geslaagd+$gezakt-$gemaakt?>
                        </div>
                        <div class="col-lg-2">
                            <?=round(($geslaagd+$gezakt-$gemaakt)*100/($geslaagd+$gezakt),1)?>%
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            Actieve kandidaten
                        </div>
                        <div class="col-lg-3">
                            <?=$gemaakt?>
                        </div>
                        <div class="col-lg-2">
                            <?=round(($gemaakt)*100/($geslaagd+$gezakt),1)?>%
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-6">
                            Geslaagd (van actieve)
                        </div>
                        <div class="col-lg-3">
                            <?=$geslaagd?> / <?=$gemaakt?>
                        </div>
                        <div class="col-lg-2">
                            <?=round($geslaagd*100/($gemaakt),1)?>%
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            Geslaagd (op totaal)
                        </div>
                        <div class="col-lg-3">
                            <?=$geslaagd?> / <?=$geslaagd+$gezakt?>
                        </div>
                        <div class="col-lg-2">
                            <?=round($geslaagd*100/($geslaagd+$gezakt),1)?>%
                        </div>
                    </div>

                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<br><br>
<small><i>( berekening SPL score: round(score/maxscore*9+1)+0.049,1) - hiermee wordt altijd omhoog afgerond naar de volgende 0.1 )</i></small>

<!--
<small><p>
<p>Uitslagen worden in een paar stappen berekend en vastgelegd; form/beoordeling (gesprek) -> results -> uitslag.
<p>Wanneer form gesaved wordt, worden results berekend.
<p>Wanneer SPL form wordt bewaard, wordt de uitslag berekend.
-->
