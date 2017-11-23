$('.input-data').mask('00/00/0000 00:00');
$('.input-hora').mask('00:00');

var $spanMensagem = $('#span-mensagem');
if ($spanMensagem.length) {
    var mensagem = $spanMensagem.text();
    showMessage('[PONTO ELETRÔNICO]',mensagem);
}