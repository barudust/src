const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const Budget = {
    create: (description, amount, callback) => {
        if (amount > 1000) return callback(new Error("El monto no puede exceder $1000.00."));
        const sql = 'INSERT INTO budgets (description, amount) VALUES (?, ?)';
        db.run(sql, [description, amount], function(err) { callback(err, { id: this.lastID, description, amount }); });
    },
    update: (id, description, amount, callback) => {
        if (amount > 1000) return callback(new Error("El monto no puede exceder $1000.00."));
        const sql = 'UPDATE budgets SET description = ?, amount = ? WHERE id = ?';
        db.run(sql, [description, amount, id], function(err) { callback(err); });
    },
    delete: (id, callback) => {
        const sql = 'DELETE FROM budgets WHERE id = ?';
        db.run(sql, id, function(err) { callback(err); });
    },
    getAll: (callback) => {
        const sql = 'SELECT * FROM budgets';
        db.all(sql, [], callback);
    },
    getById: (id, callback) => {
        const sql = 'SELECT * FROM budgets WHERE id = ?';
        db.get(sql, [id], callback);
    }
};

const Debt = require('../models/debtModel');
const debtController = {
    registerDebt: (req, res) => {
        const { description, amount } = req.body;
        if (!description || !amount) return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
        Debt.create(description, amount, (err, debt) => {
            if (err) return res.status(500).json({ message: "Error al registrar el adeudo." });
            res.status(201).json({ message: "Adeudo registrado exitosamente.", debt });
        });
    },
    modifyDebt: (req, res) => {
        const { id } = req.params;
        const { description, amount } = req.body;
        Debt.getById(id, (err, debt) => {
            if (err || !debt) return res.status(404).json({ message: "Adeudo no encontrado." });
            Debt.update(id, description || debt.description, amount || debt.amount, (err) => {
                if (err) return res.status(500).json({ message: "Error al modificar el adeudo." });
                res.json({ message: "Adeudo modificado exitosamente." });
            });
        });
    },
    deleteDebt: (req, res) => {
        const { id } = req.params;
        Debt.getById(id, (err, debt) => {
            if (err || !debt) return res.status(404).json({ message: "Adeudo no encontrado." });
            Debt.delete(id, (err) => {
                if (err) return res.status(500).json({ message: "Error al eliminar el adeudo." });
                res.json({ message: "Adeudo eliminado exitosamente." });
            });
        });
    },
    visualizeDebts: (req, res) => {
        Debt.getAll((err, debts) => {
            if (err) return res.status(500).json({ message: "Error al obtener los adeudos." });
            if (!debts.length) return res.status(404).json({ message: "No hay adeudos disponibles." });
            res.json(debts);
        });
    }
};

module.exports = { Budget, debtController };
