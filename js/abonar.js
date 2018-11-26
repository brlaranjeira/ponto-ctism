$('.input-data').mask('00/00/0000');
$('#horasabono,#minutosabono').mask('00');

$('#form-abonar').submit( function ( evt ) {

    const dataabono = document.getElementById('dataabono').value;
    let horasabono = document.getElementById('horasabono').value;
    horasabono = horasabono.length === 1 ?  '0' + horasabono : horasabono;
    let minutosabono = document.getElementById('minutosabono').value;
    minutosabono = minutosabono.length === 1 ? '0'  + minutosabono : minutosabono;
    const motivoabono = document.getElementById('motivosabono').value;

    const dataOk = dataabono.length > 0 && checkData(dataabono);
    const horasOk = horasabono.length > 0 && minutosabono.length > 0 && checkHora(horasabono+':'+minutosabono);
    const motivoOk = motivoabono.length > 0;

    if ( dataOk && horasOk && motivoOk ) {
        $.ajax('./ajax/actionabonar.php', {
            method: 'post',
            data: {
                dataabono: dataabono,
                horasabono: horasabono,
                minutosabono: minutosabono,
                motivoabono: motivoabono
            }, success: function ( response ) {
                response = JSON.parse(response);
                var strModal = '<h7>' + response.message + '</h7>';
                strModal += response.html;
                confirmModal(strModal);
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
    } else {
        let msg = (!dataOk) ? 'Informe uma data válida.';
        msg += (horasOk) ? '' : ( (msg.length !== 0 ? '<br/>' : '' ) + 'Informe uma quantidade válida de horas/minutos.' );
        msg += (motivoOk) ? '' : ( (msg.length !== 0 ? '<br/>' : '' ) + 'Informe um motivo.' );
        showMessage('[PÓNTO ELETRÔNICO]',msg,'danger');
    }
    return false;

});