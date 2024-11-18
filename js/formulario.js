document.addEventListener('DOMContentLoaded', () => {
  const loginBtn = document.getElementById('loginBtn');
  const overlay = document.getElementById('overlay');
  const cancelBtn = document.getElementById('cancelBtn');
  const content = document.getElementById('content');
  
 
  function inicializar(){}
  inicializar(); 

  // Mostrar el formulario al presionar el botón "Login"
  loginBtn.addEventListener('click', () => {
    overlay.style.display = 'flex';
    content.classList.add('blurred-content');
  });

  // Ocultar el formulario y quitar el efecto borroso al presionar "Cancelar"
  cancelBtn.addEventListener('click', () => {
    overlay.style.display = 'none';
    content.classList.remove('blurred-content');
  });

  // También podrías agregar funcionalidad para enviar el formulario si lo deseas
  document.getElementById('registrationForm').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Formulario enviado');
    overlay.style.display = 'none';
    content.classList.remove('blurred-content');
  });
});
