<?php

require_once Yii::app()->basePath.'/vendor/autoload.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GeoIp2\Database\Reader;

/**
 * Description of GeoIP2Component
 *
 * @author Jimmy
 */
class GeoIP2Component extends CApplicationComponent{
    
    // the city mmdb path
    private $_cityMmdb = null;
    // the city reader object
    private $_reader = null;
    
    public function setCityMmdb($cityMmdb) {
        $this->_cityMmdb = $cityMmdb;
    } 
    
    public function getReader() {
        return $this->_reader;
    }
    
    public function init() {
        if ($this->_cityMmdb != null) {
            $this->_reader = new Reader($this->_cityMmdb);
        }
    }
}
