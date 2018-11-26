
$('#div-deferir') .on("click", ".btn-deferir", function () {
    var cod = $(this).attr('cod');
    confirmModal("Deseja REALMENTE deferir esse registro?", function () {
        $.ajax('./ajax/deferir.php',{
            type: 'post',
            data:{
                cod: cod
            },
            success: function (response) {
                alert (response);
            }, error: function (response) {
                alert ("ERRO");
            }
        });
        window.location.reload();
    })


});

$('#div-deferir') .on("click", ".btn-indeferir", function () {
    /*var cod = $(this).attr('cod');
    confirmModal("Deseja REALMENTE não deferir esse registro?", function () {
        $.ajax('./ajax/deferir.php',{

            }
        });
        window.location.reload();
    })*/

});