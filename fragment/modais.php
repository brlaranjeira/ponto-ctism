<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 26/11/18
 * Time: 15:25
 */
?>


<div id="div-alert" class="fade alert">
    <strong id="alert-title">Success!</strong><span id="alert-message">Indicates a successful or positive action.</span>
</div>

<div id="modal-confirm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirma&ccedil;&atilde;o</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <!--<div class="btn-group"> -->
                <button type="button" id="btn-cancela-ok" class="btn btn-danger">N&atilde;o</button>
                <button type="button" id="btn-confirm-ok" class="btn btn-success">Sim</button>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

<div id="modal-custom" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
Mensagem
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="modal-mensagem-body" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>