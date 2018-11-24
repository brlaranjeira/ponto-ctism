$('.input-data').mask('00/00/0000');
$('#horasabono,#minutosabono').mask('00');


$('#form-abonar').submit( function ( evt ) {
    $.ajax('./ajax/actionabonar.php', {
        method: 'post',
        data: {
            dataabono: document.getElementById('dataabono').value,
            horasabono: document.getElementById('horasabono').value,
            minutosabono: document.getElementById('minutosabono').value,
            motivoabono: document.getElementById('motivoabono').value
        },
        success: function ( response ) {
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
    return false;
});