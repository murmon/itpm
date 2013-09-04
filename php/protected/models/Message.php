<?php

/**
 * This is the model class for table "message".
 *
 * The followings are the available columns in table 'message':
 * @property integer $id
 * @property integer $user_id
 * @property string $text
 * @property string $created_at
 */
class Message extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'message';
	}

	public function rules()
	{
		return array(
			array('user_id, text', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('text', 'length', 'max'=>500),
			array('id, user_id, text, created_at', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id')
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'text' => 'Text',
			'created_at' => 'Created At',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
