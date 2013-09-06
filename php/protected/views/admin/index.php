<?php

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/pages/admin.js', CClientScript::POS_END);

$this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped bordered condensed',
    'dataProvider' => User::model()->searchAdminUsers(),
    //'template'=>"{items}",
    'columns' => array(
        array('name' => 'id', 'header' => '#'),
        array('name' => 'username'),
        array('name' => 'email'),

        array(
            'header'=>'Activation',
            'type'=>'raw',
            'value'=>'CHtml::link( $data->status == 0 ? "activate" : "deactivate")',
            'evaluateHtmlOptions'=>true,
            'htmlOptions'=>array(
                'width'=>'50',
                'class' => 'user_change_status',
                'data-status' => '$data->status',
                'data-id' => '$data->id',
            ),
        ),
    ),
));
?>