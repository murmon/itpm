<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/flipclock/flipclock.css');


Yii::app()->clientScript->registerScriptFile('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/flipclock/libs/prefixfree.min.js', CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/flipclock/flipclock.min.js', CClientScript::POS_BEGIN);
?>

<div class="clock"></div>

<script type="text/javascript">
    var clock = $('.clock').FlipClock(60*60*24*7*3, {
        clockFace: 'DailyCounter',
        countdown: true
    });
</script>