<?php
Yii::app()->clientScript->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile('https://rawgithub.com/timrwood/moment/2.1.0/min/moment.min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/pages/chat.js', CClientScript::POS_BEGIN);
?>

<style type="text/css">
    div.input-append{
        margin-top: 5px;
    }

    div#chat-messages div.column-pad{
        height: 350px;
        overflow: auto;
    }

    div#chat-window{
        margin-top: 50px;
    }

    span.time_ago{
        color: #b1bcbd;
        margin-right: 5px;
    }
</style>

<?php if(!User::isGuest()) : ?>
    <div class="span6 pull-left" id='chat-window'>
        <div class='column-pad'>
            <div id='chat-messages'>
                <div class='column-pad'>
                </div>
            </div>
            <div class='pagination-centered' id='chat-submission'>
                <div class="input-append">
                    <input class="span6" id="appendedInputButton" type="text">
                    <button class="btn btn-primary" type="button" id="send_msg_btn"><i class="icon-comment icon-white"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>