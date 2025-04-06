function validarForm() {
    // Obtén todos los checkboxes con el nombre "caracteristicas[]"
    const checkboxes = document.querySelectorAll('input[name="caracteristicas[]"]');
    let alMenosUnoSeleccionado = false;
    // Contenedor para mostrar el mensaje de error
    const mensajeError = document.getElementById('mensajeError');

    // Itera sobre los checkboxes para verificar si alguno está marcado
    checkboxes.forEach((checkbox) => {
      if (checkbox.checked) {
        alMenosUnoSeleccionado = true;
      }
    });
  
    if (!alMenosUnoSeleccionado) {
      alert('Por favor selecciona al menos una característica.');
      //<p class="alert alert-danger">No se encontraron parcelas disponibles con esas características.</p>
      // Muestra el mensaje de error
      //mensajeError.style.display = 'block';
      //mensajeError.textContent = 'Por favor selecciona al menos una característica.';
      return false; // Evita el envío del formulario
    }
    // Oculta el mensaje de error si todo está correcto
    //mensajeError.style.display = 'none';

    return true; // Permite el envío del formulario
  }