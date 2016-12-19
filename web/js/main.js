function showMessage(container, code, message, visibleSeconds) {
    var $message;

    if (container instanceof jQuery) {
        $message = container;
    }
    else {
        $message = $('#' + container);
    }

    var $close = $message.find('.close');
    var timer = $message.data('timer');
    var codeClassMap = {
        'success': {
            'title': 'Success!',
            'messageClass': 'alert-success',
            'visibleSeconds': 5
        },
        'error': {
            'title': 'Error!',
            'messageClass': 'alert-danger',
            'visibleSeconds': 5
        }
    };

    if (timer) {
        clearTimeout(timer);
    }

    $message.hide();
    $close.off('click');
    $close.click(function(){
        $message.hide();
    });

    $.each(codeClassMap, function (key, value) {
        if (code == key) {
            $message.addClass(value.messageClass);
            $message.find("[data-type='message-title']").html(value.title);
            var messageVisibleSeconds;

            if (typeof visibleSeconds !== 'undefined') {
                messageVisibleSeconds = visibleSeconds;
            }
            else {
                messageVisibleSeconds = value.visibleSeconds;
            }

            if (messageVisibleSeconds > 0) {
                timer = setTimeout(function () {
                    $message.fadeOut('fast').removeData('timer');
                }, value.visibleSeconds * 1000);
            }
            else {
                timer = 0;
            }
        }
        else {
            $message.removeClass(value.messageClass);
        }
    });

    $message.find("[data-type='message-text']").html(message).end()
        .fadeIn('fast').data('timer', timer);
}
