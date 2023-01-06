<?php

namespace app\controllers;

use Yii;
use app\models\Rolspeler;
use app\models\RolspelerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Gesprek;

use yii\filters\AccessControl;
/**
 * RolspelerController implements the CRUD actions for Rolspeler model.
 */
class RolspelerController extends Controller

{
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
                            return (Yii::$app->user->identity->role == 'admin' );
                        }
                    ],
                    [ 'actions' => [ 'login' ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->user->identity->role == 'rolspeler');
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Rolspeler models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RolspelerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['naam'=>SORT_ASC]]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rolspeler model.
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
     * Creates a new Rolspeler model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Rolspeler();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Rolspeler model.
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
     * Deletes an existing Rolspeler model.
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

    public function actionToggleActief($id) {
        // function toggles boolean actief
        $sql="update rolspeler set actief=(!actief) where id = :id";
        $params = array(':id'=> $id);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        return $this->redirect(['/rolspeler/index']);
    }

    public function actionLogin($id=0, $token="",$gesprekid=0)
    {
        //dd($token);
        if ( $token != "" ) {
            writeLog($msg="Trying to get acces with token ".$token);
        }       

        if ( isset($_POST['rolspeler']) ) $id=$_POST['rolspeler'];
        
        if ($id) {
            $rolspeler = Rolspeler::find()->where(['id' => $id])->andWhere(['not', ['token' => null]])->one();
        } elseif($token) {
            $rolspeler = Rolspeler::find()->where(['token' => $token])->andWhere(['not', ['token' => null]])->one();
        } else {
            return $this->render('login');
        }
       
        if (!empty($rolspeler)) {
            setcookie("rolspeler", $rolspeler->id, time()+7200, "/");
            return $this->redirect(['/gesprek/rolspeler']);
        }

        return $this->render('login');
    }

    /**
     * Finds the Rolspeler model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rolspeler the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rolspeler::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
