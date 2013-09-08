

$(function () {
    var $txt_field = $('#appendedInputButton');
    var $send_btn = $('#send_msg_btn');
    var $msgs_area = $('div#chat-messages div.column-pad');

    $txt_field.keypress(function (evt) {
        if (evt.keyCode == 13) {
            sendMessage();
            updateMessages();
            $txt_field.val('');
        }
    });

    $send_btn.click(function (evt) {
        $txt_field.val('');
        sendMessage();
        updateMessages();
    });

    function sendMessage() {
        $.ajax({
            type: "POST",
            url: "/chat/send",
            dataType: 'JSON',
            data: {
                text:$txt_field.val()
            },
            success: function(data) {

            }
        });
    }

    function updateMessages() {
        $.ajax({
            type: "POST",
            url: "/chat/messages",
            dataType: 'JSON',
            success: function(data) {
                var msgs = '';
                data.forEach(function(el){
                    console.log(el.created_at);
                    msgs += "<div class='chat-msg'>" +
                        "<span class='time_ago'>" + moment(el.created_at).fromNow() + "</span><strong>" +
                        el.username + "</strong>: <span class='chat-text'>" + el.text + "</span>" +
                        "</div>";
                });
                $msgs_area.html(msgs);
            }
        });
    }

    updateMessages();

    setInterval(updateMessages, 2000);
});

