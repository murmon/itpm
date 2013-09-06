
function userChangeStatus(evt){
    var $self = $(this);

    var new_status = 1;

    switch ($self.data('status')){
        case '1':
            new_status = 0;
            break;

        case '0':
            new_status = 1;
            break;
    }


    $.ajax({
        type: "POST",
        url: "/user/changeStatus",
        dataType: 'JSON',
        data: {
            new_status: new_status,
            user_id: $self.data('id')
        },

        beforeSend : function(){
            $self.html($self.html() + "<img src='/images/ajax-loader.gif'/>");
        },

        success: function(data) {

            switch (data.status){
                case '1':
                    $self.data('status', '1');
                    $self.find('a').html('deactivate');
                    $self.find('img').remove();
                    break;

                case '0':
                    $self.data('status', '0');
                    $self.find('a').html('activate');
                    $self.find('img').remove();
                    break;
            }
        }
    });
}

$('.user_change_status').click(userChangeStatus);
