<?php

class PhotosController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('admin'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
                'users'=>array('admin'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
                'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Photos;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
    try
	  {
		if(isset($_POST['Photos']))
		{
			$model->attributes=$_POST['Photos'];
            $uploadedFile=CUploadedFile::getInstance($model,'photo_name');
            $fileName = "{$uploadedFile}";
            $model->photo_name = $fileName;
			if($model->save())
            {
                $path = Yii::app()->basePath;
                $path = substr($path,0,strlen($path)-10);

                $uploadedFile->saveAs($path.'/img/'.$fileName);


                    $this->redirect(array('view','id'=>$model->photo_name));
            }
		}
          Yii::app()->ThumbsGen->createThumbnails();
      }
	 catch(CDbException $e)
	 {
	 	Yii::app()->user->setFlash('error', "Image with same name can't be uploaded twice, kindly rename the image.");
	 }
		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Photos']))
		{

            $temp = $model->photo_name;

			$model->attributes=$_POST['Photos'];
            echo $model->photo_name;

            // $uploadedFile=CUploadedFile::getInstance($model,'photo_name');
             //$fileName = "{$uploadedFile}";
            //$model->photo_name = $fileName;
			if($model->update())
            {
               /* if(!empty($uploadedFile))  // check if uploaded file is set or not
                {
                    $path = Yii::app()->basePath;
                    $path = substr($path,0,strlen($path)-10);
                    unlink($path.'/img/'.$temp);
                    //delete and replace new one
                    $uploadedFile->saveAs($path.'/img/'.$fileName);
                }*/
                $this->redirect(array('view','id'=>$model->photo_name));
            }
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel($id);
            $path = Yii::app()->basePath;
            $path = substr($path,0,strlen($path)-10);
            $model->delete();
            unlink($path.'/img/'.$model->photo_name);
            //unlink($path.'/img/thumbs/'.$model->photo_name);
            if (file_exists($path.'/img/thumbs/'.$model->photo_name))  {
				  unlink($path.'/img/thumbs/'.$model->photo_name);
				}
				
				

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Photos');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Photos('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Photos']))
			$model->attributes=$_GET['Photos'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Photos::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='photos-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
