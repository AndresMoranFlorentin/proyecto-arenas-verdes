document.getElementById('buscadorForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const reservations = [
        {
            id: 1,
            inicio: "2024-12-01",
            fecha_fin: "2024-12-05",
            personas: 4,
            tipo_de_vehiculo: "auto",
            caracteristicas: ["fogon", "tomaElectrica"]
        },
        {
            id: 2,
            inicio: "2024-12-03",
            fecha_fin: "2024-12-06",
            personas: 2,
            tipo_de_vehiculo: "camioneta",
            caracteristicas: ["accesibilidad"]
        }
    ];

    let inicio = document.getElementById('inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;
    let personas = document.getElementById('personas').value;
    let tipo_de_vehiculo = document.getElementById('tipo_de_vehiculo').value;
    let feature = document.getElementById('caracteristicas').value;

    let filteredReservations = reservations.filter(reservation =>
        reservation.inicio <= inicio &&
        reservation.fecha_fin >= fecha_fin &&
        reservation.personas == personas &&
        reservation.tipo_de_vehiculo == tipo_de_vehiculo &&
        reservation.caracteristicas.includes(feature)
    );

    let resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';

    if (filteredReservations.length > 0) {
        filteredReservations.forEach(reservation => {
            let div = document.createElement('div');
            div.innerHTML = `Reserva : 
            <br>Fecha de inicio: ${reservation.inicio}
            <br>Fecha de fin: ${reservation.fecha_fin}
            <br>Cantidad de personas: ${reservation.personas}
            <br>Tipo de vehículo: ${reservation.tipo_de_vehiculo}
            <br>Características: ${reservation.caracteristicas.join(', ')}`;
            resultsDiv.appendChild(div);
        });
    } else {
        resultsDiv.innerHTML = 'No se encontraron reservas que cumplan con los criterios.';
    }
})