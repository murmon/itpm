<?php

class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function filters()
    {
        return array(
            array(
                'application.filters.AccessFilter + view',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        if(!Yii::app()->user->isGuest){
            $this->redirect('view');
        }

        $google_img = CHtml::image('/images/google-login.png');
        $google_img_link = CHtml::link($google_img, $this->createUrl('login', array('service' => 'google')));

        $this->render('/site/index', array(
                'google_img_link' => $google_img_link,
            )
        );
    }

    public function actionView(){
        $messages = Message::model()->with('user')->findAll(array(
                'order' => 't.created_at DESC',
                "limit" => 50,
            )
        );

        $this->render('/site/view', array(
                'messages' => $messages,
            )
        );
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect('index');
        }

        $service = Yii::app()->request->getQuery('service');
        if (isset($service)) {
            $authIdentity = Yii::app()->eauth->getIdentity($service);
            $authIdentity->redirectUrl = Yii::app()->user->returnUrl;

            if ($authIdentity->authenticate()) {
                $identity = new EAuthUserIdentity($authIdentity);
                if ($identity->authenticate()) {
                    $in_ident = new UserIdentity(array("service" => $identity));
                    if ($in_ident->authenticate()) {
                        //1 day
                        Yii::app()->user->login($in_ident, 1 * 24 * 60 * 60);
                        $this->redirect('index');
                    }

                }
            }
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}