<?php

require_once Yii::app()->basePath . '/vendor/autoload.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of YoutubeComponent
 *
 * @author jimmy
 */
class YoutubeComponent extends CApplicationComponent {

  // youtube key for retrieving thumbnail from youtube
  private $_youtubeApiKey = null; // form conf
  
  function getYoutubeApiKey() {
    return $this->_youtubeApiKey;
  }

  function setYoutubeApiKey($_youtubeApiKey) {
    $this->_youtubeApiKey = $_youtubeApiKey;
  }
      
  /**
   * Retrieve the video info from youtube API according to the input video ID
   * 
   * @param string $youtubeId the youtube video ID
   * @return mixed the json_decode value of the response from Youtube API. 
   * 	    (Please refer to Youtube video list API)
   * @throws Exception thrown when cURL call is failed
   */
  public function getVideoInfo($youtubeId) {
    $client = new Google_Client();
    $client->setApplicationName("youtube9loop");
    $client->setDeveloperKey($this->_youtubeApiKey);
    $youtube = new Google_Service_YouTube($client);
    return $youtube->videos->listVideos('snippet', array('id' => $youtubeId));
  }
  
  /**
   * Retrieve the video title from youtube API according to the input 
   * video ID
   * @param string $youtubeId Youtube video ID
   * @return string the video title (Please refer to the thumbnail 
   * 	    part of Youtube video list API)
   * @throws Exception thrown when exception occurs when retrieving video 
   * 	    info or video info not found
   */
  public function getVideoTitle($youtubeId) {
    try {
      $videoInfo = $this->getVideoInfo($youtubeId);
    } catch (Exception $e) {
      // rethrow
      throw $e;
    }
    if (count($videoInfo->getItems()) < 1) {
      throw new Exception('video info not found');
    }
    $youtubeItems = $videoInfo->getItems();
    return $youtubeItems[0]->getSnippet()->getTitle();
  }

  /**
   * Retrieve the thumbnail info from youtube API according to the input 
   * video ID
   * @param string $youtubeId Youtube video ID
   * @return object the thumbnail info object (Please refer to the thumbnail 
   * 	    part of Youtube video list API)
   * @throws Exception thrown when exception occurs when retrieving video 
   * 	    info or video info not found
   */
  public function getThumbnailInfo($youtubeId) {
    try {
      $videoInfo = $this->getVideoInfo($youtubeId);
    } catch (Exception $e) {
      // rethrow
      throw $e;
    }
    if (count($videoInfo->getItems()) < 1) {
      throw new Exception('video info not found');
    }
    $youtubeItems = $videoInfo->getItems();
    return $youtubeItems[0]->getSnippet()->getThumbnails();
  }

  /**
   * Retrieve the thumbnail path from youtube API according to the input 
   * video ID. The component will return the URL of first available thumbnail 
   * type
   * @param string $youtubeId Youtube video ID
   * @return string the URL of the first available thumbnail type
   * @throws Exception thrown when failed to retrieve video info or there is 
   * 	    no valid thumbnail type
   */
  public function getThumbnailPath($youtubeId) {
    try {
      $thumbnailInfo = $this->getThumbnailInfo($youtubeId);
    } catch (Exception $e) {
      throw $e;
    }
    if ($thumbnailInfo->getMaxres()) {
      return $thumbnailInfo->getMaxres()->getUrl();
    }
    if ($thumbnailInfo->getStandard()) {
      return $thumbnailInfo->getStandard()->getUrl();
    }
    if ($thumbnailInfo->getHigh()) {
      return $thumbnailInfo->getHigh()->getUrl();
    }
    if ($thumbnailInfo->getMedium()) {
      return $thumbnailInfo->getMedium()->getUrl();
    }
    if ($thumbnailInfo->getDefault()) {
      return $thumbnailInfo->getDefault()->getUrl();
    }
    throw new Exception('No valid thumbnail type');
  }

}
