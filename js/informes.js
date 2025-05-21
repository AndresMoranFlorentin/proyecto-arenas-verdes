document
  .getElementById("formulario-informes")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Evita que la página se recargue

    const fecha = document.getElementById("fecha").value;
    const tipoInforme = document.getElementById("tipo_informe").value;
    const resultadoDiv = document.getElementById("resultado-informe");

    if (!fecha) {
      resultadoDiv.innerHTML =
        "<p style='color: red;'>Por favor, seleccione una fecha.</p>";
      return;
    }

    fetch(`generar_informe_ajax?fecha=${fecha}&tipo_informe=${tipoInforme}`)
      .then((response) => response.text())
      .then((data) => {
        resultadoDiv.innerHTML = `<h3>Resultado del Informe</h3><p>${data}</p>`;
      })
      .catch((error) => {
        resultadoDiv.innerHTML =
          "<p style='color: red;'>Ocurrió un error al generar el informe.</p>";
        console.error("Error:", error);
      });
  });
