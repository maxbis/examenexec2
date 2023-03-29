<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use app\models\Vraag;

use app\models\Examen;
use app\models\Werkproces;
use app\models\Student;
use app\models\Results;
use app\models\Gesprek;
use app\models\Uitslag;
use app\models\Rolspeler;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

use yii\helpers\ArrayHelper;

/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class UitslagController extends Controller
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

    private function getScore($max, $punten) {
        // K1-W1..W4 17, 10, 19, 6 max punten
        $score=90*$punten/$max+10;
        if ($score>=80) return "G";
        if ($score>=55) return "V";
        return "O";
    }

    public function actionRemove($studentid, $examenid) {

        $transaction = Yii::$app->db->beginTransaction();

        try {
        // remove uitslag
            $sql="  delete from results
                    where studentid=:studentid
                    and formid in (select f.id from form f where f.examenid=:examenid)
                ";
            $params = [':studentid'=>$studentid, ':examenid'=>$examenid];
            $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $sql="  delete from uitslag
                    where studentid=:studentid
                    and examenid=:examenid
                ";
            $params = [':studentid'=>$studentid, ':examenid'=>$examenid];
            $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $transaction->commit();
            
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    public function actionIndex($examenid="", $sortorder=1, $export=0) {
        // if no parameter is specified then taken the active exam (examen.actief=1)
        // SPL uses wierd round up; it will always round up to the next 0.1 so 3.01 -> 3.1

        $examen=Examen::find()->where(['actief'=>1])->asArray()->one();


        if ( ! $examenid ) {
            $examenid = $examen['id'];
        } else {
            $examen=Examen::find()->where(['id' => $examenid])->asArray()->one();
        }

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
                AND e.id=:examenid
                AND f.examenid=:examenid
                GROUP BY 1,2,3,4,5
                ORDER BY 1,2
            ) as sub
        INNER JOIN werkproces w ON w.id=formnaam
        group by naam, studentid, klas, formnaam, maxscore
        order by 1
        ";
        $params = [':examenid'=> $examenid];
        $result = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

        // print status
        //$sql2="select s.naam naam, p.werkprocesId werkproces, p.status status from beoordeling.printwerkproces p
        //        join student s on s.nummer=p.studentnummer";
        // $result2 = Yii::$app->db->createCommand($sql2)->queryAll();

        $formWpCount = $this->formWpCount($examenid);
        
        $sql="SELECT  s.naam,  f.werkproces, u.ready ready, COUNT(distinct g.formid) cnt
            FROM gesprek g
            INNER JOIN student s ON s.id=g.studentid
            INNER JOIN form f ON f.id = g.formid
            INNER JOIN examen e ON e.id=f.examenid
            LEFT JOIN uitslag u ON u.studentid=g.studentid AND u.werkproces=f.werkproces AND u.examenid=:examenid
            WHERE e.id=:examenid
            AND f.examenid=:examenid
            GROUP BY 1,2,3
            ORDER BY SUBSTRING_INDEX(TRIM(s.naam), ' ', :sortorder) ,f.werkproces";
        $params = [':examenid'=> $examenid, ':sortorder'=>$sortorder];
        $progres = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();  // [ 0 => [ 'naam' => 'Achraf Rida ', 'werkproces' => 'B1-K1-W1', 'cnt' => '3'], 1 => .... ]

        // d($progres);
        // d($result);
        $wp=[];
        foreach($formWpCount as $key => $value) {
            $wp[]=$key;
        }

        $dataSet=[];
        foreach($progres as $item) { // init datastructure
            foreach($wp as $thisWp) {
                $dataSet[$item['naam']][$thisWp]['result']=['', ''];
                $dataSet[$item['naam']][$thisWp]['status']=0;
            }
            $dataSet[$item['naam']]['studentid']="";
        }

        foreach($progres as $item) { // count forms per werproces
            if ( $item['ready'] ) {
                $dataSet[$item['naam']][$item['werkproces']]['status']=99;
            } else {
                $dataSet[$item['naam']][$item['werkproces']]['status']=$item['cnt'];
            }
           
        }
        
        foreach($result as $item) { // Result [ cijfer, result(O, V, G) ]
            // if cruciaal item niet gehaald, cijfer = 1.0 and result = O
            // ToDo
            $dataSet[$item['naam']][$item['werkproces']]['result']=[ $item['cijfer'], $this->rating($item['cijfer']) ];
            $dataSet[$item['naam']]['studentid']=$item['studentid'];
            $dataSet[$item['naam']]['groep']=$item['klas'];
        }
        // d($wp);
        // d($werkproces);
        // dd($dataSet);

        // create cruciaalList, ass. array with key studentid.werkprocess to indicate that thsi student for this wp has failed becasue of crucial item
        $sql="
        SELECT distinct studentid, wp FROM (
            SELECT s.naam naam, s.id studentid, f.werkproces wp, v.mappingid mappingid,
                    MAX(c.cruciaal),
                        SUM(r.score)
            FROM results r
            INNER JOIN student s on s.id=r.studentid
            INNER JOIN vraag v on v.formid = r.formid
            INNER JOIN criterium c on c.id = v.mappingid
            INNER JOIN form f on f.id=v.formid
            INNER JOIN examen e on e.id=f.examenid
            WHERE v.volgnr = r.vraagnr
            AND e.id=:examenid
            AND f.examenid=:examenid
            GROUP BY 1,2,3,4
            HAVING MAX(cruciaal)=1 AND SUM(score)<5
        ) AS sub
        ORDER BY 1
        ";

        $cruciaal = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
        $cruciaalList=[];
        foreach($cruciaal as $item) {
            $cruciaalList[$item['studentid'].$item['wp']]=1;
        }

        if ($export) $this->dataToExcel($dataSet, $wp, $examen);

       //dd($cruciaalList);
        return $this->render('index', [
            'dataSet' => $dataSet,
            'formWpCount' =>$formWpCount, // formcount per wp
            'wp' => $wp,
            'examenid' => $examenid,
            'examen' => $examen, // active exam
            'cruciaalList' => $cruciaalList,
            'sortorder' => $sortorder,
        ]);
    }

    private function dataToExcel($dataSet, $wp, $examen){

        $nr=0;
        $fp = fopen('php://output', 'wb');

        $filenaam="Uitslag ".$examen['naam']."csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename='.$filenaam);

        foreach($dataSet as $naam => $value) {
            $line=[];

            if ($value['studentid']=='') continue; // if beoordeling is not yet specified skip this record
            $nr++;

            array_push($line,$nr, $value['groep'],$naam);

            foreach($wp as $thisWp) {
                array_push($line, number_format($value[$thisWp]['result'][0], 1, ',', '' ) ); // avoid decimal places (independent from localization)
            }
            foreach($wp as $thisWp) {
                array_push($line,$value[$thisWp]['result'][1]);
            }

            fputcsv($fp, $line, ',', '"', "\\");

        }

        fclose($fp);

        exit;
    }

    private function formWpCount($examenid) {
        $sql="  SELECT werkproces, COUNT(*) cnt FROM form f
                INNER JOIN examen e ON f.examenid=e.id 
                WHERE e.id=:examenid
                GROUP BY 1";
        $params = [':examenid'=> $examenid];
        $formWpCount = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
        $formWpCount = Arrayhelper::map($formWpCount,'werkproces','cnt'); // output [ 'B1-K1-W1' => '3', 'B1-K1-W2' => '2', ... ]
        return($formWpCount);
    }
    
    private function rating($cijfer) {
        if ( $cijfer >= 8 ) return "G"; 
        if ( $cijfer >= 5.5 ) return "V";
        return "O";
    }

    // show filled in (SPL) form for 2nd beoordeelaar (form is HTML variant of the fial PDF version)
    function actionResult($studentid, $wp){

        $examen=Examen::find()->where(['actief'=>1])->asArray()->one();
        $werkproces=Werkproces::find()->where(['id'=>$wp])->asArray()->one();
        $student=Student::find()->where(['id'=>$studentid])->asArray()->one();

        $sql="
            SELECT  v.mappingid mappingid, r.formid formid, r.studentid studentid, f.omschrijving fnaam,
                    c.omschrijving cnaam, c.nul, c.een, c.twee, c.drie, c.cruciaal, sum(score) score
            FROM criterium c
            INNER JOIN vraag v ON v.mappingid=c.id
            INNER JOIN form f ON f.id=v.formid
            INNER JOIN examen e ON e.id = f.examenid
            LEFT OUTER JOIN results r ON r.formid=f.id AND r.vraagid=v.id AND r.studentid=:studentid
            WHERE f.werkproces=:werkproces
            AND e.actief=1
            GROUP BY 1,2,3,4,5,6,7,8,9,10
            ORDER BY sort_order,1,2
        ";
        $params = [':studentid'=> $studentid,':werkproces'=>$wp];
        $results = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

        //dd($results);

        $uitslag=Uitslag::find()->where(['and', ['studentid'=>$studentid], ['werkproces'=>$wp], ['examenid'=>$examen['id']] ])->one();

        $rolspelers = Rolspeler::find()->where(['actief'=>1])->orderBy(['naam'=>SORT_ASC])->all();

        if (! $uitslag ) { // if uitslag is not empty, get all remarks
            $uitslag = new Uitslag();
            
            $sql="
                select GROUP_CONCAT(CONCAT('[',f2.omschrijving,']: ', b2.opmerking, '\n')) opmerkingen
                from beoordeling b2
                INNER JOIN form f2 ON f2.id=b2.formid
                where b2.id = any 
                (
                    SELECT max(b.id)
                    FROM beoordeling b
                    INNER JOIN form f ON f.id=b.formid
                    WHERE studentid=:studentid
                    AND f.werkproces=:werkproces
                    AND f.examenid=:examenid
                    AND opmerking != ''
                    group by f.id
                )";

            // get comments from underlying forms/documents
            $params = [':studentid'=> $studentid,':werkproces'=>$wp, ':examenid' => $examen['id'] ];
            $commentaar = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll()[0]['opmerkingen'];
            $uitslag->commentaar = str_replace(',[', '[', $commentaar);

            $sql="
                SELECT rolspelerid
                FROM gesprek g
                INNER JOIN form f ON f.id=g.formid
                WHERE studentid=:studentid
                AND werkproces=:werkproces
                ORDER BY g.id DESC
                LIMIT 1
            ";
            $params = [':studentid'=> $studentid,':werkproces'=>$wp];
            $rolspeler1 = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll()[0]['rolspelerid'];
            $uitslag->beoordeelaar1id = $rolspeler1;
            //dd($rolspeler1);
           
            $uitslag->studentid = $studentid;
            $uitslag->werkproces = $wp;
            $uitslag->examenid = $examen['id'];
        }

        return $this->render('results', [
            'examen' => $examen,
            'werkproces' =>$werkproces,
            'student' => $student,
            'results' => $results, 
            'model' => $uitslag,
            'rolspelers' => $rolspelers,
        ]);

    }

    // with studentid and formid get the most recent gesprek
    function actionGetForm($studentid, $formid, $mappingid=NULL) {
        $gesprek = Gesprek::find()->Where(['formid'=>$formid])->andWhere(['studentid'=>$studentid])->orderBy(['created' => SORT_DESC])->asArray()->one();
        return $this->redirect(['/vraag/form', 'gesprekid'=>$gesprek['id'] , 'compleet'=>1, 'mappingid'=>$mappingid]);
    }

    function actionUpdate() {
        $postedModel = new Uitslag();

        $postedModel->load(Yii::$app->request->post());

        if ( $postedModel->id ) {
            $model = Uitslag::findOne($postedModel->id);
            $model->load(Yii::$app->request->post());
        } else {
            $model = $postedModel;
        }
        
        if ($model->save()) {
            //dd([$model->id, $model->resultaat]);
            return $this->redirect(['index']);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}

