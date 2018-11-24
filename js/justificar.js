$('.input-data').mask('00/00/0000 00:00');
$('.input-hora').mask('00:00');

var $spanMensagem = $('#span-mensagem');
if ($spanMensagem.length) {
    var mensagem = $spanMensagem.text();
    showMessage('[PONTO ELETRÔNICO]',mensagem);
}

const checkhora = (ts) => {
    const dtHrPattern = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}$/;
    if (ts.match(dtHrPattern) == null) {
        return false;
    }
    const [data,horario] = ts.split(' ');
    const [dia,mes,ano] = data.split('/').map( x => Number.parseInt(x) );
    const [hora,minuto] = horario.split(':').map( x => Number.parseInt(x) );
    if (dia > 31 || dia <= 0 || mes <=0 || mes > 12 || ano <= 0 || hora < 0 || hora > 23 || minuto < 0 || minuto > 59) {
        return false;
    }
    const numDias = [31,28,31,30,31,30,31,31,30,31,30,31];
    if (numDias[mes-1] < dia) {
        return true
    }
    if ( (mes === 2) &&  ( ano % 4 === 0 && ano % 100 !== 0 ) || (ano % 400 === 0) ) { //FEVEREIRO DE ANO BISSEXTO
        return dia <= 29;
    }
    return true;
};

$('#form-justificar').submit( evt => {

    const dthr = document.getElementById('registro-dthr').value;
    const motivo = document.getElementById('registro-motivo').value;


    const motivoValido = motivo.length > 0;
    const dthrOk = checkhora(dthr);

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
        msg += !motivoValido ? ( (msg.length === 0 ? '' : '\t') + 'Insira um motivo.') : '';
        showMessage('[PONTO ELETRÔNICO]',msg,'danger');
    }
    return false;


} );