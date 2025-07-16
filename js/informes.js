function mostrarCampos() {
  const tipo = document.getElementById("tipoReserva").value;
  document.getElementById("campoUnico").style.display = tipo === "dia" ? "block" : "none";
  document.getElementById("campoRango").style.display = tipo === "rango" ? "block" : "none";
}

document.getElementById("formulario-informes").addEventListener("submit", function (event) {
  event.preventDefault(); // Evita que la página se recargue

  const tipoInforme = document.getElementById("tipo_informe").value;
  const tipoReserva = document.getElementById("tipoReserva").value;
  const resultadoDiv = document.getElementById("resultado-informe");

  const fechaUnica = document.getElementById("fecha").value;
  const fechaInicio = document.getElementById("fechaInicio").value;
  const fechaFin = document.getElementById("fechaFin").value;

  let url = "";

  // Validaciones y armado de URL
  if (tipoReserva === "dia") {
    if (!fechaUnica) {
      resultadoDiv.innerHTML = "<p style='color: red;'>Por favor, seleccione una fecha.</p>";
      return;
    }
    url = `generar_informe_ajax?fecha=${fechaUnica}&tipo_informe=${tipoInforme}`;
  } else if (tipoReserva === "rango") {
    if (!fechaInicio || !fechaFin) {
      resultadoDiv.innerHTML = "<p style='color: red;'>Debe ingresar ambas fechas para el rango.</p>";
      return;
    }
    else if(fechaInicio>fechaFin){
       resultadoDiv.innerHTML = "<p style='color: red;'>La fecha de Inicio no puede ser mayor a la de fin.</p>";
      return;
    }
    url = `generar_informe_ajax?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&tipo_informe=${tipoInforme}`;
  } else {
    resultadoDiv.innerHTML = "<p style='color: red;'>Seleccione un tipo de informe válido.</p>";
    return;
  }

  // Reutilización del fetch
  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      resultadoDiv.innerHTML = `<h3>Resultado del Informe</h3><p>${data}</p>`;
    })
    .catch((error) => {
      resultadoDiv.innerHTML = "<p style='color: red;'>Ocurrió un error al generar el informe.</p>";
      console.error("Error:", error);
    });
});
