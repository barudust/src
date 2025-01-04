const Income = require('../models/incomeModel');
const FinancialAnalysis = require('../models/financialAnalysisModel');

const incomeController = {
    registerIncome: (req, res) => {
        const { description, amount } = req.body;
        if (!description || !amount) return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
        Income.create(description, amount, (err, income) => {
            if (err) return res.status(500).json({ message: "Error al registrar el ingreso." });
            res.status(201).json({ message: "Ingreso registrado exitosamente.", income });
        });
    },
    modifyIncome: (req, res) => {
        const { id } = req.params;
        const { description, amount } = req.body;
        Income.getById(id, (err, income) => {
            if (err || !income) return res.status(404).json({ message: "Ingreso no encontrado." });
            Income.update(id, description || income.description, amount || income.amount, (err) => {
                if (err) return res.status(500).json({ message: "Error al modificar el ingreso." });
                res.json({ message: "Ingreso modificado exitosamente." });
            });
        });
    },
    deleteIncome: (req, res) => {
        const { id } = req.params;
        Income.getById(id, (err, income) => {
            if (err || !income) return res.status(404).json({ message: "Ingreso no encontrado." });
            Income.delete(id, (err) => {
                if (err) return res.status(500).json({ message: "Error al eliminar el ingreso." });
                res.json({ message: "Ingreso eliminado exitosamente." });
            });
        });
    },
    visualizeIncomes: (req, res) => {
        Income.getAll((err, incomes) => {
            if (err) return res.status(500).json({ message: "Error al obtener los ingresos." });
            if (!incomes.length) return res.status(404).json({ message: "No hay ingresos disponibles." });
            res.json(incomes);
        });
    }
};

const financialAnalysisController = {
    getFinancialAnalysis: (req, res) => {
        FinancialAnalysis.getMovements((err, movements) => {
            if (err) return res.status(500).json({ message: "Error al obtener el análisis financiero." });
            res.json(movements);
        });
    },
    deleteFinancialAnalysis: (req, res) => {
        FinancialAnalysis.deleteFinancialAnalysis((err) => {
            if (err) return res.status(500).json({ message: "Error al eliminar el análisis financiero." });
            res.json({ message: "Análisis financiero eliminado exitosamente." });
        });
    }
};

const apiUrlIncomes = 'http://localhost:3000/api/ingresos';
function registrarIngreso() {
    const descripcion = document.getElementById('descripcion-ingreso').value;
    const monto = document.getElementById('monto-ingreso').value;
    fetch(apiUrlIncomes, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ description: descripcion, amount: parseFloat(monto) }) })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-registro-ingreso').innerText = data.message)
    .catch(error => document.getElementById('mensaje-registro-ingreso').innerText = "Error al registrar: " + error.message);
}

module.exports = { incomeController, financialAnalysisController };
