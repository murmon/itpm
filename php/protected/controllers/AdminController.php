<?php

class AdminController extends Controller
{
    public function actionIndex()
    {
        if(!User::isAdmin()){
            throw new CHttpException(404, 'Not found.');
        }

        $this->render('index');
    }
}