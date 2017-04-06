<?php

class QueryController extends ExtendedController
{

	public $layout='//layouts/main';
	public $title ="";

	public function actionByLink() {
		$youtube_link = Yii::app()->request->getPost("link");
		// check if the path is youtu.be
		if (preg_match('/youtu.be\/([^\/]*)\/?/', $youtube_link, $matches)) {
		    $id = $matches[1];
		} else {
		    $a = parse_url($youtube_link);
		    //parse the "v" variable
		    $q = $a["query"];
		    parse_str($q, $output);
		    $id = $output["v"];
		}
		$this->redirect('/watch?v='.$id);
	}
	
	public function actionError() {
		$error = Yii::app()->errorHandler->error;
	    if ($error)
	    	$this->render('site/error', array('error'=>$error));
	    else
	    	throw new CHttpException(404, 'Page not found.');
	}
        
    public function actionIndex() {
		$this->render('search', array(
		    "title" => 'SearchTest'
		));
    }

}