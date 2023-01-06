<?php
use yii\helpers\Html;
use yii\helpers\Url;

$action = Url::toRoute(['student/bulk-edit-post']);
?>

<h1>Studenten bulk edit</h1>

<form action=<?= $action ?> onsubmit="return result()" method="post" id="form1">
<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

<br>

<table class="table">

<?php

echo "<tr>";
foreach($students[0] as $key => $value) {
    echo "<th>$key</th>";
}
echo "</tr>";

foreach($students as $student) {
    echo "<tr>";

    foreach($student as $key => $value) {
        if ($key== 'id') {
            echo "<td>$value</td>";
        } else {
            echo "<td><input type=\"text\" id=\"".$student['id']."-".$key."\" name=\"".$student['id']."-".$key."\" value=\"".$value."\" ></td>";
        }
    }

    echo "</tr>";
}

?>

</table>

<div class="form-group">
      <?= Html::a('Cancel', [Url::toRoute(['/student'])], ['class'=>'btn btn-primary']) ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

</form>