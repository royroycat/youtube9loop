<?php

/**
 * This is the model class for table "video_count".
 *
 * The followings are the available columns in table 'video_count':
 * @property string $date
 * @property string $continent_name
 * @property string $country_name
 * @property string $city_name
 * @property string $youtube_id
 * @property integer $video_count
 */
class VideoCount extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'video_count';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, continent_name, country_name, city_name, youtube_id, video_count', 'required'),
			array('video_count', 'numerical', 'integerOnly'=>true),
			array('continent_name, youtube_id', 'length', 'max'=>31),
			array('country_name, city_name', 'length', 'max'=>127),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('date, continent_name, country_name, city_name, youtube_id, video_count', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'date' => 'Date',
			'continent_name' => 'Continent Name',
			'country_name' => 'Country Name',
			'city_name' => 'City Name',
			'youtube_id' => 'Youtube',
			'video_count' => 'Video Count',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('date',$this->date,true);
		$criteria->compare('continent_name',$this->continent_name,true);
		$criteria->compare('country_name',$this->country_name,true);
		$criteria->compare('city_name',$this->city_name,true);
		$criteria->compare('youtube_id',$this->youtube_id,true);
		$criteria->compare('video_count',$this->video_count);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VideoCount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
