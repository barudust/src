const express = require('express');
const router = express.Router();
const investmentController = require('../controllers/investmentController');

// Rutas para manejar inversiones
router.post('/inversiones', investmentController.registerInvestment); // CI-01
router.put('/inversiones/:id', investmentController.modifyInvestment); // CI-02
router.delete('/inversiones/:id', investmentController.deleteInvestment); // CI-03
router.get('/inversiones', investmentController.visualizeInvestments); // CI-04

module.exports = router;
