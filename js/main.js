function showMessage ( title , message , tp='success', time = 5000) {
    var cls = 'alert-'+tp;
    $('#div-alert > #alert-title').text( title.toUpperCase() );
    $('#div-alert > #alert-message').text( ' ' + message );
    $('#div-alert').addClass(cls).addClass('in');
    setTimeout( function () {
        $('#div-alert').removeClass('in').removeClass(cls);
    }, time);
}

function hideTdCarregando() {
    $('#td-carregando').addClass('hidden');
}
function showTdCarregando() {
    $('#td-carregando').removeClass('hidden');
}

function confirmModal( mensagem, callbackOk, callbackCancel, labelOk, labelCancel ) {

    /* LABEL PRINCIPAL */
    mensagem = mensagem === undefined ? 'Confirma?' : mensagem;
    $('#modal-confirm .modal-body').html( mensagem );

    /* BOTAO OK */
    labelOk = labelOk === undefined ? 'Sim' : labelOk;
    $('#btn-confirm-ok').text(labelOk);
    $('#btn-confirm-ok').unbind('click');
    $('#btn-confirm-ok').click( function () {
        $('#modal-confirm').modal('hide');
        $('#modal-confirm').on('hidden.bs.modal', callbackOk);
    } );

    /* BOTAO CANCELA */
    labelCancel = labelCancel === undefined ? 'Não' : labelCancel;
    $('#btn-cancela-ok').text( labelCancel );
    $('#btn-cancela-ok').unbind('click');
    $('#btn-cancela-ok').click(function () {
        $('#modal-confirm').modal('hide');
        $('#modal-confirm').on('hidden.bs.modal', callbackCancel ? callbackCancel : function () {});
    });

    /* DESABILITA EVENTO APOS HIDE E MOSTRA */
    $('#modal-confirm').off('hidden.bs.modal');
    $('#modal-confirm').modal('show');

}
