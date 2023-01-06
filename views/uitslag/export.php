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

<?php
// this is a page that shows data to be copied paste to Excel. Sinde there's an export (file) this is obsolte and not used anymore

use yii\helpers\Url;
use yii\helpers\Html;

$nr=0;

$output='';


foreach($dataSet as $naam => $value) {
    $line=[];

    if ($value['studentid']=='') continue; // if beoordeling is not yet specified skip this record
    $nr++;

    $output.= '"'.$nr.'",';
    $output.= '"'.$value['groep'].'",';
    $output.= '"'.strrchr($naam,' ').'",';
    $output.= '"'.$naam.'",';

    foreach($wp as $thisWp) {
        $output.= '"'.$value[$thisWp]['result'][0].'",';
    }
    foreach($wp as $thisWp) {
        $output.= '"'.$value[$thisWp]['result'][1].'",';
    }
    
    $output.= '<br>';
}
?>

<a href="#" title="Copy" onclick="CopyToClipboard('div1')" class="btn btn-success">&nbsp;Copy&nbsp;</a> &nbsp; <?= Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>

<br><br>
<pre id="div1">
<?= $output ?>
</pre>

