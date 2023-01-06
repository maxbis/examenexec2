<?php

namespace app\controllers;

use Yii;
use app\models\Form;
use app\models\FormSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Vraag;
use app\models\Examen;
use app\models\Werkproces;

use yii\filters\AccessControl;

class FormController extends Controller
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
     * Lists all Form models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FormSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Form model.
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
     * Creates a new Form model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Form();
        $examenModel = examen::find()->all();
        $werkprocesModel = werkproces::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'examenModel' => $examenModel,
            'werkprocesModel' => $werkprocesModel
        ]);
    }

    /**
     * Updates an existing Form model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $examenModel = examen::find()->all();
        $werkprocesModel = werkproces::find()->all();
 
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'examenModel' => $examenModel,
            'werkprocesModel' => $werkprocesModel
        ]);
    }

    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $sql="delete from vraag where formid=:formid";
            $params = [':formid'=> $id];
            $result=Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

            $this->findModel($id)->delete();

            $transaction->commit();
            
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    public function actionForm($id)
    {
        $query = vraag::find()
        ->where(['formid' => $id])
        ->orderBy( ['volgnr' => SORT_ASC, ] );

        $vragen = $query->all();

        $form = form::find()->where(['id' => $id])->one();

        return $this->render('form', [
            'form' => $form,
            'vragen' => $vragen,
        ]);
    }

    public function actionToggleActief($id) {
        // function toggles boolean actief
        $sql="update form set actief=(!actief) where id = :id"; 
        $params = array(':id'=> $id);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Form model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Form the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Form::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
