const { ipcRenderer } = require("electron");

const loginForm = document.getElementById('loginForm');
loginForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const email = document.getElementById('inputEmail').value;
  const password = document.getElementById('inputPassword').value;

  console.log("Formulario enviado con:", email, password);

  // Enviar datos al proceso principal para autenticar
  const response = await ipcRenderer.invoke('login', email, password);

  if (response.success) {
    console.log('Inicio de sesión exitoso');
    // Enviar un mensaje al proceso principal para cargar index.html
    ipcRenderer.send('load-main'); // Evento para cargar index.html en el proceso principal
  } else {
    console.log('Error de autenticación');
    alert('Credenciales incorrectas');
    ipcRenderer.send('load-register'); // Evento para cargar el registro si las credenciales son incorrectas
  }
});
