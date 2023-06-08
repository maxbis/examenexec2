<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CriteriumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = $werkproces['id'].' '.$werkproces['titel'];
//$this->params['breadcrumbs'][] = $this->title;
//dd($student);

$form = ActiveForm::begin(['action' => 'update',]);
$rolspelerList=ArrayHelper::map($rolspelers,'id','naam');

?>

<script>
    function redirectToPage(selectElement, studentid){
        var selectedOption = selectElement.value;
        var currentPageUrl = window.location.pathname;
        window.location.href = currentPageUrl + "?studentid="+studentid+"&snapshot=" + selectedOption;
    }

    function changeColor(column, punten, werkproces, cruciaal=0, maxscore) {
        console.log(punten, maxscore);
        // Get the parent row of the clicked column
        var row = column.parentNode;
        
        // Get all the columns in the row
        var columns = row.getElementsByTagName('td');
        
        // Reset the background color of all columns in the row
        for (var i = 0; i < columns.length; i++) {
            columns[i].style.backgroundColor = '#ffffff';
            var input = columns[i].querySelector('input');
            if (input) {
                input.value = punten;
            }
        }
        
        // Change the background color of the clicked column
        column.style.backgroundColor = '#d5f7ba';

        var table = column.closest('table');
        var sum=0;
        var aantal=0;

        for (var i = 1; i < table.rows.length; i++) {
            var input = table.rows[i].cells[5].querySelector('input[type="text"]');
            if (input) {
                aantal++;
                var value = parseFloat(input.value);
                if (!isNaN(value)) {
                    sum += value;
                }
            }
        }

        
        var cijferc=Math.round( ((((sum/aantal)*3)+1) + 0.049)*10 )/10;
        var cijfer=Math.round( ((sum*9/maxscore+1)+0.049)*10 )/10;
        console.log(sum, aantal,maxscore, cijfer, aantal*3, cijferc);

        document.getElementById("cijfer-"+werkproces).textContent = cijfer;
        if (cruciaal==1) {
            if (punten==0) {
                document.getElementById("cruciaal-"+werkproces).value = 1;
            } else {
                document.getElementById("cruciaal-"+werkproces).value = 0;
            }
        }
        

    }
</script>

<form action="update-uitslag" id="myform" method=post>
<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
<input type="hidden" name="studentid" value="<?= $student['id']  ?>" />

