document.addEventListener("DOMContentLoaded", () => {
  
  const accordion = document.getElementsByClassName("content_box");

  for (i = 0; i < accordion.length; i++) {
    accordion[i].addEventListener("click", function () {
      this.classList.toggle("active");
    });
  }

  /**
   * FUNCIONALIDAD PARA CERRAR MODAL DE LOGIN Y ABRIR EL DE OLVIDE MI CONTRASEÑA
   */
    const loginModal = document.getElementById('loginModal');
    const olvideModal = document.getElementById('olvideModal');
    const olvideLink = document.querySelector('#loginModal a[data-bs-target="olvideModal"]');

    olvideLink.addEventListener('click', function (event) {
        event.preventDefault(); // Evita el comportamiento por defecto del enlace

        // Cerrar el modal de login
        const bootstrapLoginModal = bootstrap.Modal.getInstance(loginModal);
        bootstrapLoginModal.hide();

        // Abrir el modal de "Olvide mi contraseña" después de cerrar el login
        const bootstrapOlvideModal = new bootstrap.Modal(olvideModal);
        bootstrapOlvideModal.show();
    });

});




