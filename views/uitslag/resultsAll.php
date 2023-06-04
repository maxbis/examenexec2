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
    function changeColor(column, punten, werkproces) {

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

        var cijfer=Math.round( ((sum/aantal*3+1) + 0.049)*10 )/10;
        // console.log(sum, aantal, cijfer);
        // console.log("cijfer-"+werkproces);
        document.getElementById("cijfer-"+werkproces).textContent = cijfer;

    }
</script>

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
        </ul>
    </div>

    <br>

    <form action="update-uitslag" id="myform" method=post>
    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
    <input type="hidden" name="studentid" value="<?= $student['nummer']  ?>" />

        <?php

        // dd($rubics);
        // dd($uitslagen);

        foreach($uitslagen as $uitslag) {

            echo "<h1>";
            echo $uitslag['werkproces'];
            echo "</h1>";

            ?>
            <div class="card" style="width: 40rem;border: 1px solid black;padding:10px;">
                <div class="row row-cols-auto">
                    <div class="col-4">Beoordeelaar 1</div>
                    <div class="col-4">Beoordeelaar 2</div>
                    <div class="col-4">Cijfer</div>
                </div>
                <div class="row row-cols-auto">
                    <div class="col-4"><input type="text" size="10" name="beoordeelaar1id" value="<?= $uitslag['beoordeelaar1id'] ?>" /></div>
                    <div class="col-4"><input type="text" size="10 name="beoordeelaar2id" value="<?= $uitslag['beoordeelaar2id'] ?>" /></div>
                    <div id="cijfer-<?= $uitslag['werkproces'] ?>" class="col-4"><?= $uitslag['cijfer']/10 ?></div>
                </div>
            </div>
            <br>
            <?php

            $resultaat=json_decode($uitslag['resultaat'], true);
 
            echo "<div style=\"color:#d0d0d0;\" id=json-".$uitslag['werkproces'].">";
            echo json_encode($resultaat);
            echo "</div>";

            ?>
            <table border=0 class="table">
            <thead><tr>
                <th>Rubics</th><th class="text-center">0</th><th class="text-center">1</th><th class="text-center">2</th><th class="text-center">3</th><th></th>
            </tr> </thead> <?php

            foreach($resultaat as $key => $value)  {
                $omschrijving=$rubics[$key]['omschrijving'];
                if ($rubics[$key]['cruciaal']) $omschrijving.="*";

                $bgcolor=['#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF','#FFFFFF'];
                $bgcolor[$resultaat[$key]]='#d5f7ba';

                echo "\n<tr>\n";
                echo "\n<td width=80px>".$rubics[$key]['omschrijving']."</td>";
                echo "\n<td onclick=\"changeColor(this,0,'".$uitslag['werkproces']."')\" width=80px bgcolor=".$bgcolor[0].">".$rubics[$key]['nul']."</td>";
                echo "\n<td onclick=\"changeColor(this,1,'".$uitslag['werkproces']."')\" width=80px bgcolor=".$bgcolor[1].">".$rubics[$key]['een']."</td>";
                echo "\n<td onclick=\"changeColor(this,2,'".$uitslag['werkproces']."')\" width=80px bgcolor=".$bgcolor[2].">".$rubics[$key]['twee']."</td>";
                echo "\n<td onclick=\"changeColor(this,3,'".$uitslag['werkproces']."')\" width=80px bgcolor=".$bgcolor[3].">".$rubics[$key]['drie']."</td>";
                echo "\n<td width=20px align=\"right\"> <input style=\"border:none;color:#d0d0d0\" size=\"1\" type=\"text\" name=\"resultaat_".$uitslag['id']."_".$key."\" value=\"".$resultaat[$key]."\" readonly> </td>";
                echo "\n</tr>\n";
            }

            echo "</table>";
            ?>
            <br>
            <div class="uitslag-form">
                <div class="row">
                    <div class="col-sm-12">
                        <textarea name="<?= "opmerking_".$uitslag['id']."_0" ?>" form="myform" style="width:1000px;height:150px;"><?= $uitslag['commentaar'] ?></textarea>
                    </div>
                </div>
            </div>
            <br>
            <?php

        }

        ?>

    <input type="submit" value="Submit">
    </form>

</div>