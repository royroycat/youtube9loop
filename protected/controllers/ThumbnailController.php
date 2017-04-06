<?php
/**
 * @property string $youtubeApiKey
 */
class ThumbnailController extends ExtendedController {
    
	const FACEBOOK_WIDTH = 1200;
	const FACEBOOK_HEIGHT = 630;
	const WATERMARK_PATH = '/../img/watermark.png';
    
	public function actionIndex() {
	    header('Content-type: image/png');
	    $youtubeId = Yii::app()->request->getQuery('v');
	    $array = explode(",",$youtubeId);
	    $firstYoutubeId = $array[0];
	    
	    Yii::app()->facebookThumbnail->renderThumbnail($firstYoutubeId);
	}
	
}