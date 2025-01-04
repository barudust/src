const express = require('express');
const router = express.Router();
const budgetController = require('../controllers/budgetController');

// Rutas para manejar presupuestos
router.post('/presupuestos', budgetController.registerBudget); // CI-01
router.put('/presupuestos/:id', budgetController.modifyBudget); // CI-02
router.delete('/presupuestos/:id', budgetController.deleteBudget); // CI-03
router.get('/presupuestos', budgetController.visualizeBudgets); // CI-04

module.exports = router;
