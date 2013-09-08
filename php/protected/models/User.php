<?php
class User extends CActiveRecord
{
    const STATUS_ACTIVATED = 1;

    public function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return array(
            array('username', 'required'),
            array('username', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_oauth_id, created_at, username', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'message' => array(self::HAS_MANY, 'Message', 'user_id'),
            'post' => array(self::HAS_MANY, 'Post', 'user_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'google_service_id' => 'Google Id',
            'created_at' => 'Created At',
            'username' => 'Username',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('google_service_id', $this->user_oauth_id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('username', $this->username, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchAdminUsers()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('email', $this->email, true);

        $sort = new CSort();
        $sort->defaultOrder = 'created_at DESC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function isGuest()
    {
        return Yii::app()->user->isGuest;
    }

    public static function isAdmin()
    {
        return in_array(self::load()->username, array( 'Danylo Vivchar', 'kulparoman'));
    }

    public static function getCurrentUser()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);

        if ($user == null) {
            throw new CHttpException('No such user!');
        }

        return $user;
    }

    public static function isActivated($id = false)
    {
        if (!$id) {
            $id = Yii::app()->user->id;
        }

        return self::load($id)->status == self::STATUS_ACTIVATED || self::isAdmin();
    }

    public static function load($id = false)
    {
        if (!$id) {
            $id = Yii::app()->user->id;
        }

        return self::model()->findByPk($id);
    }
}