<div>

    <div class="container">
        <div class="row row-cols-2">
            <div class="col-6 text-left-align">
                <div class="card"">
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
                    </ul>
                    <?php if ( ! isset($_GET['snapshot']) ) { ?>
                        <input class="btn btn-light" type="submit" value="Save All (form)">
                    <?php } else { ?>
                        <input class="btn btn-light" type="submit" value="Restore this snapshot" onclick="return confirm('Are you sure you want to restore this snapshot?');">
                    <?php } ?>
                </div>
            </div>

            <div class="col-4">
                <?php
                    if ( ! isset($_GET['snapshot']) ) {
                ?>
                    Snapshot <select onchange="redirectToPage(this, <?=$student['id']?>)" style="background-color:white;border-width:1px;" name="snapshot"?>">
                    <option value=''>last</option>
                    <?php
                        $selected='';
                        foreach($snapshots as $snapshot) {
                            echo '<option value="' . $snapshot.'" ' . $selected.' >' . $snapshot . '</option>';
                        }
                    ?>
                    </select>
                <?php
                    }else{
                        echo "<div class='bg-warning text-center'>Looking at snapshot: ".$_GET['snapshot']."</div>";
                    }
                ?>
            </div>
        </div>
    </div>


    <br>

        <?php

        // dd($rubics);
        // dd($uitslagen);

        foreach($uitslagen as $uitslag) {

            echo "<br><h1>";
            echo '• '.$uitslag['werkproces'];
            echo "</h1>";

            ?>
            <div class="card" style="width: 65rem;border: 1px solid black;padding:10px;">
                <div class="row row-cols-4">
                    <div class="col-3">Student</div>
                    <div class="col-2">Cijfer</div>
                    <div class="col-3">Beoordelaar 1</div>
                    <div class="col-3">Beoordelaar 2</div>
                    
                </div>

                <div class="row row-cols-4">
                    <div class="col-3"><?= $student['naam'] ?></div>

                    <div id="cijfer-<?= $uitslag['werkproces'] ?>" class="col-2"><?= number_format(($uitslag['cijfer']/10),1,'.','') ?></div>

                    <div class="col-3">
                        <select style="background-color:white;border-width:1px;" name="b1_<?=$uitslag['id']?>_<?= $uitslag['werkproces']?>">
                        <?php
                        foreach($rolspelers as $rolspeler) {
                            $selected="";
                            if ( $uitslag['beoordeelaar1id']  ==  $rolspeler['id'] ) $selected="selected"; 
                            echo '<option value="' . $rolspeler['id'].'" '. $selected.'>' . $rolspeler['naam'] . '</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <select style="background-color:#fcfcfc;border-width:1px;" name="b2_<?=$uitslag['id']?>_<?= $uitslag['werkproces']?>">
                        <?php
                        foreach($rolspelers as $rolspeler) {
                            $selected="";
                            if ( $uitslag['beoordeelaar2id']  ==  $rolspeler['id'] ) $selected="selected"; 
                            echo '<option value="' . $rolspeler['id'].'" ' . $selected.' >' . $rolspeler['naam'] . '</option>';
                        }
                        ?>
                        </select>
                    </div>

                </div>
            </div>
            <br>
            <?php

            $resultaat=json_decode($uitslag['resultaat'], true);

            ?>
            <table border=0 class="table">
            <thead><tr>
                <th>Rubics</th><th class="text-center">0</th><th class="text-center">1</th><th class="text-center">2</th><th class="text-center">3</th><th></th>
            </tr> </thead> <?php

            $cruciaal=0;

            foreach($resultaat as $key => $value)  {
                if ($rubics[$key]['cruciaal'] && $resultaat[$key]==0) {
                    $cruciaal=1;
                }

                $bgcolor=['#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF'];
                $bgcolor[$resultaat[$key]]='#d5f7ba';

                echo "\n<tr>\n";
                echo "\n<td width=80px>".$rubics[$key]['omschrijving']."</td>";

                echo "\n<td onclick=\"changeColor(this,0,'".$uitslag['werkproces']."','".$rubics[$key]['cruciaal']."','".$werkprocesses[$uitslag['werkproces']]['maxscore']."')\" width=80px bgcolor=".$bgcolor[0].">".$rubics[$key]['nul']."</td>";
                echo "\n<td onclick=\"changeColor(this,1,'".$uitslag['werkproces']."','".$rubics[$key]['cruciaal']."','".$werkprocesses[$uitslag['werkproces']]['maxscore']."')\" width=80px bgcolor=".$bgcolor[1].">".$rubics[$key]['een']."</td>";
                echo "\n<td onclick=\"changeColor(this,2,'".$uitslag['werkproces']."','".$rubics[$key]['cruciaal']."','".$werkprocesses[$uitslag['werkproces']]['maxscore']."')\" width=80px bgcolor=".$bgcolor[2].">".$rubics[$key]['twee']."</td>";
                echo "\n<td onclick=\"changeColor(this,3,'".$uitslag['werkproces']."','".$rubics[$key]['cruciaal']."','".$werkprocesses[$uitslag['werkproces']]['maxscore']."')\" width=80px bgcolor=".$bgcolor[3].">".$rubics[$key]['drie']."</td>";
                echo "\n<td width=20px align=\"right\"> <input style=\"border:none;color:#d0d0d0\" size=\"1\" type=\"text\" name=\"resultaat_".$uitslag['id']."_".$key."\" value=\"".$resultaat[$key]."\" readonly> </td>";
                echo "\n</tr>\n";
            }

                echo "</table>";

            echo "<div style=\"color:#d0d0d0;\" id=json-".$uitslag['werkproces'].">";
                echo json_encode($resultaat);
                ?>
                <input style="color:#c0c0c0;border: 0px solid;width:5;" type="hidden" id="cruciaal-<?= $uitslag['werkproces']?>" name="cruciaal_<?=$uitslag['id']?>_<?= $uitslag['werkproces']?>" value="<?=$cruciaal?>" />
                <input style="color:#c0c0c0;border: 0px solid;width:5;" type="hidden" id="max-<?= $uitslag['werkproces']?>" name="max_<?=$uitslag['id']?>_<?= $uitslag['werkproces']?>" value="<?=$werkprocesses[$uitslag['werkproces']]['maxscore']?>" />
            </div>
            <br>
            <div class="uitslag-form">
                <div class="row">
                    <div class="col-sm-12">
                        <textarea name="<?= "opmerking_".$uitslag['id']."_0" ?>" form="myform" style="width:65rem;height:150px;"><?= $uitslag['commentaar'] ?></textarea>
                    </div>
                </div>
            </div>
            <br>
            <?php

        }

        ?>

    <br>
    <?php if ( ! isset($_GET['snapshot']) ) { ?>
        <label><input type="checkbox" id="ready" name="ready" value="1"> Alles gecontroleerd en klaar voor printen.</label>
        &nbsp;&nbsp;&nbsp;<small> (Als er na 'print-ready' iets wordt veranderd, dan verdwijnt het vinkje weer.)</small>
        <br>
        <?= Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>
        <input class="btn btn-danger" type="submit" value="Save All">
        <br>
    <?php } ?>
    </form>

</div>