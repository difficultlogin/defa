$(function() {
    $('#callback-form').submit(function() {
        var form = $(this);
        var flag = true;

        form.find('*[required]').each(function(index, value) {
            var val = $.trim($(this).val());

            if (!val) {
                $(this).addClass('warning');
                flag = false;
            } else {
                $(this).removeClass('warning');
            }
        });

        var email_field = form.find('input[name="email"]');

        if (!validateEmail($.trim(email_field.val()))) {
            email_field.addClass('warning');
            alert('Field email don\'t valid');
            return false;
        }

        if (flag) {
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serializeArray(),
                dataType: 'JSON',
                success: function(data) {
                    if (data['status'] && data['message']) {
                        alert(data['status'] + ".\n" + data['message']);
                    }
                },
                error: function(data) {
                    alert('System error');
                }
            });
        } else {
            alert('Please complete the required fields');
        }

        return false;
    });

    // Helpers functions

    function validateEmail(email) {
        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(email);
    }

});