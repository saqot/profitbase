$(function () {
    const $form = $("form");
    if ($form.length) {
        initFormSubmit($form);
    }
    
    const $linksClose = $("a.close-action");
    if ($linksClose.length) {
        initCloseAction($linksClose);
    }
    
});


function initCloseAction($links) {
    $links.off('click').on('click', async function (e) {
        e.preventDefault();
        const $link = $(this);
        if ($link.hasClass('disabled')) {
            return;
        }
        
        $link.addClass('disabled').prop('disabled', true);
        
        const name = $link.attr('data-name');
        const isConfirm = confirm(`Точно закрыть ${name}?`);
        if (isConfirm) {
            const data = {
                'isConfirm': 1,
                'token'    : $link.attr('data-token'),
            };
            data[$link.attr('data-field-id')] = $link.attr('data-id');
            
            await sendAjax($link.attr('href'), data, $link.attr('data-class-msg'));
        }
        
        $link.removeClass('disabled').prop('disabled', false);
    });
}

function initFormSubmit($form) {
    
    
    $form.submit(async function (e) {
        e.preventDefault();
        const $btn = $form.find('button.send');
        $btn.addClass('disabled').prop('disabled', true);
        
        const values = {};
        $.each($form.serializeArray(), (i, field) => {
            values[field.name] = field.value;
        });
        
        await sendAjax($form.attr('action'), values,);
        $btn.removeClass('disabled').prop('disabled', false);
        
    })
}

function sendAjax(action, values, classBlockMessage = 'msg') {
    const $msg = $(`.${classBlockMessage}`);
    
    return new Promise((resolve) => {
        
        $.ajax({
            type    : "POST",
            url     : action,
            data    : values,
            dataType: "json",
            encode  : true,
            headers : {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success : function (result) {
                console.log(result);
                $msg.html('');
                
                if (result.msg !== undefined) {
                    $msg.html(`<div class="alert alert-info mb-2">${result.msg}</div>`);
                }
                if (result.redirect !== undefined) {
                    setTimeout(function () {
                        window.location.replace(result.redirect);
                    }, 1200);
                }
                
                resolve()
            },
            error   : function (xhr) {
                let err = null;
                try {
                    err = JSON.parse(xhr.responseText);
                } catch (e) {
                    err = 'Не удалось распарсить ответ сервера c ошибкой';
                    console.log(xhr.responseText)
                }
                err = err.detail !== undefined ? err.detail : err;
                $msg.html(`<div class="alert alert-danger mb-2">${err}</div>`);
                
                resolve()
            }
        });
    });
    
}