const { BrowserWindow } = require("electron");
let window;

// Función para crear la ventana principal
function createWindow() {
  window = new BrowserWindow({
    width: 800,
    height: 600,
    webPreferences: {
      nodeIntegration: true,
    },
  });

  // Carga la URL de tu archivo PHP
  window.loadURL("http://localhost/mi-aplicacion/src/views/login.php"); // Ajusta esta URL según tu configuración
}

module.exports = {
  createWindow,
};
