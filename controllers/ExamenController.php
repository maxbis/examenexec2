<?php

namespace app\controllers;

use Yii;
use app\models\Examen;
use app\models\Form;
use app\models\ExamenSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

class ExamenController extends Controller
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
    /**
     * Lists all Examen models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExamenSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Examen model.
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
     * Creates a new Examen model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Examen();
        $model->actief = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Examen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Examen model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ( count(form::find()->where(['examenid'=>$id])->all()) == 0 ) {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success', "Exam deleted.");
        } else {
            Yii::$app->session->setFlash('error', "Exam cannot be deleten, it has still forms attached to it.");
        }
       
        return $this->redirect(['index']);
    }

    /**
     * Finds the Examen model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Examen the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Examen::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleActief($id) {
        // function toggles boolean actief
        $sql="update examen set actief=1 where id = :id; update examen set actief=0 where id != :id;";
        $sql="update examen set actief=abs(actief-1) where id = :id;";
        $params = array(':id'=> $id);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        return $this->redirect(['index']);
    }

    public function actionCopyExam($id) {
        
        $fromExamenId=$id;
        // get examen
        $examen=Examen::find()->where(['id'=>$id])->one();
    
        if (! $examen) {
            return;
        }
        // copy exam
        $sql="  insert into examen (naam, actief, datum_start, datum_eind, examen_type, otherid, titel)
                select concat(naam, ' copy'), 0, datum_start, datum_eind, examen_type, otherid, titel
                from examen where id=:id";
        $params = [':id'=> $fromExamenId];
        $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        $sql="  select max(id) id from examen";
        $result=Yii::$app->db->createCommand($sql)->queryOne();
        $toExamenId=$result['id'];

        //$toExamenId = Yii::$app->db->getLastInsertID();
        //$toExamenId = 9;

        $sql="  select f.id from form f inner join examen e on e.id=f.examenid where e.id=:fromExamenId";
        $params = [':fromExamenId'=> $fromExamenId];
        $forms=Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();

        // itterate through forms
        foreach($forms as $fromForm) {
            d($fromForm['id']);
            $sql="  insert into form (omschrijving, nr, examenid, actief, werkproces, instructie)
                    select concat(f.omschrijving, ' copy') , f.nr, :toExamenId, f.actief, f.werkproces, f.instructie
                    from form f
                    where id=:formId";
            $params = [':formId'=> $fromForm['id'], ':toExamenId'=> $toExamenId ];
            $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
            $toFormId = Yii::$app->db->getLastInsertID();

            $sql="  insert INTO vraag (formid, volgnr, vraag, toelichting, ja, soms,nee, mappingid, standaardwaarde)
                    select :toFormId, volgnr, vraag, toelichting, ja, soms,nee, mappingid, standaardwaarde FROM `vraag` 
                    where formid = :fromFormId";
            $params = [':fromFormId'=> $fromForm['id'], ':toFormId'=> $toFormId ];
            $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();       

        }
        
        return $this->redirect(['index']);

    }
}
