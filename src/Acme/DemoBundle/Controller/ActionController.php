<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Action;
use frontend\models\ActionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ActionController implements the CRUD actions for Action model.
 */
class ActionController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'addbreedaction' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * Lists all Action models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ActionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Action model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Action model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Action();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pkActionID]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Action model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pkActionID]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    function actionAddbreedaction() {

        //echo '<pre>';print_r($_POST);die;
        $model = new Action();
        if ($model->load(Yii::$app->request->post())) {

            //$model->fkUserID = Yii::$app->user->id;
            $model->action = $_POST['Action']['action'];
            $model->actionDate = Date('Y-m-d');
           
            if ($model->save(false)) {
                return TRUE;
            } else {
                return FALSE;
            }
            return $this->renderPartial('addbreedAction', [
                        'model' => $model
            ]);
        } else {
            return $this->renderPartial('addbreedAction', [
                        'model' => $model
            ]);
        }
    }

    public function actionBreedfarm() {

        $model = new Action();
        return \yii\helpers\Json::encode($model->getActionID());
    }

    function actionEditbreedaction() {


        $model = $this->findModel($_GET['id']);
        if ($model->load(Yii::$app->request->post())) {

            //$model->fkUserID = Yii::$app->user->id;
            $model->action = $_POST['Action']['action'];
            $model->actionDate = Date('Y-m-d H:i:s');
            $model->save();
            if ($model->save(false)) {
                return TRUE;
            } else {
                return FALSE;
            }
            return $this->renderPartial('editbreedAction', [
                        'model' => $model
            ]);
        } else {
            return $this->renderPartial('editbreedAction', [
                        'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Action model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Action model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Action the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Action::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
