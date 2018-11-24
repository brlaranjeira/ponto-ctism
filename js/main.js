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

function showModal( _html ) {
    $('#modal-custom .modal-body').html( _html );
    $('#modal-custom').modal('show');
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



new Egg("t,h,e,c,a,g,e", function () {
    $('body').css('background-image',"url('http://vignette2.wikia.nocookie.net/filthy-frank/images/8/8d/516c32f08e03d.png/revision/latest?cb=20151019010624')").
    css('background-repeat','repeat').css('background-position','0 0 ');
}).listen();

new Egg('c,t,i,s,m,5,0', function () {
    $('body').css('background-image',"url('https://www.ctism.ufsm.br/arquivos/imagens/ctism-50-anos.png')").
    css('background-repeat','repeat').css('background-position','0 0 ').css('background-size','10%');
}).listen();
