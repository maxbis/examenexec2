<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>

<!-- preview form -->

<style>
    input[type=radio] {
        width: 1.5em;
        height: 1.5em;
    }
</style>

<div class="Beoordelingsformulier">
    <h1>
    Beoordelingsformulier
    </h1>
    <i>Gesprek: <?= $form->nr ?> - <?= $form->omschrijving ?></i>
    <br><br>
    <div style="width: 800px;">
        <?= $form->instructie ?>
    </div>
    <br><br>

    <form action="/beoordeling/formpost" onsubmit="return result()" method="get" id="myForm">

        <table class="table">

    <tr>
        <th scope="col" style="width: 1px;"></th>
        <th scope="col" style="width: 1px;" colspan=2>Vragen</th>
        <th scope="col" style="width: 80px;" title="Volledig">Ja</th>
        <th scope="col" style="width: 80px;" title="Niet volledig, soms of enigzins">+/-</th>
        <th scope="col" style="width: 80px;" title="Niet of nauwelijks">Nee</th>
    </tr>
        
        <?php foreach ($vragen as $item): ?>
            <tr>
                <td><?= $item->volgnr ?></td>
                <td colspan=2>
                    <?= Html::a($item->vraag, ['/vraag/update', 'id'=>$item->id], ['title'=>'Edit']); ?>
                </td>
                
                <td><input type="radio" id="1-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->ja ?>" required></td>
                    <?php if ( isset($item->soms) ) : ?>
                        <td><input type="radio" id="2-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->soms ?>"></td>
                    <?php else: ?>
                        <td>nvt</td>
                    <?php endif; ?>
                <td><input type="radio" id="3-<?=$item->volgnr?>" name="<?= $item->volgnr ?>" value="<?= $item->nee ?>" <?= $item->standaardwaarde ? 'checked' : '' ?> ></td>
            </tr>
            <?php if ( $item->toelichting != "" ): ?>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td>
                    <td><?= $item->toelichting ?></td>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                <tr>
            <?php endif; ?>
        <?php endforeach; ?>
        

    </table>

        <b>Opmerkingen</b><br>

        <textarea rows="4" cols="100" name="opmerking" form="myForm"></textarea>

        <br><br>
        
        <div class="form-group">
        <br>
             <?= Html::a( '&nbsp;&nbsp;Lijst&nbsp;&nbsp;', ['/vraag/index', 'VraagSearch[formid]'=>$form->id ], ['class'=>'btn btn-primary']); ?>
            &nbsp;&nbsp;&nbsp;
            <?= Html::a( 'Terug', Yii::$app->request->referrer , ['class'=>'btn btn-primary']); ?>
            &nbsp;&nbsp;&nbsp;
            <?= Html::a( 'Nieuwe Vraag', ['/vraag/create', 'formid'=>$form->id] , ['class'=>'btn btn-warning']); ?>
        </div>
    </form>

</div>