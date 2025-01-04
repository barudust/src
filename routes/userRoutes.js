const express = require('express');
const router = express.Router();
const registroController = require('../controllers/registroController');

// Ruta para crear una cuenta
router.post('/registro', registroController.crearCuenta); // CI-01

// Ruta para modificar información del usuario
router.put('/usuarios/:id', registroController.modificarInformacion); // CI-02

// Ruta para eliminar cuenta del usuario
router.delete('/usuarios/:id', registroController.eliminarCuenta); // CI-03

// Ruta para obtener todos los usuarios
router.get('/usuarios', registroController.obtenerUsuarios); // CI-04

module.exports = router;

