function iniciarSesion() {
    const correo = document.getElementById('correo').value;
    const nombre = document.getElementById('nombre').value;
    const contrasena = document.getElementById('contrasena').value;

    // Comprobación de credenciales
    if (correo === "RepasoDeCuentas@gmail.com" && nombre === "Admin" && contrasena === "RepasoCuentas") {
        alert("Inicio de sesión exitoso.");
        // Redirigir a inicioIII
        window.location.href = "inicioIII.html"; // Redirige a inicioIII
    } else {
        alert("Credenciales incorrectas. Intenta nuevamente.");
    }
}
