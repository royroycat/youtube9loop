<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VideoCountHelper
 *
 * @author Jimmy
 */
class VideoCountHelper {
    
    // time range for query
    const WEEKLY = 0;
    const ANYTIME = 1;
    
    // location for query
    const CITY = 0;
    const COUNTRY = 1;
    const CONTINENT = 2;
    const EVERYWHERE = 3;
    
    // default row count
    const DEFAULT_ROW_COUNT = 30;
    const DEFAULT_OFFSET = 0;
    
    // create or update count record with client location info
    public static function increaseCount($ip, $youtubeId) {
        $reader = Yii::app()->geoIp2->reader;
        try {
            $record = $reader->city($ip);
            // update count
            $connection = Yii::app()->db;
            $sql = 'INSERT INTO video_count (date, continent_name, country_name, city_name, youtube_id, video_count) VALUES (CURDATE(), :continent_name, :country_name, :city_name, :youtube_id, 1) '.
                    'ON DUPLICATE KEY UPDATE video_count=video_count+1';
            $command = $connection->createCommand($sql);
            $continent = $record->continent->names['en'];
            $country = $record->country->names['en'];
            $city = $record->city->names['en'];
            if (is_null($city)) {
                $city = '';
            }
            $command->bindParam(":continent_name", $continent, PDO::PARAM_STR);
            $command->bindParam(":country_name", $country, PDO::PARAM_STR);
            $command->bindParam(":city_name", $city, PDO::PARAM_STR);
            $command->bindParam(":youtube_id", $youtubeId, PDO::PARAM_STR);
            $command->execute();
        } catch (Exception $e) {
            // don't throw exception to block normal function
            // log the error if necessary
        }
    }
    
    // update video date info
    public static function updateVideoDate($youtubeId) {
        try {
            // update count
            $connection = Yii::app()->db;
            $sql = 'INSERT INTO video_date (youtube_id, first_view_date, last_view_date) VALUES (:youtube_id, NOW(), NOW()) '.
                    'ON DUPLICATE KEY UPDATE last_view_date=NOW()';
            $command = $connection->createCommand($sql);
            $command->bindParam(":youtube_id", $youtubeId, PDO::PARAM_STR);
            $command->execute();
        } catch (Exception $e) {
            // don't throw exception to block normal function
            // log the error if necessary
        }
    }
    
    // return an array of VideoCountResult
    public static function queryTop($ip, 
	    $location=self::EVERYWHERE, 
            $timerange=self::ANYTIME,
	    $offset=self::DEFAULT_OFFSET,
	    $rowCount=self::DEFAULT_ROW_COUNT) {
        $result = array();
        // result is an array of VideoCountResult
        $sql = 'SELECT youtube_id, SUM(video_count) count FROM video_count WHERE ';
        $param = array();
        if ($location == self::EVERYWHERE) {
            $sql .= '1=1';
        } else {
            // retrieve client location
            $reader = Yii::app()->geoIp2->reader;
            $record = $reader->city($ip);
            $continent = $record->continent->names['en'];
            $country = $record->country->names['en'];
            if (is_null($country)) {
                $country = '';
            }
            $city = $record->city->names['en'];
            if (is_null($city)) {
                $city = '';
            }
            // generate location condition
            if ($location == self::CONTINENT) {
                $sql .= 'continent_name=:continent_name';
                $param[':continent_name'] = $continent;
            } else if ($location == self::COUNTRY) {
                $sql .= 'continent_name=:continent_name AND country_name=:country_name';
                $param[':continent_name'] = $continent;
                $param[':country_name'] = $country;
            } else if ($location == self::CITY) {
                $sql .= 'continent_name=:continent_name AND country_name=:country_name AND city_name=:city_name';
                $param[':continent_name'] = $continent;
                $param[':country_name'] = $country;
                $param[':city_name'] = $city;
            }
        }
        // generate time range condition
        if ($timerange == self::WEEKLY) {
            $sql .= ' AND date between date_sub(now(),INTERVAL 1 WEEK) and now() ';
        }
        $sql .= ' GROUP BY youtube_id ORDER BY SUM(video_count) DESC, youtube_id';
		$sql .= sprintf(' LIMIT %d, %d', $offset, $rowCount);
        
        // query count
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        // bind parameters
        
        foreach ($param as $key => &$value) {
            $command->bindParam($key, $value, PDO::PARAM_STR);
        }
	
	// bind column
	$dataReader = $command->query();
	$dataReader->bindColumn(1, $youtubeId);
	$dataReader->bindColumn(2, $count);
	
	while(($row = $dataReader->read()) !== false) {
	    $entry = new VideoCountResult();
	    $entry->setYoutubeId($youtubeId);
	    $entry->setCount($count);
	    $result[] = $entry;
	}
	
        return $result;
    }
    
    private static function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }
}