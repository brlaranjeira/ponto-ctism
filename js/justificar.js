$('.input-data').mask('00/00/0000 00:00');
$('.input-hora').mask('00:00');

var $spanMensagem = $('#span-mensagem');
if ($spanMensagem.length) {
    var mensagem = $spanMensagem.text();
    showMessage('[PONTO ELETRÔNICO]',mensagem);
}

$('#form-justificar').submit( evt => {
    const dthr = document.getElementById('registro-dthr').value;
    const motivo = document.getElementById('registro-motivo').value;

    const motivoValido = motivo.length > 0;
    const dthrOk = checkDataHora(dthr);

    if (motivoValido && dthrOk) {
        $.ajax('./ajax/actionjustificar.php', {
            method: 'post',
            data: {
                registroevt: document.getElementById('registro-evt').value,
                registromotivo: document.getElementById('registro-motivo').value,
                registrodthr: dthr
            }, success: ( response ) => {
                response = JSON.parse(response);
                confirmModal( '<h7>'+response.message+'</h7>' + response.html );
            }, error: ( response ) => {
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
        });
    } else {
        let msg = !dthrOk ? 'Insira uma data válida.' : '';
        msg += !motivoValido ? ( (msg.length === 0 ? '' : '<br/>') + 'Insira um motivo.') : '';
        showMessage('[PONTO ELETRÔNICO]',msg,'danger');
    }
    return false;


} );