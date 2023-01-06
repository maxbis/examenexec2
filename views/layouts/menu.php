<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::img('@web/exam.png', ['alt' => 'My logo', 'width' => '40px', 'height' => '40px']) ,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        //'class' => 'navbar-inverse navbar-fixed-top',
        'class' => 'navbar navbar-expand-sm bg-light',
        //'style' => 'font-size: 1.5em',
    ],
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav mr-auto'],
    'encodeLabels' => false,
    'items' => [
        [   'label' => 'Admin',
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                 ['label' => 'Examens', 'url' => ['/examen/index'] ],
                 ['label' => 'Werkproces', 'url' => ['/werkproces/index'] ],
                 ['label' => 'SPL Rubics', 'url' => ['/criterium/index'] ],
                 ['label' => 'Formulieren', 'url' => ['/form']],
                 ['label' => 'Vragen', 'url' => ['/vraag']],
                 ['label' => '-----------------------------------'],
                 ['label' => 'Nieuwe Beoordeling', 'url' => ['/gesprek/create-and-go']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [   'label' => 'Status',
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'items' => [
                ['label' => 'Studenten', 'url' => ['/student/index?StudentSearch[actief]=1']],
                ['label' => 'Rolspelers', 'url' => ['/rolspeler']],
                ['label' => 'Alle Gesprekken/beoordelingen', 'url' => ['/gesprek'],],
                ['label' => '-----------------------------------'],
                ['label' => 'Resultaat', 'url' => ['/uitslag/index']],
            ],
            'options' => ['class' => 'nav-item']
        ],
        [
            'visible' => (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Queries',
            'items' => [
                ['label' => 'Gesprekken per kandidaat', 'url' => ['/query/gesprekken-per-kandidaat']],
                ['label' => 'Gesprekken per rolspeler/beoordelaar', 'url' => ['/query/rolspeler-belasting']],
                ['label' => 'Vrije rolspelers', 'url' => ['/query/vrije-rolspelers']],
                ['label' => 'Beoordelaars', 'url' => ['/query/beoordelaars']],
                ['label' => '-----------------------------------'],
                ['label' => 'Recalc Scores', 'url' => ['/query/recalc']],
                ['label' => 'Orphan gesprekken', 'url' => ['/query/no-result2']],
                ['label' => 'Double Results', 'url' => ['/query/no-doubles']],
                //['label' => 'Punten per onderdeel', 'url' => ['/query/punten']],
            ],
        ],
        [
            'visible' => false && (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin'),
            'label' => 'Login as...',
            'items' => [
                 ['label' => 'Student Log in', 'url' => ['/student/login']],
                 ['label' => 'Rolspeler Log in', 'url' => ['/rolspeler/login']],
                 ['label' => 'Logout', 'url' => ['/site/clear']],
            ],
        ],

    ],

]);


echo Nav::widget([
    'options' => ['class' => 'navbar-nav ml-auto'],
    'items' => [
        Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/site/login'], 'options' => ['class' => 'nav-item']]
        ) : (
            ['label' => 'Logout', 'url' => ['/site/logout'], 'options' => ['class' => 'nav-item'],]
        )
    ],
]);

NavBar::end();
?>
