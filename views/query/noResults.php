<?php
use yii\helpers\Url;
use yii\helpers\Html;

$nr=1;
?>

<h1>Orphan Gesprekken</h1>
<small>Gesprekken zonder uitslag</small>

<?php
    if (isset($descr)) {
        echo "<small>".$descr."</small>";
    }
?>

<p></p>

<div class="card"  style="width: 900px">
    <div class="card-body">

        <table class="table border=1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gesprek</th>
                    <th>Examennaam</th>
                    <th>Form</th>
                    <th>Student</th>
                    <th>Studentnummer</th>
                    <th>Studentennaam</th>
                    <th>Repair</th>
                </tr>
            </thead>


            <?php
                foreach($data as $item) {
                    echo "<tr>";
                    echo "<td>".$nr++."</td>";
                    echo "<td>".$item['gesprekid']."</td>";
                    echo "<td>".$item['examennaam']."</td>";
                    echo "<td>".$item['formid']."</td>";
                    echo "<td>".$item['studentid']."</td>";
                    echo "<td>".$item['studentennummer']."</td>";
                    echo "<td>".$item['studentennaam']."</td>";
                    echo "<td>".Html::a('repair',['vraag/form', 'gesprekid'=>$item['gesprekid'], 'compleet'=>'1'])."</td>";
                    echo "</tr>";
                }
            
            ?>
        </table>

    </div>
</div>