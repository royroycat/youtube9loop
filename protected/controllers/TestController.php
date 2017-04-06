<?php

class TestController extends ExtendedController {
	
	public $layout='//layouts/main';
	public $title ="i am a test page";

	public function actionIndex() {
		$this->render('test', array(
			"title" => $this->title,
			'youtube_id' => 'ASO_zypdnsQ'
		));
	}

}
