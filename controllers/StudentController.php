<?php

namespace app\controllers;

use Yii;
use app\models\Student;
use app\models\StudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends Controller
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
                    [ 'actions' => ['login'],
                        'allow' => true,
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
     * Lists all Student models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort(['defaultOrder' => ['naam'=>SORT_ASC]]);
        $dataProvider->pagination = ['pageSize' => 100,];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionActiveStudents() {
        $students= Student::find()->orderby('naam')->all();
        return $this->render('activeStudents', [
            'students' => $students,
        ]);
    }

    public function actionBulkEdit() {
        $students= Student::find()->orderby('naam')->all();
        return $this->render('BulkEdit', [
            'students' => $students,
        ]);
    }

    public function actionActiveStudentsPost() {
        $request = Yii::$app->request;
        $post = $request->post();

        $ids='';
        foreach($post as $key => $value) {
            if ($key == '_csrf' ) continue; // skip _csrf token
            $ids.=$value.',';
        }
        $ids = rtrim($ids, ',');

        $sql="update student set actief=0;update student set actief=1 where id in ( ".$ids." )";
        Yii::$app->db->createCommand($sql)->execute();

        return $this->redirect(['student/index?StudentSearch[actief]=1']);
    }

    public function actionBulkEditPost() {
        $request = Yii::$app->request;
        $post = $request->post();

        d($post);

        $ids='';
        $dataSet=[];
        foreach($post as $key => $value) {
            if ($key == '_csrf' ) continue; // skip _csrf token
            list($k, $field)=explode("-", $key); //Split key
            $dataSet[$k][$field]=$value;
            $ids.=$value.',';
        }
        $ids = rtrim($ids, ',');

        $sql="update student set actief=0;update student set actief=1 where id in ( ".$ids." )";
        // Yii::$app->db->createCommand($sql)->execute();
        dd('klaar');
        return $this->redirect(['student/index?StudentSearch[actief]=1']);
    }

    /**
     * Displays a single Student model.
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
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Student model.
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
     * Deletes an existing Student model.
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

    public function actionLogin($nummer=0)
    {
        MyHelpers::CheckIP();
        
        if (isset($_COOKIE['student'])) {
            $id = $_COOKIE['student'];
            writeLog("Log in via Cookie for student id: ".$id);
        } else {
            $id = "";
        }

        if ( isset($_COOKIE['student']) ) {
            return $this->redirect(['/gesprek/student']);
        }

  
        return $this->render('login');
    }


    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggleActief($id) {
        // function toggles boolean actief
        $sql="update student set actief=abs(actief-1) where id = :id;";
        $params = array(':id'=> $id);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        return $this->redirect(['index']);
    }
}
