document.addEventListener('DOMContentLoaded', () => {

    // Capturar todos los inputs dinámicos una vez creados
    const tabla = document.querySelector('table');

    // Agregar un evento input para validar la entrada
    tabla.addEventListener('input', (e) => {
        if (e.target.tagName === 'INPUT' && e.target.type === 'text') {
            const valor = e.target.value;

            // Si el valor no es numérico, limpiar el input
            if (isNaN(valor) || valor < 0) {
                e.target.value = ''; // Borra el valor no válido
            }
        }
    });

    const botonEditar = document.getElementById('editar-todo');
    const botonGuardar = document.getElementById('guardar-todo');
    const botonCancelar = document.getElementById('cancelar-todo');
    const columnasEditables = document.querySelectorAll('.editable');

    // Evento para editar la tabla
    botonEditar.addEventListener('click', () => {
        columnasEditables.forEach(columna => {
            const valorActual = columna.textContent.trim();
            const nombreColumna = columna.dataset.col;
            columna.innerHTML = `<input type="number" name="${nombreColumna}" 
                value="${valorActual.replace('$ ', '').trim()}" 
                class="form-control" min="0" step="1">`;
        });

        botonEditar.style.display = 'none';
        botonGuardar.style.display = 'inline-block';
        botonCancelar.style.display = 'inline-block';
    });

    // Evento para cancelar los cambios
    botonCancelar.addEventListener('click', () => {
        columnasEditables.forEach(columna => {
            const input = columna.querySelector('input');
            if (input) {
                const valorOriginal = input.getAttribute('value'); // Recuperar el valor original
                columna.innerHTML = `$ ${valorOriginal}`; // Restaurar el texto original
            }
        });

        botonEditar.style.display = 'inline-block';
        botonGuardar.style.display = 'none';
        botonCancelar.style.display = 'none';
    });
});
