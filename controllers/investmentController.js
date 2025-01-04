const Investment = require('../models/investmentModel');

const investmentController = {
    registerInvestment: (req, res) => {
        const { type, amount } = req.body;
        if (!type || !amount) {
            return res.status(400).json({ message: "El tipo y monto son obligatorios." });
        }
        
        Investment.create(type, amount, (err, investment) => {
            if (err) return res.status(500).json({ message: "Error al registrar la inversión." });
            res.status(201).json({ message: "Inversión registrada exitosamente.", investment });
        });
    },

    modifyInvestment: (req, res) => {
        const { id } = req.params;
        const { type, amount } = req.body;

        Investment.getById(id, (err, investment) => {
            if (err || !investment) {
                return res.status(404).json({ message: "Inversión no encontrada." });
            }

            Investment.update(id, type || investment.type, amount || investment.amount, (err) => {
                if (err) return res.status(500).json({ message: "Error al modificar la inversión." });
                res.json({ message: "Inversión modificada exitosamente." });
            });
        });
    },

    deleteInvestment: (req, res) => {
        const { id } = req.params;

        Investment.getById(id, (err, investment) => {
            if (err || !investment) {
                return res.status(404).json({ message: "Inversión no encontrada." });
            }

            Investment.delete(id, (err) => {
                if (err) return res.status(500).json({ message: "Error al eliminar la inversión." });
                res.json({ message: "Inversión eliminada exitosamente." });
            });
        });
    },

    visualizeInvestments: (req, res) => {
        Investment.getAll((err, investments) => {
            if (err) return res.status(500).json({ message: "Error al obtener las inversiones." });
            if (!investments.length) return res.status(404).json({ message: "No hay inversiones disponibles." });

            res.json(investments);
        });
    }
};

module.exports = investmentController;
