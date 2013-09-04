<?php

class ChatController extends Controller
{
	public function actionSend()
	{
		if(Yii::app()->request->isAjaxRequest && isset($_POST['text'])){
            $usr_id = Yii::app()->user->id;

            $msg = new Message;
            $msg->user_id = $usr_id;
            $msg->text = CHtml::encode($_POST['text']);
            if(!$msg->save()){
                $arr = $msg->getErrors();
            }
        }
	}

    public function actionMessages()
    {
        if(Yii::app()->request->isAjaxRequest){
            $msgs = Message::model()->with('user')->findAll(array(
                    'order' => 't.created_at DESC',
                    "limit" => 20,
                )
            );

            $response = array();
            foreach ($msgs as $msg) {
                $response_item ['username'] = $msg->user->username;
                $response_item ['created_at'] = $msg->created_at;
                $response_item ['text'] = $msg->text;
                $response[] = $response_item;
            }
            echo json_encode($response);
        }
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}