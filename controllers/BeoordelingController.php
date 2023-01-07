<?php

namespace app\controllers;

use Yii;
use app\models\Beoordeling;
use app\models\BeoordelingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



use app\models\Rolspeler;
/**
 * BeoordelingController implements the CRUD actions for Beoordeling model.
 */
class BeoordelingController extends Controller
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
        ];
    }

    /**
     * Lists all Beoordeling models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BeoordelingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Beoordeling model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Beoordeling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Beoordeling();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Beoordeling model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Beoordeling model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionFormpost($resultsString, $gesprekid, $formId, $studentid, $studentnr, $rolspelerid, $opmerking)
    {
        // answerString: 1_51_1_6|2_52_1_6|......

        $answerStrings=explode("|",$resultsString); // [ 1_51_1_6, 2_52_1_6, ... ] 
        $answer=[];
        $points=[];
        foreach($answerStrings as $answerString) {
            $answerItem=explode("_",$answerString); // [ 1, 51, 1, 6] - antwoord teller, vraag id, antwoord (1,2, of 3), points
            $number[$answerItem[0]]=$answerItem[1];
            $answer[$answerItem[1]]=$answerItem[2];
            $points[$answerItem[1]]=$answerItem[3];
        }

        $result = [ 'studentid' => $studentid,
                    'studentnr' => $studentnr,
                    'formid' => $formId,
                    'rolspelerid' => $rolspelerid,
                    'number' => $number,
                    'answers' =>  $answer,
                    'points' => $points,
                    'totaalpoints' => max( array_sum($points), 0) // min 0
                ]; 

        $model = new Beoordeling();
        $model->gesprekid = $gesprekid;
        $model->studentid = $studentid;
        $model->formid = $formId;
        $model->opmerking = $opmerking;
        $model->rolspelerid = $rolspelerid;
        $model->resultaat = json_encode($result);

        // JSON example: {"studentid":"5","studentnr":"2081428","formid":"1","rolspelerid":"7","answers":["51":"1",....],"points":["10",....],"totaalscore":50}
        $sql="delete from results where studentid=:studentid and formid=:formid";
        $params = [ ':studentid'=> $studentid,  ':formid'=> $formId, ];
        $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        foreach ( $result['number'] as $key => $value ) {
            $sql="insert into results (studentid, formid, vraagid, vraagnr, antwoordnr, score)
                    values(:studentid, :formid, :vraagid, :vraagnr, :antwoordnr, :score)";
            $params = [ 'studentid'=> $studentid,
                        ':formid'=> $formId,
                        ':vraagid'=> $value,
                        ':vraagnr'=> $key, 
                        ':antwoordnr'=> $result['answers'][$value],
                        ':score'=> $result['points'][$value],
                    ];
            $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
        
        $sql="delete from beoordeling where gesprekid=:gesprekid";
        $params = [ ':gesprekid'=> $gesprekid, ];
        $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        writeLog("Beoordeling $studentid $gesprekid $formId $model->resultaat");

        if ($model->save()) {
            $sql="update gesprek set status=2 where id=:gesprekid";
            $params = [ ':gesprekid'=> $gesprekid, ];
            $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $token = Rolspeler::find()->where(['id' => $rolspelerid])->one();
            // if we are not an actual rolspeler we want to redirect to the uitslag page (uitslag/index)
            return $this->redirect(['/gesprek/rolspeler', 'token' => $token->token]);
        } else {
            // somehow the results are not stored in the db
            echo "Error, resutls are not saved, save this page!";
            dd($result);
            exit;
        };
        //ToDo store reuslts in DB (studentid needs to be passed)
    }

    /**
     * Finds the Beoordeling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Beoordeling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Beoordeling::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
