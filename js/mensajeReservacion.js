function resetForm() {
    document.getElementById('buscadorForm').reset();
}

function cerrarMensajeYResetear() {
    resetForm();
    cerrarMensaje();
}

function cerrarMensaje() {
    var mensaje = document.querySelector('.mensaje');
    if (mensaje) {
        mensaje.style.display = 'none';
    }

    var formulario = document.querySelector('.formulario');
    if (formulario) {
        formulario.style.display = 'block';
    }
}