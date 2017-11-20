
$('#div-deferir') .on("click", ".btn-deferir", function () {
    alert("DEFERIDO");
    window.location.reload();
});

$('#div-deferir') .on("click", ".btn-indeferir", function () {
    alert("NÃO DEFERIDO");
    window.location.reload();
});