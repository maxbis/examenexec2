<?php
use yii\helpers\Url;
?>

<h1>Ingevulde formulieren  per student</h1>

<p></p>

<div class="card"  style="width: 400px">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>naam</th>
                    <th>gesprekken</th>
                </tr>
            </thead>
            
            <?php
            $i=1;
            foreach ($result as $item) {
                echo "<tr>";
                echo "<td>".$i++."</td>";
                $url = Url::toRoute(['gesprek/student']);
                echo "<td><a href=\"".$url."?id=".$item['id']."\">".$item['naam']."</a></td>";
                echo "<td>".$item['cnt']."</td>";
                echo "</tr>";
            }

            ?>

        </table>
    </div>
</div>