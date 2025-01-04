const UserModel = require('../models/userModel');

function crearCuenta(req, res) {
    const { nombre, apellidos, telefono, correo } = req.body;

    if (!nombre || !apellidos || !telefono || !correo) {
        return res.status(400).json({ message: "Todos los campos son obligatorios." });
    }

    UserModel.createUser(nombre, apellidos, telefono, correo, (err, user) => {
        if (err) return res.status(500).json({ message: "Error al crear la cuenta." });
        res.status(201).json({ message: "Cuenta creada exitosamente.", user });
    });
}

function modificarInformacion(req, res) {
    const { id } = req.params;
    const { nombre, apellidos, telefono, correo } = req.body;

    UserModel.updateUser(id, nombre || null, apellidos || null, telefono || null, correo || null, (err) => {
        if (err) return res.status(500).json({ message: "Error al modificar la información." });
        res.json({ message: "Información modificada exitosamente." });
    });
}

function eliminarCuenta(req, res) {
    const { id } = req.params;

    UserModel.deleteUser(id, (err) => {
        if (err) return res.status(500).json({ message: "Error al eliminar la cuenta." });
        res.json({ message: "Cuenta eliminada exitosamente." });
    });
}

function obtenerUsuarios(req,res){
   UserModel.getAllUsers((err,data)=>{
      if(err){
         return res.status(500).json({message:"Error al obtener usuarios."});
      }
      res.json(data);
   })
}

module.exports = {
    crearCuenta,
    modificarInformacion,
    eliminarCuenta,
    obtenerUsuarios
};

function crearCuenta() {
    const nombre = document.getElementById('nombre').value;
    const apellidos = document.getElementById('apellidos').value;
    const telefono = document.getElementById('telefono').value;
    const correo = document.getElementById('correo').value;
 
    fetch('http://localhost:3000/api/registro', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ nombre, apellidos, telefono, correo }),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Mensaje de confirmación
        // Limpiar campos después del registro
        document.getElementById('form-crear-cuenta').reset();
    })
    .catch(error => {
        alert("Error al crear cuenta: " + error.message);
    });
 }
 
 function modificarInformacion() {
    const idToModify = prompt("Ingrese el ID del usuario a modificar:");
    
    const nuevoNombre = document.getElementById('nuevo-nombre').value || null;
    const nuevosApellidos = document.getElementById('nuevo-apellidos').value || null;
    const nuevoTelefono = document.getElementById('nuevo-telefono').value || null;
    const nuevoCorreo = document.getElementById('nuevo-correo').value || null;
 
    fetch(`http://localhost:3000/api/usuarios/${idToModify}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ nombre: nuevoNombre , apellidos:nuevosApellidos , telefono:nuevoTelefono ,correo:nuevoCorreo }),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Mensaje de confirmación
        // Limpiar campos después de la modificación
        document.getElementById('form-modificar-info').reset();
    })
    .catch(error => {
        alert("Error al modificar información: " + error.message);
    });
 }
 
 function eliminarCuenta() {
    const idToDelete = prompt("Ingrese el ID de la cuenta a eliminar:");
    
    fetch(`http://localhost:3000/api/usuarios/${idToDelete}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Mensaje de confirmación
    })
    .catch(error => {
        alert("Error al eliminar cuenta: " + error.message);
    });
 }
 