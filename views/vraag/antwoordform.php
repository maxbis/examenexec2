<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<?php
    if ($student) {
        $studentNaam = $student->naam;
        $studentId = $student->id;
        $dummy = false;
    } else {
        $studentNaam = "DEBUG!";
        $studentId = "";
        $dummy = true;
    }
?>

<script>
    function CopyToClipboard(containerid) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select().createTextRange();
            document.execCommand("copy");
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
            document.execCommand("copy");
        }
    }
</script>

<style>
    input[type=radio] {
        width: 1.5em;
        height: 1.5em;
    }
</style>

<!-- Toont ingevulde formulier (statisch) -->

<div class="Beoordelingsformulier">
    <h1>
    Beoordelingsformulier
    <?= $studentNaam ?>
    </h1>
    <i>Gesprek: <?= $form->nr ?> - <?= $form->omschrijving ?></i>
    <br><br>
    <div style="width: 800px;">
        <?= $form->instructie ?>
    </div>
    <br><br>

    <table class="table">

        <tr>
            <th scope="col" style="width: 1px;"></th>
            <th scope="col" style="width: 1px;" colspan=2>Vragen</th>
            <th scope="col" style="width: 80px;">Ja</th>
            <th scope="col" style="width: 80px;">Soms/Beetje</th>
            <th scope="col" style="width: 80px;">Nee</th>
        </tr>
            
        <?php foreach ($vragen as $item): ?>
            <tr>
            <?php
                if ( $item['mappingid'] == $mappingid ) {
                    echo "<tr style=\"background-color:#f0f0f0\">";
                } else {
                    echo "<tr>";
                }
            ?>
                <td><?= $item->volgnr ?></td>
                <td colspan=2><?= $item->vraag ?></td>
                
                <td>
                    <?php 
                        $thisAnswer = array_shift($resultaat['answers']);
                        $thisPoints = array_shift($resultaat['points']);
                        if ( $thisAnswer == 1){
                            echo "&#128505; (".$thisPoints.")";
                        } else {
                            echo "&#128454;";
                        }
                    ?>
                </td>

                <?php if ( isset($item->soms) ) : ?>
                    <td>
                        <?php
                            if ( $thisAnswer == 2){
                                echo "&#128505; (".$thisPoints.")";
                                } else {
                                    echo "&#128454;";
                                }
                        ?>
                    </td>
                <?php else: ?>
                    <td>nvt</td>
                <?php endif; ?>

                <td>
                    <?php 
                        if ( $thisAnswer == 3){
                            echo "&#128505; (".$thisPoints.")";
                        } else {
                            echo "&#128454;";
                        }
                    ?>
                </td>
            </tr>
            <?php
                if ( $item->toelichting != "" ) {
                    if ( $item['mappingid'] == $mappingid ) {
                        echo "<tr style=\"background-color:#f0f0f0\">";
                    } else {
                        echo "<tr>";
                    }
                    echo "<td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td>".$item->toelichting."</td>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "</tr>";
                }
            ?>
        <?php endforeach; ?>
            
        <tr>
            <td></td>
            <td colspan=2><br>Totaal aantal punten</td>
            <?php // older version of the result json has element totalscore instead of total points ?>
            <td colspan=3><br><?=  isset($resultaat['totaalpoints']) ? $resultaat['totaalpoints'] : $resultaat['totaalscore'] ?></td>
            <td></td>
        </tr>

    </table>

    <br>

    <div class="card" style="width: 40rem;">
        <div class="card-body">
            <h5 class="card-title">Opmerking</h5>
            <?= $beoordeling->opmerking ?>
        </div>
    </div>

    <br>
    Ingevuld op
    <?php
        $date = new DateTime($beoordeling->timestamp);
        echo $date->format('d-m-y - H:i').' uur, door '.$rolspeler->naam."<br><hr>";

        echo Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']);

        if  (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin') {
            echo "&nbsp;&nbsp;&nbsp;";
            echo Html::a('Correct', ['gesprek/correct',  'id'=>$gesprek->id], ['class'=>'btn btn-warning']);
        }

    ?>

    <?php $bestandsNaam = 'F'.$form->nr.'_'.str_replace(' ', '_', rtrim($studentNaam)) ?>

    <br />
    <br />

    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-clipboard" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
        <path fill-rule="evenodd" d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
    </svg>

    <a href="#" id="div1" title="Copy" onclick="CopyToClipboard('div1')"><small><?=$bestandsNaam?></small></a>
    <small> (bestandsnaam voor audio-opname van externe rolspelers)</small>
    <small><hr>Bij twee dezelfde beoordelingen telt alleen de laatste mee in het resultaat; het oude formulier hoeft niet te worden verwijderd.
    </small>

</div>
