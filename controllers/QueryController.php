<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use app\models\Vraag;
use app\models\Form;
use app\models\Examen;
use app\models\Werkproces;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use yii\helpers\ArrayHelper;

/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class QueryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // when logged in, any user
                    [ 'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                         'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == 'admin');
                        }
                    ],
                ],
            ],

        ];
    }

    private function getExamenId() {
        $sql = "select otherid from examen where actief=1";
        $examenid = Yii::$app->db->createCommand($sql)->queryOne();

        if ($examenid) {
            return $examenid['otherid'];
        } else {
            return 0;
        }
    }

    private function executeQuery($sql, $title="no title") {
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        $data['title']=$title;;

        if ($result) { // column names are derived from query results
            $data['col']=array_keys($result[0]);
        }
        $data['row']=$result;

        return $data;

    }

    private function getScore($max, $punten) {
        // K1-W1..W4 17, 10, 19, 6 max punten
        $score=90*$punten/$max+10;
        if ($score>=80) return "G";
        if ($score>=55) return "V";
        return "O";
    }

    private function rating($cijfer) {
        if ( $cijfer > 8 ) return "G"; 
        if ( $cijfer >= 5.5 ) return "V";
        return "O";
    }

    public function exportExcel($data) {
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: attachment; filename=exportfile.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        return $header."\n".$data;
    }

    public function actionVrijeRolspelers() {
        // Vrije Rolspers

        $sql="  select naam
                from rolspeler r where
                actief = 1 AND id  not in (
                select rolspelerid from gesprek g
                where r.id=g.rolspelerid
                and status <2 )
                order by r.naam
            ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Rolspelers zonder gesprek"),
        ]);
    }

    public function actionRolspelerBelasting() {

        $sql="  select r.naam naam, count(*) aantal
                from gesprek g 
                inner join rolspeler r on r.id=g.rolspelerid
                inner join form f on f.id=g.formid
                inner join examen e on e.id=f.examenid
                where e.actief = 1 
                group by 1
                order by 1
            ";

        return $this->render('output', [
            'descr' => 'Aantal ingevulde beoordelingen per rolspeler/beoordelaar van het actieve examen',
            'data' => $this->executeQuery($sql,"Aantal beoordelingen per rolspeler"),
        ]);
    }

    public function actionPunten() {
        $sql="
            SELECT s.naam naam, f.omschrijving onderdeel, greatest(sum(r.score),0) punten
            FROM results r
            INNER JOIN student s ON s.id=r.studentid
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN examen e ON e.id=f.examenid
            WHERE e.actief=1
            GROUP BY 1,2
            ORDER BY 1,2
        ";

        return $this->render('output', [
            'descr' => 'Punten per onderdeel per kandidaat',
            'data' => $this->executeQuery($sql, "Socre per student per onderdeel"),
        ]);
    }

    public function actionPuntenPerWerkproces() {
        $sql="
            SELECT s.naam naam, f.werkproces werkproces, greatest(sum(r.score),0) punten, round( greatest(sum(r.score),0)*10/(maxscore), 1)  '%'
            FROM results r
            INNER JOIN student s ON s.id=r.studentid
            INNER JOIN form f ON f.id=r.formid
            INNER JOIN examen e ON e.id=f.examenid
            INNER JOIN werkproces w ON  w.id = f.werkproces
            WHERE e.actief=1
            GROUP BY 1,2
            ORDER BY 1,2
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Score per student per werkproces"),
        ]);
    }

    public function actionRecalc() {
        // r.id, r.vraagid, r.vraagnr, r.score oldscore, case antwoordnr when 1 then ja when 2 then soms when 3 then nee end newscore,
        $sql="
           SELECT 
            concat ('update results set score = ', case antwoordnr when 1 then ja when 2 then soms when 3 then nee end, ' where id = ', r.id,';') Results
            FROM results r
            inner join vraag v on r.vraagid=v.id
            and case antwoordnr when 1 then ja when 2 then soms when 3 then nee end <> r.score
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Recalc"),
            'nocount' => true, 
            'descr' => 'When the weight of questions is changed these queries should be run (directly on the DB) to retrospectivly change the scores. The result should be emtpty.',
        ]);
    }

    public function actionGesprekkenPerKandidaat() {
        $sql="
            SELECT s.naam, s.id, s.nummer, count(*) gesprekken
            FROM student s
            INNER JOIN gesprek g
            ON g.studentid=s.id
            INNER JOIN form f
            ON f.id=g.formid
            INNER JOIN examen e
            ON e.id=f.examenid
            WHERE g.status=2
            AND e.actief=1
            GROUP BY s.naam, s.id, s.nummer
            ORDER BY gesprekken, s.naam
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Gesprekken per kandidaat"),
        ]);
    }

    public function actionUitslag() {
        $sql="
        select naam, formnaam, round( (greatest(0,sum(score))/maxscore*9+1),1) cijfer
            from (
                SELECT s.naam naam, f.werkproces formnaam, v.mappingid mappingid, 
                round(sum(r.score)/10,0) score
                FROM results r
                INNER JOIN student s on s.id=r.studentid
                INNER JOIN vraag v on v.formid = r.formid
                INNER JOIN form f on f.id=v.formid
                INNER JOIN examen e on e.id=f.examenid
                WHERE v.volgnr = r.vraagnr
                AND e.actief=1
                GROUP BY 1,2,3
                ORDER BY 1,2
            ) as sub
        INNER JOIN werkproces w ON w.id=formnaam
        group by naam, formnaam, maxscore
        order by 1,2
        ";
        
        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Gesprekken per kandidaat"),
        ]);
    }

    public function actionStudentList() {
        $sql="select * from student";

        $this->downloadExcel($this->executeQuery($sql));

        return;
    }

    public function actionNoResult() {
        $sql="
        select  g.id Gesprek, e.naam examennaam, g.formid Form, s.id Student, s.nummer StNummer, s.naam Studentennaam
        from gesprek g
        inner join student s on s.id=g.studentid
        inner join form f on f.id=g.formid
        inner join examen e on e.id=f.examenid
        where 
        studentid not IN
        (
            select studentid from results r where r.formid = g.formid
        )
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Results Deleted"),
            'descr' => 'Show gesprekken without results, there should be none. Fix by update a gesprek or create new.'
        ]);
    }

    public function actionNoResult2() {
        $sql="
        select  g.id gesprekid, e.naam examennaam, g.formid formid, s.id studentid, s.nummer studentennummer, s.naam studentennaam
        from gesprek g
        inner join student s on s.id=g.studentid
        inner join form f on f.id=g.formid
        inner join examen e on e.id=f.examenid
        where 
        studentid not IN
        (
            select studentid from results r where r.formid = g.formid
        )
        ";

        return $this->render('noResults', [
            'data' => Yii::$app->db->createCommand($sql)->queryAll(),
        ]);
    }

    public function actionNoDoubles() {
        $sql="
            SELECT r.studentid, r.formid, vraagid, COUNT(*)
            FROM results r
            WHERE studentid=42
            GROUP BY 1,2,3
            HAVING COUNT(*) > 1
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Double Count?"),
            'descr' => 'Show vragen that are put more than once in the results, there should be none.'
        ]);
    }

    public function actionBeoordelaars() {
        $sql="
            select s.klas klas, s.naam student, u.werkproces werkproces, r1.naam beoordelaar1, r2.naam beoordelaar2
            from uitslag u
            INNER JOIN student s on s.id=u.studentid
            LEFT JOIN rolspeler r1 on r1.id=beoordeelaar1id
            LEFT JOIN rolspeler r2 on r2.id=beoordeelaar2id
            INNER JOIN examen e on e.id=u.examenid
            where e.actief=1
            order by 1,2,3
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Overzicht Beoordelaars per klas en werkproces")
        ]);
    }

    public function actionResultaat() {
        // SPL uses wierd round up; it will always round up to the next 0.1 so 3.01 -> 3.1
        $sql="
        select naam, studentid, klas, formnaam werkproces, round( ((greatest(0,sum(score))  /maxscore*9+1))+0.049 ,1)  cijfer
            from (
                SELECT s.naam naam, s.id studentid, s.klas klas, f.werkproces formnaam, v.mappingid mappingid, 
                round(sum(r.score)/10,0) score
                FROM results r
                INNER JOIN student s on s.id=r.studentid
                INNER JOIN vraag v on v.formid = r.formid
                INNER JOIN form f on f.id=v.formid
                INNER JOIN examen e on e.id=f.examenid
                WHERE v.volgnr = r.vraagnr
                AND e.actief=1
                GROUP BY 1,2,3,4,5
                ORDER BY 1,2
            ) as sub
        INNER JOIN werkproces w ON w.id=formnaam
        group by naam, studentid, klas, formnaam, maxscore
        order by 1
        ";
        
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        // print status
        //$sql2="select s.naam naam, p.werkprocesId werkproces, p.status status from beoordeling.printwerkproces p
        //        join student s on s.nummer=p.studentnummer";
        // $result2 = Yii::$app->db->createCommand($sql2)->queryAll();

        
        $sql="SELECT werkproces, COUNT(*) cnt FROM form f
            INNER JOIN examen e ON f.examenid=e.id 
            WHERE e.actief=1
            GROUP BY 1";
        $werkproces = Yii::$app->db->createCommand($sql)->queryAll();
        $werkproces = Arrayhelper::map($werkproces,'werkproces','cnt'); // output [ 'B1-K1-W1' => '3', 'B1-K1-W2' => '2', ... ]
        
        $sql="SELECT  s.naam,  f.werkproces, COUNT(distinct g.formid) cnt FROM gesprek g
            INNER JOIN student s ON s.id=g.studentid
            INNER JOIN form f ON f.id = g.formid
            INNER JOIN examen e ON e.id=f.examenid
            WHERE e.actief=1
            GROUP BY 1,2
            ORDER BY 1,2";
        $progres = Yii::$app->db->createCommand($sql)->queryAll();  // [ 0 => [ 'naam' => 'Achraf Rida ', 'werkproces' => 'B1-K1-W1', 'cnt' => '3'], 1 => .... ]

       // dd($werkproces);
        $wp=[];
        foreach($werkproces as $key => $value) {
            $wp[]=$key;
        }

        $dataSet=[];
        foreach($progres as $item) { // init
            foreach($wp as $thisWp) {
                $dataSet[$item['naam']][$thisWp]['result']=['', ''];
                $dataSet[$item['naam']][$thisWp]['status']='';
            }
            $dataSet[$item['naam']]['studentid']="";
        }

        foreach($progres as $item) {
            $dataSet[$item['naam']][$item['werkproces']]['status']=$item['cnt'];
        }

        foreach($result as $item) {
            $dataSet[$item['naam']][$item['werkproces']]['result']=[ $item['cijfer'], $this->rating($item['cijfer']) ];
            $dataSet[$item['naam']]['studentid']=$item['studentid'];
            $dataSet[$item['naam']]['groep']=$item['klas'];
        }
        //d($wp);
        //d($werkproces);
        //dd($dataSet);

        return $this->render('resultaat', [
            'dataSet' => $dataSet,
            'werkproces' =>$werkproces,
            'wp' => $wp,
        ]);
    }

    public function actionStudents() {
        $sql="SELECT nummer, naam, klas, locatie FROM student order by naam";

        $data = $this->executeQuery($sql, "Recalc");
        dd($data);
        $output = $this->exportExcel($data);
        dd($output);
        return $output;
    }

    public function actionMaxPunten() {
        $sql="
            SELECT e.id,w.id, sum( ( greatest(COALESCE(v.ja,0) ,COALESCE(v.soms,0), COALESCE(v.nee,0)) ) ) maxscore, max(w.maxscore*10) maxscore_SPL
            FROM vraag v
            inner join form f on f.id=v.formid
            inner join  werkproces w on w.id=f.werkproces
            inner join examen e on e.id=f.examenid
            group by 1,2
            order by 1,2
        ";

        return $this->render('output', [
            'data' => $this->executeQuery($sql, "Overzicht punten beoordeling v SPL (let op query is nog in onderzoek)")
        ]);
    }

}

