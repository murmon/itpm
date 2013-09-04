<?php
/**
 * Клас работы с Webmoney
 */

require_once dirname(dirname(__FILE__)).'/EOAuth2Service.php';


/**
 * GitHub provider class.
 * @package application.extensions.eauth.services
 */
class WebmoneyOAuthService extends EOAuth2Service {

    protected $name = 'webmoney';
    protected $title = 'Webmoney';
    protected $type = 'OAuth';
    protected $jsArguments = array('popup' => array('width' => 500, 'height' => 750));

    protected $client_id = '';
    protected $wmid = '';
    protected $client_secret = '';
    protected $scope = '';

    protected $lang = '';
    protected $serviceName = 'webmoney';
    protected $error = "";

    protected $providerOptions = array(
        'authorize' => 'https://login.wmtransfer.com/GateKeeper.aspx',
        'access_token' => 'https://login.wmtransfer.com/GateKeeper.aspx',
    );

    protected $uid = null;
    /*
        WmLogin_AuthType – способ аутентификации. Значения:
            KeeperClassic – аутентификация через WebMoney Keeper Classic
            KeeperLight - аутентификация через сертификат WebMoney Keeper Light
            Enum – аутентификация через сервис E-num
            Telepat – аутентификация через сервис telepat
        WmLogin_Created - UTC время создания авторизационного тикет в формате dd.mm.yyyy hh:mm:ss
        WmLogin_Expires - UTC время истечения срока действия тикет в формате dd.mm.yyyy hh:mm:ss
        WmLogin_LastAccess - UTC время последнего обращения к тикету в формате dd.mm.yyyy hh:mm:ss
        WmLogin_Ticket - авторизационный тикет. Удовлетворяет регулярному выражению [a-zA-Z0-9\$\!\/]{32,48}
        WmLogin_UrlID - urlid возврата, на который осуществляется POST
        WmLogin_UserAddress - IP адрес пользователя
        WmLogin_WMID – WMID пользователя
     */

    protected function fetchAttributes() {
        $this->attributes['error'] = $this->error;
        $this->attributes['name'] = $_POST['WmLogin_Ticket'];
        $this->attributes['id'] = $_POST['WmLogin_WMID'];
        $this->attributes['login_type'] = $_POST['WmLogin_AuthType'];

        $this->attributes['Login_Created'] = $_POST['WmLogin_Created'];
        $this->attributes['Login_Expires'] = $_POST['WmLogin_Expires'];
    }


    /**
     * Returns the url to request to get OAuth2 code.
     * @param string $redirect_uri url to redirect after user confirmation.
     * @return string url to request.
     */
    protected function getCodeUrl($redirect_uri) {
        $this->setState('redirect_uri', $redirect_uri);
        return $this->providerOptions['authorize'].'?RID='.$this->client_id.'&lang='.$this->lang;
    }

    /**
     * Save access token to the session.
     * @param stdClass $token access token object.
     */
    protected function saveAccessToken($token) {
        $this->setState('auth_token', $token["access_token"]);
        $this->setState('uid', $token["user_id"]);
        $this->setState('expires', $token["expires_in"] === 0 ? (time()*2) : (time() + $token["expires_in"] - 60));
        $this->uid = $token["user_id"];
        $this->access_token = $token["access_token"];
    }

    /**
     * Restore access token from the session.
     * @return boolean whether the access token was successfuly restored.
     */
    protected function restoreAccessToken() {
        if ($this->hasState('uid') && parent::restoreAccessToken()) {
            $this->uid = $this->getState('uid');
            return true;
        }
        else {
            $this->uid = null;
            return false;
        }
    }

    /**
     * Returns the error info from json.
     * @param stdClass $json the json response.
     * @return array the error array with 2 keys: code and message. Should be null if no errors.
     */
//    protected function fetchJsonError($json) {
//        if (isset($json->error)) {
//            return array(
//                'code' => is_string($json->error) ? 0 : $json->error->error_code,
//                'message' => is_string($json->error) ? $json->error : $json->error->error_msg,
//            );
//        }
//        else
//            return null;
//    }

