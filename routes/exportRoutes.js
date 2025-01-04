const express = require('express');
const router = express.Router();
const exportController = require('../controllers/exportController');

// Ruta para obtener los datos financieros para exportar
router.get('/exportar-datos', exportController.getFinancialData); // CI-01

// Ruta para eliminar los datos exportados (específicos o todos)
router.delete('/exportar-datos/:type', exportController.deleteExportedData); // CI-02

module.exports = router;
