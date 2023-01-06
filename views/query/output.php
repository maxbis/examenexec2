<?php
use yii\helpers\Url;
$nr=0;
?>

<h1><?= $data['title'] ?></h1>

<?php
    if (isset($descr)) {
        echo "<small>".$descr."</small>";
    }
?>

<p></p>

<div class="card"  style="width: 600px">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <?php
                        if ( ! isset($nocount) ) echo "<td>#</td>";
                        if ( $data['row'] ) {
                            for($i=0;$i<count($data['col']);$i++) {
                                echo "<th>".$data['col'][$i]."</th>";
                            }
                        } else {
                            echo "<td>Empty result set</td>";
                        }
                    ?>
            </thead>
            
            <?php
                if ( $data['row'] ) {
                    foreach($data['row'] as $item) {
                        if ( ! isset($nocount) ) {
                            $nr++;
                            echo "<tr>";
                            echo "<td>".$nr."</td>";
                        }
                        for($i=0;$i<count($data['col']);$i++) {
                            echo "<td>".$item[$data['col'][$i]]."</td>";
    
                        }
                        echo "</tr>";
                    }
                }

            ?>

        </table>
    </div>
</div>