    /**
     * _GetAnswer - xml запрос для проверки ТИКЕТА
     * @return xml
     */
    public function _GetAnswer(){ //Проверка тикета

        global $CertPath; // Инициализируем сеанс CURL

        $xml="  <request>
                <siteHolder>$this->wmid</siteHolder>
                <user>".$_POST['WmLogin_WMID']."</user>
                <ticket>".$_POST['WmLogin_Ticket']."</ticket>
                <urlId>".$this->client_id."</urlId>
                <authType>".$_POST['WmLogin_AuthType']."</authType>
                <userAddress>".$_POST['WmLogin_UserAddress']."</userAddress>
                </request>"; //Фрмируем xml-запрос

        $ch = curl_init("https://login.wmtransfer.com/ws/authorize.xiface"); // В выводе CURL http-заголовки не нужны
        curl_setopt($ch, CURLOPT_HEADER, 0); // Возвращать результат, а не выводить его в браузер
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // Метод http-запроса - POST
        curl_setopt($ch, CURLOPT_POST,1); // Что передаем?
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // Задаем корневой сертификат для проверки
        //curl_setopt($ch, CURLOPT_CAINFO, $CertPath);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); // Выполняем запрос, ответ помещаем в переменную $result;
        $result=curl_exec($ch);
        if(curl_errno($ch))
            echo "Curl Error number = ".curl_errno($ch).", Error desc = ".curl_error($ch)."<br>";
        curl_close($ch);
        return $result;
    }

    /**
     * _isticket - проверка подлиности ТИКЕТА
     * @return xml
     */
    public function isticket(){ //Проверка подлиности Тикета
        $testticket=preg_match('/^[a-zA-Z0-9\$\!\/]{32,48}$/i', $_POST['WmLogin_Ticket']);
        if($_POST['WmLogin_UrlID']==$this->client_id && $testticket==1) {
            // Отправляем запрос и получаем ответ
            $resxml=$this->_GetAnswer();
            $xmlres = simplexml_load_string($resxml);
            if(!$xmlres) {
                $this->error = "Не получен XML-ответ";
                $this->iserror = true;
                return false;
            }else{
                $result=strval($xmlres->attributes()->retval);
                // Если результат не равен 0 - прерываем и выдаем ошибку
                if($result!=0) {
                    $this->error= "Тикет ошибочный :(";
                    $this->iserror = true;
                    return false;
                }else{
                    $this->authenticated = true; //Тикет продлиный, можно логинить
                    $this->iserror = false; //Ошибок нет
                    return true;
                }
            }
        }else {
            $this->error= "=== Ошибка при получении тикета ===";
            $this->iserror = true;
            $this->authenticated = false;
            return false;
        }
    }

    /**
     * Authenticate the user.
     * @return boolean whether user was successfuly authenticated.
     */
    public function authenticate() {
        // Проверка есть ли ответ от webmoney
        if (isset($_POST['WmLogin_Ticket'])) {
            if (!$this->isticket()){// если есть ответ от webmoney, проверям подлиность полученого ответа
                $this->cancel(); //закрывем попап если ответ неверный!
            }
        }
        // Redirect to the authorization page
        else if (!$this->restoreAccessToken()) {
            // Use the URL of the current page as the callback URL.
            if (isset($_GET['redirect_uri'])) {
                $redirect_uri = $_GET['redirect_uri'];
            }
            else {
                $server = Yii::app()->request->getHostInfo();
                $path = Yii::app()->request->getUrl();
                $redirect_uri = $server.$path;
            }
            $url = $this->getCodeUrl($redirect_uri);
            Yii::app()->request->redirect($url);
        }
        return $this->getIsAuthenticated();

    }


}