const express = require('express');
const router = express.Router();
const debtController = require('../controllers/debtController');

// Rutas para manejar adeudos
router.post('/adeudos', debtController.registerDebt); 
router.put('/adeudos/:id', debtController.modifyDebt); 
router.delete('/adeudos/:id', debtController.deleteDebt); 
router.get('/adeudos', debtController.visualizeDebts); 

// Rutas para manejar deudas
router.post('/deudas', debtController.registerLoan); 
router.put('/deudas/:id', debtController.modifyDebt); 
router.delete('/deudas/:id', debtController.deleteDebt); 
router.get('/deudas', debtController.visualizeLoans); 

module.exports = router;
