const express = require('express');
const router = express.Router();
const financialAnalysisController = require('../controllers/financialAnalysisController');
const Income = require('../models/incomeModel');
const Debt = require('../models/debtModel');

// Ruta para obtener el análisis financiero
router.get('/analisis-financiero', financialAnalysisController.getFinancialAnalysis); // CI-01
// Ruta para eliminar el análisis financiero
router.delete('/analisis-financiero', financialAnalysisController.deleteFinancialAnalysis); // CI-02

// Rutas para manejar ingresos
router.post('/ingresos', (req, res) => {
    const { description, amount } = req.body;
    if (!description || !amount) return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
    Income.create(description, amount, (err, income) => {
        if (err) return res.status(500).json({ message: "Error al registrar el ingreso." });
        res.status(201).json({ message: "Ingreso registrado exitosamente.", income });
    });
});

router.put('/ingresos/:id', (req, res) => {
    const { id } = req.params;
    const { description, amount } = req.body;
    Income.getById(id, (err, income) => {
        if (err || !income) return res.status(404).json({ message: "Ingreso no encontrado." });
        Income.update(id, description || income.description, amount || income.amount, (err) => {
            if (err) return res.status(500).json({ message: "Error al modificar el ingreso." });
            res.json({ message: "Ingreso modificado exitosamente." });
        });
    });
});

router.delete('/ingresos/:id', (req, res) => {
    const { id } = req.params;
    Income.getById(id, (err, income) => {
        if (err || !income) return res.status(404).json({ message: "Ingreso no encontrado." });
        Income.delete(id, (err) => {
            if (err) return res.status(500).json({ message: "Error al eliminar el ingreso." });
            res.json({ message: "Ingreso eliminado exitosamente." });
        });
    });
});

router.get('/ingresos', (req, res) => {
    Income.getAll((err, incomes) => {
        if (err) return res.status(500).json({ message: "Error al obtener los ingresos." });
        if (!incomes.length) return res.status(404).json({ message: "No hay ingresos disponibles." });
        res.json(incomes);
    });
});

module.exports = router;

