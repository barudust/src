const Budget = require('../models/budgetModel');
const apiUrlBudgets = 'http://localhost:3000/api/presupuestos';
const apiUrlDebts = 'http://localhost:3000/api/deudas';

const budgetController = {
    registerBudget: (req, res) => {
        const { description, amount } = req.body;
        if (!description || !amount) return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
        Budget.create(description, amount, (err, budget) => {
            if (err) return res.status(500).json({ message: "Error al registrar el presupuesto." });
            res.status(201).json({ message: "Presupuesto registrado exitosamente.", budget });
        });
    },
    modifyBudget: (req, res) => {
        const { id } = req.params;
        const { description, amount } = req.body;
        Budget.getById(id, (err, budget) => {
            if (err || !budget) return res.status(404).json({ message: "Presupuesto no encontrado." });
            Budget.update(id, description || budget.description, amount || budget.amount, (err) => {
                if (err) return res.status(500).json({ message: "Error al modificar el presupuesto." });
                res.json({ message: "Presupuesto modificado exitosamente." });
            });
        });
    },
    deleteBudget: (req, res) => {
        const { id } = req.params;
        Budget.getById(id, (err, budget) => {
            if (err || !budget) return res.status(404).json({ message: "Presupuesto no encontrado." });
            Budget.delete(id, (err) => {
                if (err) return res.status(500).json({ message: "Error al eliminar el presupuesto." });
                res.json({ message: "Presupuesto eliminado exitosamente." });
            });
        });
    },
    visualizeBudgets: (req, res) => {
        Budget.getAll((err, budgets) => {
            if (err) return res.status(500).json({ message: "Error al obtener los presupuestos." });
            if (!budgets.length) return res.status(404).json({ message: "No hay presupuestos disponibles." });
            res.json(budgets);
        });
    }
};

function registrarPresupuesto() {
    const descripcion = document.getElementById('descripcion-presupuesto').value;
    const monto = document.getElementById('monto-presupuesto').value;
    fetch(apiUrlBudgets, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description: descripcion, amount: parseFloat(monto) }),
    })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-registro-presupuesto').innerText = data.message)
    .catch(error => document.getElementById('mensaje-registro-presupuesto').innerText = "Error al registrar: " + error.message);
}

function modificarPresupuesto() {
    const id = document.getElementById('id-modificar-presupuesto').value;
    const nuevaDescripcion = document.getElementById('nuevo-descripcion-presupuesto').value;
    const nuevoMonto = document.getElementById('nuevo-monto-presupuesto').value;
    fetch(`${apiUrlBudgets}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description: nuevaDescripcion, amount: parseFloat(nuevoMonto) }),
    })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-modificacion-presupuesto').innerText = data.message)
    .catch(error => document.getElementById('mensaje-modificacion-presupuesto').innerText = "Error al modificar: " + error.message);
}

function eliminarPresupuesto() {
    const id = document.getElementById('id-eliminar-presupuesto').value;
    fetch(`${apiUrlBudgets}/${id}`, { method: 'DELETE' })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-eliminacion-presupuesto').innerText = data.message)
    .catch(error => document.getElementById('mensaje-eliminacion-presupuesto').innerText = "Error al eliminar: " + error.message);
}

function visualizarPresupuestos() {
   fetch(apiUrlBudgets)
      .then(response => response.json())
      .then(data => {
          const lista = document.getElementById('lista-presupuestos');
          lista.innerHTML = '';
          if (Array.isArray(data)) {
              data.forEach(budget => {
                  const li = document.createElement('li');
                  li.textContent = `ID: ${budget.id}, Descripción: ${budget.description}, Monto: ${budget.amount}`;
                  lista.appendChild(li);
              });
          } else {
              lista.innerHTML = `<li>${data.message}</li>`;
          }
      })
      .catch(error => alert("Error al visualizar presupuestos: " + error.message));
}

module.exports = budgetController;
