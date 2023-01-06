<?php

namespace app\controllers;

use Yii;
use app\models\Gesprek;
use app\models\GesprekSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Student;
use app\models\Form;
use app\models\Rolspeler;
use app\models\Beoordeling;

use yii\filters\AccessControl;

/**
 * GesprekController implements the CRUD actions for Gesprek model.
 */
class GesprekController extends Controller
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
                    [ 'actions' => ['student','create', 'update-status'],
                        'allow' => true,
                    ],
                    [ 'actions' => [ 'rolspeler', 'update', 'call-student' ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == 'rolspeler');
                        }
                     ],
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

    /**
     * Lists all Gesprek models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GesprekSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $rolspeler = Rolspeler::find()->where(['actief' => '1'])->orderBy(['naam' => SORT_ASC  ])->all();
        $form = Form::find()
            ->joinwith('examen')
            ->where(['form.actief' => '1'])
            ->andwhere(['examen.actief' => '1'])
            ->all();
     
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'rolspeler' => $rolspeler,
            'form' => $form,
            'alleGesprekken' => Gesprek::find()->all(),
        ]);
    }

    /**
     * Displays a single Gesprek model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $beoordeling = Beoordeling::find()->where(['gesprekid' => $id])->one();
 
        return $this->render('view', [
            'beoordeling' => $beoordeling,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Gesprek model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Gesprek();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (isset(Yii::$app->user->identity->role) && Yii::$app->user->identity->role == 'admin') { 
                if ( $model->rolspelerid ) {
                    // if rolspeler defined, then go to fill in form
                    return $this->redirect(['vraag/form', 'gesprekid' => $model->id]);
                } else { // if logged on as admin 
                    // else just init a new gesprek and put it in the list wating for a rolspeler
                    return $this->redirect(['gesprek/index']);
                }
            } else { // no admin so we are student (or rolspeler).
                return $this->redirect(['gesprek/student']);
            }
            
        }
        // this code is never executed, create is only called wwith a filled in model.
        writeLog("ERROR: We should never be here in the code, please check!");
        return $this->redirect(['gesprek/student']);
    }

    public function actionCorrect($id) {
        
        $oldGesprek = Gesprek::Find()->where(['id'=>$id])->one();
        $model = new Gesprek();
        $model->formid=$oldGesprek->formid;
        $model->rolspelerid=$oldGesprek->rolspelerid;
        $model->studentid=$oldGesprek->studentid;
        $model->opmerking=$oldGesprek->opmerking;
        $model->rolspelerid=$oldGesprek->rolspelerid;
        $model->save();
        return $this->redirect(['vraag/form', 'gesprekid' => $model->id , 'oldid' => $id] );
    }

    public function actionCreateAndGo()
    {
        $newGesprek = new Gesprek();
        if ($newGesprek->load(Yii::$app->request->post()) && $newGesprek->save()) {
            return $this->redirect(['student', 'nummer' => $newGesprek->student->nummer]);
        }

        $forms = Form::find()->select(['form.id id','omschrijving','nr','examenid','form.actief actief', 'instructie'])
                        ->joinWith('examen',true,'INNER JOIN')
                        ->where(['form.actief'=>1])
                        ->andWhere(['examen.actief'=>1])->all();

        $studenten = Student::find()->where(['actief'=>1])->orderBy(['naam'=>SORT_ASC])->all();

        $rolspelers = Rolspeler::find()->where(['actief'=>1])->orderBy(['naam'=>SORT_ASC])->all();

        return $this->render('createAndGo',[
            'gesprek' => $newGesprek,
            'studenten' => $studenten,
            'forms' => $forms,
            'rolspelers' => $rolspelers,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $formModel = Form::find()->all();

        return $this->render('update', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    public function actionUpdateStatus($id, $status, $rolspelerid, $statusstudent) {
        $model = $this->findModel($id);        
        $model->status=$status;
        $model->rolspelerid=$rolspelerid;
        $model->statusstudent=$statusstudent;
        $model->save();
    }


    /**
     * Deletes an existing Gesprek model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // Delete gesprek, beoordeling en results

        // delete results; als het goed is staan er nooit dubelle results in
        // $gesprek=$this->findModel($id);
        // $sql="delete from results where studentid=:studentid and formid=:formid";
        // $params = [ ':studentid'=> $gesprek->studentid,  ':formid'=> $gesprek->formid, ];
        // $error = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

         // delete all beoordelingen belonging to this gesprek
         Yii::$app->db->createCommand()->delete('beoordeling', 'gesprekid = '.$id)->execute();

        // delete gesprek
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Gesprek model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gesprek the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gesprek::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // Show student screen (after login)
    public function actionStudent($id=0, $nummer=0) {

        if ( Yii::$app->request->post() ) {
            $nummer=Yii::$app->request->post();
            isset($nummer['nummer']) ? $nummer=$nummer['nummer'] : $nummer=0;
            writeLog("Login studentnr: ".$nummer);
        }

        if ( $id==0 && $nummer==0 && isset($_COOKIE['student']) ) {
            $id=$_COOKIE['student'];
       }
        if ($id) {
            $student = Student::find()->where(['id' => $id])->one();
            if (empty($student)) {
                writeLog("Login with wrong studentid (possible hack!): ".$id);
                sleep(2); // help to prevent brute force attack
                return $this->render('/student/login');
            }
        } elseif ($nummer) { 
            $student = Student::find()->where(['nummer' => $nummer])->andwhere(['actief'=>1])->one();
            if (empty($student)) {
                writeLog("Login with wrong studentnr: ".$nummer);
                sleep(2);
                return $this->render('/student/login');
            }
            $id=$student->id;
        } 
        if (! $id) {
            return $this->render('/student/login');
        } else {
            setcookie("student", $id, time()+7200, "/");
        }

        $newGesprek = new Gesprek(); 
        $formModel = Form::find()->select(['form.id id','omschrijving','nr','examenid','form.actief actief', 'instructie'])
                        ->joinWith('examen',true,'INNER JOIN')
                        ->where(['form.actief'=>1])
                        ->andWhere(['examen.actief'=>1])->all();
        // ToDo show only gesprekken van current examen!
        // $gesprekken = Gesprek::find()->where(['studentid' => $id])->all(); // this is code before examen
        $sql = "select g.id id, g.formid formid, g.rolspelerid rolspelerid,  g.studentid studentid,
                        g.opmerking opmerking, g.status status, g.statusstudent statusstudent, g.created created 
                FROM gesprek g
                INNER JOIN form ON g.formid=form.id
                INNER JOIN examen ON form.examenid=examen.id
                WHERE g.studentid=:id
                AND examen.actief=1";
        $gesprekken = Gesprek::findBySql($sql, [':id' => $id])->all();

        $alleGesprekken = Gesprek::find()->all();

        return $this->render('student',[
            'gesprekken' => $gesprekken,
            'alleGesprekken' => $alleGesprekken,
            'newGesprek' => $newGesprek,
            'student' => $student,
            'formModel' => $formModel,
        ]);
    }

    public function actionCallStudent($id=0) {
        if ( Yii::$app->user->identity->role == 'rolspeler') {
            if ( isset($_COOKIE['rolspeler']) ) $id = $_COOKIE['rolspeler'];
        } elseif ( ! (Yii::$app->user->identity->role == 'admin' && $id) ) {
            echo "Oops...not logged in as rolspeler or admin";
            exit; // we are not logged on so we are not able to call a student
        }

        // if rolspeler is set inactive, set back to active (since student is picked up)
        $sql = "update rolspeler set actief=1 where id=:id";
        $params = array(':id'=>$id);
        $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        // this query replaces the otehr two. In this statement a student is assigned to a rolspeler in one go
        // in order to avoid concurrency issues
        //$sql="update gesprek set rolspelerid=:rolspelerid where id=
        //        (   select id
        //           from gesprek
        //            where status =0 and (rolspelerid='' or rolspelerid is null)
        //            and created =
        //            (   select min(created)
        //                from gesprek
        //                where status =0 and (rolspelerid='' or rolspelerid is null)
        //            )
        //        )";
        //$params = array(':rolspelerid'=>$id);
        //$result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        $sql = "select * from gesprek where status =0 and (rolspelerid='' or rolspelerid is null) order by created ASC";
        $student = Yii::$app->db->createCommand($sql)->queryOne();

        if ($student) { // doi we have a waiting student
            $sql = "update gesprek set rolspelerid=:rolspelerid where id=:id";
            $params = array(':rolspelerid'=>$id,':id'=>$student['id']);
            $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }

        return $this->redirect(array('gesprek/rolspeler'));
    }

    public function actionRolspeler($id=0,$token="",$gesprekid=0)
    {

        // only if not admin, becasue admin needs easy access via GET token=ABC
        if ( Yii::$app->user->identity->role == 'rolspeler') {
            if ( isset($_COOKIE['rolspeler']) ) $id = $_COOKIE['rolspeler'];
        }   

        if ($id) {
            $rolspeler = Rolspeler::find()->where(['id' => $id])->andWhere(['not', ['token' => null]])->one();
        } elseif($token) {
            $rolspeler = Rolspeler::find()->where(['token' => $token])->andWhere(['not', ['token' => null]])->one();
        } else {
            dd('A,Wrong, no id and no token recieved');
            // return $this->render('rolspeler');
        }

        if ($gesprekid) { // we came here via a cancelled gesprek
            // set status terug naar 0
            $sql="update gesprek set status=0 where id = :id";
            $params = array(':id'=> $gesprekid);
            Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        }
        
        if (!empty($rolspeler)) {
            
            $gesprekken = Gesprek::find()->joinWith('examen')->where('examen.actief=1')->andwhere(['rolspelerid' => $rolspeler->id])->orderBy(['status' => 'SORT_ASC', 'id' => SORT_DESC])->all();
            $alleGesprekken = Gesprek::find()->joinWith('examen')->where('examen.actief=1')->all();
            $unassigned = Gesprek::find()->where('rolspelerid=0 OR rolspelerid is null')->count();
            setcookie("rolspeler", $rolspeler->id, time()+7200, "/");

            return $this->render('/gesprek/rolspeler', [
                'alleGesprekken' => $alleGesprekken,
                'gesprekken' => $gesprekken,
                'unassigned' => $unassigned,
                'rolspeler' => $rolspeler,
            ]);
        }
        
        dd('B,Wrong, no id and no token recieved');
        // return $this->render('rolspeler')
    }
}
