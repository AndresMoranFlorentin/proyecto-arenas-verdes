document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editModal');
    const editButton = document.getElementById('editUser');

    editButton.addEventListener('click', function () {
       
        // Obtenemos los atributos data-* del botón
        const nombre = this.getAttribute('data-nombre');
        const apellido = this.getAttribute('data-apellido');
        const dni = this.getAttribute('data-dni');
        const phone = this.getAttribute('data-phone');
        const email = this.getAttribute('data-email');
        const localidad = this.getAttribute('data-localidad');
        
        // Asignamos los valores a los inputs del modal
        document.getElementById('editName').value = nombre;
        document.getElementById('editApellido').value = apellido;
        document.getElementById('editDni').value = dni;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editEmail').value = email;
        document.getElementById('editLocalidad').value = localidad;
    });
});
