$('#tb-registros').on('click','.btn-delete',function() {
    var cod = $(this).attr('cod');
    if ( confirm('confirma a exclusão?') ) {
        $.ajax('./ajax/delete.php', {
            method: 'post',
            data: {cod: cod},
            success: function ( response ) {
                carregaTbConsulta();
            }, error: function ( response ) {
                var responseText = JSON.parse(response.responseText);
                switch (response.status) {
                    case 302:
                        window.location.href = responseText.href;
                        break;
                    default:
                        alert(responseText.message);
                        break;
                }
            }
        })
    }
});

$(document).ready( function () {
    carregaTbConsulta();
});

function carregaTbConsulta() {
    $('#td-carregando').removeClass('hidden');
    $('#tb-registros > tbody').empty();
    var bolsista = document.getElementById('select-bolsista').value;
    var mes = document.getElementById('select-mes').value;
    var ano = document.getElementById('select-ano').value;
    $.ajax('./ajax/tabelaconsulta.php',{
        method: 'get',
        data: {
            bolsista: bolsista,
            ano: ano,
            mes: mes
        }, success: function ( response ) {
            $('#tb-registros > tbody').html(response);
            $('#td-carregando').addClass('hidden');

        }, error: function ( response ) {
            alert('nao foi');
        }
    });
}