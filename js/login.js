$('#form-signin').submit( evt => {
    const usr = document.getElementById('login-usr').value;
    const pw = document.getElementById('login-pw').value;
    if ( usr.length === 0 || pw.length === 0 ) {
        showMessage('[PONTO ELETRÔNICO]','Login ou senha não preenchidos','danger');
        return false;
    }
} );