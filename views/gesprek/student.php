<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$status = ['wachten', 'loopt', 'klaar'];

// counts[0] heeft aantal wachtende, counts[1] in gesprek, counts[2] klaar 
$counts = array_count_values(array_column($alleGesprekken, 'status'));

if ( !isset($counts[0]) ) $counts[0]=0;
if ( !isset($counts[1]) ) $counts[1]=0;

$barlen1 = max(5,$counts[0]*2);
$barlen2 = max(5,$counts[1]*2);

?>

<meta http-equiv="refresh" content="60">

<script>
    function changeStatus(id, status, rolspelerid, statusstudent) {
        // console.log(val, id);
        $.ajax({
        url: "<?= Url::to(['/gesprek/update-status']) ?>",
            data: {id: id, 'status': status, 'rolspelerid': rolspelerid, 'statusstudent': statusstudent},
            cache: false
        }).done(function (html) {
            location.reload();
        });
    }
</script>

<div class="container">

  <div class="row">

    <div class="col-8">

      <h1>Gespreksoverzicht
      <?= $student->naam ?>
      </h1>
      <?php if(count($gesprekken)==0): ?>
        <br>
        Je hebt nog geen gesprekken aangevraagd
        <br>
      <?php endif; ?>

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
</div>



<?php if(count($gesprekken)!=0): ?>

  <br>

  <div class="gesprek-overzicht">

    <table class="table" style="width: 100rem;">

      <tr>
        <th scope="col" style="width: 15rem;">Tijd</th>
        <th scope="col" style="width: 15rem;">Gesprek</th>
        <th scope="col" style="width: 20rem;">Status</th>
        <th scope="col" style="width: 15rem;">Opmerking</th>
        <th scope="col" style="width: 15rem;">Waar is Student</th>
      </tr>
    
      <?php $waar=['-','On the Move!','Waiting for Call']; ?>

      <?php foreach ($gesprekken as $item): ?>
          <tr>
          <td><?= Yii::$app->formatter->asTime($item->created) ?> </td>
          <td><?= $item->form->omschrijving ?> </td>
          <td><?= $status[$item->status] ?> </td>
          <td><?= $item->opmerking ?></td>
          <td>
          <?php
            if ( $item->statusstudent!=0 && $item->status != 2 ) {
              echo Html::dropDownList('$item', $item->statusstudent, ['1'=>'On the Move','2'=>'Waiting for Call'],
                    ['onchange' => "changeStatus('$item->id','$item->status', '$item->rolspelerid', $(this).val() )"]);
            } else {
              echo '';
            }
            
            ?>
            </td>
          </tr>
      <?php endforeach; ?>

    </table>
  </div>
<?php endif; ?>

<hr>
<br>

<h1>Nieuw Gesprek aanvragen
</h1>

<?php
  $studentId = $student->id;
?>

<div class="gesprek-form">

    <?php $form = ActiveForm::begin(['action' => 'create',]);?>

    <?= $form->field($newGesprek, 'studentid')->hiddenInput(['value' => $student->id])->label(false) ?>
    <?= $form->field($newGesprek, 'studentmummer')->hiddenInput(['value' => $student->nummer])->label(false) ?>

    <?php
        $itemList=ArrayHelper::map($formModel,'id','omschrijving');
        echo $form->field($newGesprek, 'formid')->dropDownList($itemList,[ 'style'=>'width:400px', 'prompt'=>'Please select'])->label('Kies Gesprek');
    ?>

    <?= $form->field($newGesprek, 'opmerking')->textArea( ['style'=>'width:400px'] ) ?>

    <div class="form-group">
      <?= Html::a( 'Cancel', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>
      &nbsp;&nbsp;&nbsp;
      <?= Html::submitButton('Vraag aan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
 
</div>

<div>
  <small><pre><?= $student->message ?></pre></small>
</div>