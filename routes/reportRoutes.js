const express = require('express');
const router = express.Router();
const reportController = require('../controllers/reportController');

// Ruta para generar informes financieros
router.get('/informes-financieros', reportController.generateReport); // CI-01

// Ruta para eliminar informes financieros (específicos o todos)
router.delete('/informes-financieros/:type', reportController.deleteReports); // CI-02

module.exports = router;
