<?php
dd($alleGesprekken);
$counts = array_count_values(array_column($alleGesprekken, 'status'));
$barlen1 = max(1,$counts[0]*2);
$barlen2 = max(1,$counts[1]*2);
?>

<div class="row">
    <div class="col-8">
        <h1><?= $this->title ?></h1>
    </div>

    <div class="col bg-light">
        <font size="2" >
            <table border=0 width="100%" class="table-sm">
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