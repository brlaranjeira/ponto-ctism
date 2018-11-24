$(document).ready( function () {
    var hrInicialBrowser = Date.now() ;
    setInterval( function () {
        var hrInicialServer = parseInt($('#horario').attr('hrinicial'))*1000;
        var delta = Date.now() - hrInicialBrowser;
        var hrFinal = parseInt(hrInicialServer) + parseInt(delta);
        var ts = new Date( hrFinal  );

        var h = ts.getHours().toString();
        var m = ts.getMinutes().toString();
        var s = ts.getSeconds().toString();
        h = h.length == 1 ? '0' + h : h;
        m = m.length == 1 ? '0' + m : m;
        s = s.length == 1 ? '0' + s : s;

        $('#horario').text(h + ':' + m + ':' + s);

    },1000);
    var $spanMensagem = $('#span-mensagem');
    if ($spanMensagem.length) {
        var mensagem = $spanMensagem.text();
        showMessage('[PONTO ELETRÔNICO]',mensagem);
    }

    $('#div-registrar').on('click','.btn-registro',function() {
        var evt = $(this).attr('evt');
        var $thisBtn = $(this);
        var $otherBtn = $('#div-registrar').find('button.btn-registro').not('[evt="' + evt + '"]').first();
        $.ajax('./ajax/registrar.php', {
            type: 'post',
            data: { evt: evt },
            success: function ( response ) {
                response  = JSON.parse( response );
                showMessage('PONTO ELETRÔNICO]', response.message);
                $thisBtn.removeClass('btn-hl');
                $otherBtn.addClass('btn-hl');
            }, error: function ( response ) {
                var responseText = JSON.parse(response.responseText);
                switch (response.status) {
                    case 302:
                        window.location.href = responseText.href;
                        break;
                    default:
                        showMessage('[PONTO ELETRÔNICO]',responseText.message,'danger');
                        break;
                }
            }
        })
    })

});