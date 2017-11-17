$(document).ready( function () {
    var hrInicialBrowser = Date.now() ;
    setInterval( function () {
        var hrInicialServer = parseInt($('#horario').attr('hrinicial'))*1000;
        var delta = Date.now() - hrInicialBrowser;
        var hrFinal = parseInt(hrInicialServer) + parseInt(delta);
        var ts = new Date( hrFinal  );

        var h = ts.getHours().toString();
        var m = ts.getMinutes().toString();
        var s = ts.getSeconds().toString();
        h = h.length == 1 ? '0' + h : h;
        m = m.length == 1 ? '0' + m : m;
        s = s.length == 1 ? '0' + s : s;

        $('#horario').text(h + ':' + m + ':' + s);

    },1000);
});