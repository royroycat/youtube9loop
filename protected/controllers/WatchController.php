<?php

class WatchController extends ExtendedController
{
	
	public $layout='//layouts/main';
	public $title ="";
	public $numOfSong; // main layout will grep this :)

	public function actionIndex() {
		$youtube_id = Yii::app()->request->getQuery("v");
		
		$array = explode(",",$youtube_id);
		$this->numOfSong = count($array);
		$youtube_id = $array[0];
		
		$clientIp = Yii::app()->request->userHostAddress;
		$first = null;
		
		// if cannot get any v query then ... find cookies , no cookies then default
		if (!$youtube_id) {
			// http://www.youtube9loop.com without id passed
			if (isset(Yii::app()->request->cookies['songlist']->value)) {
				$this->redirect('watch?v='.Yii::app()->request->cookies['songlist']->value);
			} else {
				$youtube_id = Yii::app()->params["default_youtube_id"];
			}	
		}
    
    try {
      $this->title = Yii::app()->youtube->getVideoTitle($first?$first:$youtube_id);
    } catch (Exception $ex) {
      $this->title = "";
    }
    $this->title = ($this->numOfSong > 1 ? "(" . $this->numOfSong . ") " : "") . $this->title;
		
		$this->render('watch', array(
      "youtube_id" => $youtube_id,
      "title" => $this->title,
      "songlistArray" => $array
        )
    );
  }
	
	public function actionQueryTop() {
	    // retrieve query parameters
	    $location = Yii::app()->request->getQuery("location");
	    $timeRange = Yii::app()->request->getQuery("timeRange");
	    $offset = Yii::app()->request->getQuery("offset");
	    $rowCount = Yii::app()->request->getQuery("rowCount");
	    
	    $locationVal = VideoCountHelper::EVERYWHERE;
	    if (!is_null($location)) {
		$locationConst = 'VideoCountHelper::'.$location;
		if (defined($locationConst)) {
		    $locationVal = constant($locationConst);
		}
	    }
	    
	    $timeRangeVal = VideoCountHelper::WEEKLY;
	    if (!is_null($timeRange)) {
		$timeRangeConst = 'VideoCountHelper::'.$timeRange;
		if (defined($timeRangeConst)) {
		    $timeRangeVal = constant($timeRangeConst);
		}
	    }
	    
	    $offsetVal = is_null($offset) ? VideoCountHelper::DEFAULT_OFFSET : intval($offset);
	    $rowCountVal = is_null($rowCount) ? VideoCountHelper::DEFAULT_ROW_COUNT : intval($rowCount);
	    
	    // retrieve ip
	    $clientIp = Yii::app()->request->userHostAddress;
	    
	    // query and return result
	    $top = VideoCountHelper::queryTop($clientIp, 
			$locationVal, $timeRangeVal, $offsetVal, $rowCountVal);
	    $result = array();
	    foreach($top as $row) {
		$result[] = array("youtubeId" => $row->getYoutubeId(), 
		    "count" => $row->getCount());
	    }
	    echo(json_encode($result));
	}
	
	public function actionError() {
		$error = Yii::app()->errorHandler->error;
	    if ($error)
	    	$this->render('site/error', array('error'=>$error));
	    else
	    	throw new CHttpException(404, 'Page not found.');
	}

	public function actionStat() {
		$youtube_id = Yii::app()->request->getPost("id");
		$this->stat($youtube_id);
	}

	private function stat($id) {
	    // update count
	    $clientIp = Yii::app()->request->userHostAddress;
	    if (is_array($id)) {
			foreach ($id as $i) {
			    VideoCountHelper::increaseCount($clientIp, $i);
			    VideoCountHelper::updateVideoDate($i);
			}
	    } else {
			VideoCountHelper::increaseCount($clientIp, $id);
			VideoCountHelper::updateVideoDate($id);
	    }
	}

}