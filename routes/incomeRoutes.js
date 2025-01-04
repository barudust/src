const express = require('express');
const router = express.Router();
const incomeController = require('../controllers/incomeController');

// Rutas para manejar ingresos
router.post('/ingresos', incomeController.registerIncome); // CI-01
router.put('/ingresos/:id', incomeController.modifyIncome); // CI-02
router.delete('/ingresos/:id', incomeController.deleteIncome); // CI-03
router.get('/ingresos', incomeController.visualizeIncomes); // CI-04

module.exports = router;
