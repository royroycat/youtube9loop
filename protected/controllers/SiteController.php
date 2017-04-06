<?php

class SiteController extends ExtendedController
{

	public $layout='//layouts/main';

	public function actionIndex() {
		if (isset(Yii::app()->request->cookies['songlist']->value)) {
			$this->redirect('watch?v='.Yii::app()->request->cookies['songlist']->value);
		} else {
			$this->redirect('watch?v='.Yii::app()->params["default_youtube_id"]);
		}
	}

	public function actionError() {
		$error = Yii::app()->errorHandler->error;
	    if ($error)
	    	$this->render('error', array('error'=>$error));
	    else
	    	throw new CHttpException(404, 'Page not found.');
	}

}