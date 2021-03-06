$('#tb-registros').on('click','.btn-delete',function() {

    var $btn = $(this);
    var cod = $(this).attr('cod');

    confirmModal('Confirma a exclusão deste registro?', function () {
        $.ajax('./ajax/delete.php', {
            method: 'post',
            data: {cod: cod},
            success: function ( response ) {
                btnCarregando( $btn );
                carregaTbConsulta( function () {
                    showMessage('[PONTO ELETRÔNICO]','Registro removido!')
                });

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
    });

});

$(document).ready( function () {
    showTdCarregando();
    $('#tb-registros > tbody').empty();
    carregaTbConsulta();
});

function carregaTbConsulta( callbackSuccess , callbackError ) {
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
            hideTdCarregando();
            $('#tb-registros > tbody').empty();
            $('#tb-registros > tbody').html(response);
            $('[data-toggle="tooltip"]').tooltip({
                container: '#tb-registros'
            });
            if ( callbackSuccess !== undefined ) {
                callbackSuccess();
            }
        }, error: function ( response ) {
            showMessage('[PONTO ELETRÔNICO]', 'Permissão negada', 'danger');
            hideTdCarregando();
        }
    });
}


function btnCarregando( $btn ) {
    var $span = $btn.find('span');
    $('.btn-delete').each( function () {
        $(this).attr('disabled','');
    } );
    $span.removeClass('fa*').text('.');
    setInterval( function () {
        var texto = $span.text();
        if (texto.length < 3) {
            $span.text(texto + '.');
        } else {
            $span.text('.')
        }
    } , 500 );
}

new Egg("up,up,down,down,left,right,left,right,b,a", function() {
    $('.fa-trash-o').each( function ( ) {
        $(this).removeClass('fa-trash-o').addClass('fa-trophy');
    })
}).listen();