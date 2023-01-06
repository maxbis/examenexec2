<?php

namespace app\controllers;

use Yii;
use app\models\Vraag;
use app\models\VraagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\Form;
use app\models\Student;
use app\models\Beoordeling;
use app\models\Rolspeler;
use app\models\Gesprek;
use app\models\Criterium;

use kartik\mpdf\Pdf;

use yii\filters\AccessControl;

class VraagController extends Controller
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
 
                    [ 'actions' => [ 'form' ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return ( Yii::$app->user->identity->role == 'rolspeler' );
                        }
                    ],
                ],
            ],
           
        ];
    }

    /**
     * Lists all vraag models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VraagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $formModel = Form::find()
            ->joinwith('examen')
            ->where(['form.actief' => '1'])
            ->andwhere(['examen.actief' => '1'])
            ->orderBy('form.nr')
            ->all();

        $dataProvider->setSort(['defaultOrder' => ['formid'=>SORT_ASC, 'volgnr'=>SORT_ASC]]);

        $get= Yii::$app->request->get();
        if ( $get && array_key_exists('VraagSearch', $get) && array_key_exists('formid', $get['VraagSearch']) ) {
            $formid=Yii::$app->request->get()['VraagSearch']['formid'];
        } else {
            $formid="";
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'formModel' => $formModel,
            'formid' => $formid,
        ]);
    }

    /**
     * Displays a single vraag model.
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

    public function actionForm($gesprekid, $compleet=0, $oldid='', $mappingid=0)
    {
        $gesprek = gesprek::find()->where(['id'=>$gesprekid])->one();

        $vragen = vraag::find()->where(['formid' => $gesprek->formid])->orderBy( ['volgnr' => SORT_ASC, ] )->all();
        $student = student::find()->where(['id' => $gesprek->studentid])->one();
        $form = form::find()->where(['id'=>$gesprek->formid])->one();
        $rolspeler = Rolspeler::find()->where(['id' => $gesprek->rolspelerid])->one();

        // update gesprek(gesprekid) status=1
        $sql="update gesprek set status=1 where id = :id and status=0";
        $params = array(':id'=> $gesprekid);
        Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        if ($compleet) { // antwoordform
            $viewName = 'antwoordform'; // read-only
            // get old answers and results
            $beoordeling = beoordeling::find()->where(['gesprekid' => $gesprekid])->one();
            $resultaat = json_decode($beoordeling->resultaat, true);

        } else { // vraag form
            $viewName = 'vraagform'; // editable
            if ( $oldid ) { // do we have a corerction
                // get previous answers and corrections
                $beoordeling = Beoordeling::Find()->where(['gesprekid' => $oldid ])->one();
                $resultaat = json_decode($beoordeling->resultaat, true)['answers'];
            } else {
                // no previous results
                $resultaat = '';
                $beoordeling = '';
            }

        }

        return $this->render($viewName, [
            'vragen' => $vragen,
            'student' => $student,
            'form' => $form,
            'rolspeler' => $rolspeler,
            'gesprek' => $gesprek,
            'resultaat' => $resultaat,
            'beoordeling' => $beoordeling,
            'mappingid'=> $mappingid,
        ]);
    }

    /**
     * Creates a new vraag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($formid="")
    {
        $model = new vraag();
        $model->formid=$formid;

        $model->volgnr = Vraag::find()->where(['formid'=>$formid])->max('volgnr') + 1;

        if ($formid) {
            $criterium = Criterium::find()->select('id, omschrijving')->where([ 'werkprocesid' => $model->form->werkproces ])->asArray()->all();
        } else {
            $criterium = Criterium::find()->select('id, omschrijving')->asArray()->all();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index?VraagSearch[formid]='.$model->formid );
        }
        
        $formModel = form::find()->all();

        return $this->render('create', [
            'model' => $model,
            'formModel' => $formModel,
            'criterium' => $criterium,
        ]);
    }

    public function actionCopy($id, $prefix='')
    {
        $model = $this->findModel($id);

        $model->id = null;
        $model->vraag = $prefix.$model->vraag;
        $model->volgnr = '';
        $model->isNewRecord = true;
        $model->save();

        $criterium = Criterium::find()->select('id, omschrijving')->where([ 'werkprocesid' => $model->form->werkproces ])->asArray()->all();
        $formModel = form::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index?VraagSearch[formid]='.$model->formid );
        }

        return $this->render('update', [
            'model' => $model,
            'formModel' => $formModel,
            'criterium' => $criterium,
        ]);
    }

    /**
     * Updates an existing vraag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $criterium = Criterium::find()->select('id, omschrijving')->where([ 'werkprocesid' => $model->form->werkproces ])->asArray()->all();
        $formModel = form::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index?VraagSearch[formid]='.$model->formid );
        }

        return $this->render('update', [
            'model' => $model,
            'formModel' => $formModel,
            'criterium' => $criterium,
        ]);
    }

    /**
     * Deletes an existing vraag model.
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

    public function actionRenumber($formid, $step="") {
        $vragen= Vraag::find()->where(['formid'=>$formid])->orderby(['volgnr'=> 'SORT_ASC'])->all();
     
        if ($step=="") {
            if ( $vragen[0]['volgnr'] > 1) {
                $step=1;
            } else {
                $step=10;
            }
        }

        $teller=$step;
        foreach($vragen as $item) {
            $sql="update vraag set volgnr=:volgnr where id=:id";
            $params = array(':volgnr'=>$teller,':id'=>$item['id']);
            $result = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
            $teller=$teller+$step;
        }

        return $this->redirect( ['/vraag/index?VraagSearch[formid]='.$formid] );
    }

    /**
     * Finds the vraag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return vraag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vraag::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
