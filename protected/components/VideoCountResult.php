<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VideoCountResult
 *
 * @author Jimmy
 */
class VideoCountResult {
    // string
    private $youtubeId;
    // string
    private $title;
    // int
    private $count;
    
    public function getYoutubeId() {
	return $this->youtubeId;
    }

    public function setYoutubeId($youtubeId) {
	$this->youtubeId = $youtubeId;
    }
    
    public function getCount() {
        return $this->count;
    }

	public function getTitle() {
	    return $this->title;
	}

    public function setCount($count) {
        $this->count = $count;
    }
    
    public function setTitle($title) {
    	$this->title = $title;
    }

}
