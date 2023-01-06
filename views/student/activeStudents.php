<?php
use yii\helpers\Html;
use yii\helpers\Url;

$action = Url::toRoute(['student/active-students-post']);
?>

<script language="JavaScript">
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}
</script>

<h1>Studenten actief/inactief</h1>

<form action=<?= $action ?> onsubmit="return result()" method="post" id="form1">
<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
<input type="checkbox" onClick="toggle(this)">&nbsp select all/none

<br>

<table class="table">

<?php

//foreach($students as $student) {
$cols=3;
$rows=intdiv(count($students),$cols)+1;

for($row=0; $row<$rows; $row++) {
    echo "<tr>";
    for($col=0;$col<$cols;$col++){

        $i=$col*$rows+$row;;

        if ($i>=count($students)) {
            echo "<td>&nbsp</td><td>&nbsp;</td><td>&nbsp;</td>";
            continue;
        }

        if ( $students[$i]['actief']) {
            $checked='checked';
        } else {
            $checked = '';
        }
        
        echo "<td><input type=\"checkbox\" id=\"".$students[$i]['id']."\" name=\"".$students[$i]['id']."\" value=\"".$students[$i]['id']."\" ".$checked."></td>";
        echo "<td>".$students[$i]['klas']."</td>";
        echo "<td>".$students[$i]['naam']."</td>";
    }
    echo "</tr>";
}

?>

</table>

<div class="form-group">
      <?= Html::a('Cancel', [Url::toRoute(['student'])], ['class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

</form>

