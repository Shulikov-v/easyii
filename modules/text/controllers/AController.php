<?php
namespace yii\easyii\modules\text\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\easyii\behaviors\CommonActions;
use yii\widgets\ActiveForm;

use yii\easyii\components\Controller;
use yii\easyii\modules\text\models\Text;

class AController extends Controller
{
    public $rootActions = ['create', 'delete'];

    public function behaviors()
    {
        return [
            [
                'class' => CommonActions::className(),
                'model' => Text::className(),
            ],
        ];
    }

    public function actionIndex()
    {
        $data = new ActiveDataProvider([
            'query' => Text::find(),
        ]);
        return $this->render('index', [
            'data' => $data
        ]);
    }

    public function actionCreate($slug = null)
    {
        $model = new Text;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->flash('success', Yii::t('easyii/text', 'Text created'));
                    return $this->redirect(['/admin/'.$this->module->id]);
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            if($slug){
                $model->slug = $slug;
            }
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionEdit($id)
    {
        $model = Text::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('easyii', 'Not found'));
            return $this->redirect(['/admin/'.$this->module->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else{
                if($model->save()){
                    $this->flash('success', Yii::t('easyii/text', 'Text updated'));
                }
                else{
                    $this->flash('error', Yii::t('easyii', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->refresh();
            }
        }
        else {
            return $this->render('edit', [
                'model' => $model
            ]);
        }
    }

    public function actionDelete($id)
    {
        return $this->deleteModel($id, Yii::t('easyii/text', 'Text deleted'));
    }
}