$('.input-data').mask('00/00/0000 00:00');
$('.input-hora').mask('00:00');

var $spanMensagem = $('#span-mensagem');
if ($spanMensagem.length) {
    var mensagem = $spanMensagem.text();
    showMessage('[PONTO ELETRÔNICO]',mensagem);
}

$('#form-justificar').submit( function ( evt ) {
    $.ajax('./ajax/actionjustificar.php', {
        method: 'post',
        data: {
            registroevt: document.getElementById('registro-evt').value,
            registromotivo: document.getElementById('registro-motivo').value,
            registrodthr: document.getElementById('registro-dthr').value
        }, success: function ( response ) {
            response = JSON.parse(response)
            confirmModal( '<h7>'+response.message+'</h7>' + response.html );
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
    });
    return false;
} );