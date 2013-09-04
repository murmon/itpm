<?php
/**
 * Created by JetBrains PhpStorm.
 * User: boom
 * Date: 4/9/13
 * Time: 1:02 PM
 * To change this template use File | Settings | File Templates.
 */

class WebUser extends CWebUser {
    private $_model = null;

    private $email=null;

    private $service=null;

    function getService() {
        return $this->service;
    }

    public function getEmail()
    {
        if($user = $this->getModel()){
            // в таблице User есть поле email
            return $user->email;
        }
    }

    function getRoleName($cod=null) {
        if ($cod==null){$cod=$this->getRole();}
        return Yii::app()->authManager->getrolename($cod);
    }

    function getPublic_key() {
        if($user = $this->getModel()){
            // в таблице User есть поле email
            return $user->public_key;
        }
    }

    private function getModel(){
        if (!$this->isGuest && $this->_model === null){
            $this->_model = User::model()->findByPk($this->id, array('select' => 'email, public_key'));
        }
        return $this->_model;
    }
}