<?php

class UserIdentity extends CUserIdentity
{

    private $soc_ident = null;
    private $_id;
    private $service;

    public $user;


    public function __construct($serv)
    {
        $this->soc_ident = $serv["service"];
    }


    public function getId()
    {
        return $this->_id;
    }

    public function getService()
    {
        return $this->service;
    }


    public function createUser($username, $email, $service_id)
    {
        $user = new User; //создаем нового юзера
        $user->setAttribute("email", $email);
        $user->setAttribute("google_service_id", $service_id);
        $user->setAttribute("username", $username);
        if (!$user->save()) { // сохраняем нового юзера
            throw new CHttpException(403, 'Ошибка создания нового юзера.');
        }
        return $user;
    }


    public function authenticate()
    {
        if ($this->soc_ident instanceof EAuthUserIdentity) {

            $user = User::model()->find('google_service_id=?', array($this->soc_ident->id));
            if ($user === null) {
                $username = $this->soc_ident->service->attributes["name"];
                $email = $this->soc_ident->service->attributes["email"];
                $service_id = $this->soc_ident->id;

                $new_user = $this->createUser($username, $email, $service_id); // Создаем нового Юзера

//                if (isset($this->soc_ident->service->attributes["photo"])) {
//                    $new_user->photo = $this->soc_ident->service->attributes["photo"];
//                }

                $this->user = $new_user;
                $this->errorCode = self::ERROR_NONE; // ошыбок нет
            } else {
                $this->errorCode = self::ERROR_NONE;
            }

            $user->last_login_at = new CDbExpression('NOW()');
            $user->save();

            $this->_id = $user->id;
            $this->setState('name', $user->username);
            return !$this->errorCode;
        } else {
            return false;
        }
    }
}