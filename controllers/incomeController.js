const Income = require('../models/incomeModel');

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

const apiUrlIncomes = 'http://localhost:3000/api/ingresos';

function registrarIngreso() {
    const descripcion = document.getElementById('descripcion-ingreso').value;
    const monto = document.getElementById('monto-ingreso').value;
    fetch(apiUrlIncomes, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description: descripcion, amount: parseFloat(monto) }),
    })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-registro-ingreso').innerText = data.message)
    .catch(error => document.getElementById('mensaje-registro-ingreso').innerText = "Error al registrar: " + error.message);
}

function modificarIngreso() {
    const id = document.getElementById('id-modificar-ingreso').value;
    const nuevaDescripcion = document.getElementById('nuevo-descripcion-ingreso').value;
    const nuevoMonto = document.getElementById('nuevo-monto-ingreso').value;
    fetch(`${apiUrlIncomes}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description: nuevaDescripcion, amount: parseFloat(nuevoMonto) }),
    })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-modificacion-ingreso').innerText = data.message)
    .catch(error => document.getElementById('mensaje-modificacion-ingreso').innerText = "Error al modificar: " + error.message);
}

function eliminarIngreso() {
    const id = document.getElementById('id-eliminar-ingreso').value;
    fetch(`${apiUrlIncomes}/${id}`, { method: 'DELETE' })
    .then(response => response.json())
    .then(data => document.getElementById('mensaje-eliminacion-ingreso').innerText = data.message)
    .catch(error => document.getElementById('mensaje-eliminacion-ingreso').innerText = "Error al eliminar: " + error.message);
}

function visualizarIngresos() {
   fetch(apiUrlIncomes)
      .then(response => response.json())
      .then(data => {
          const lista = document.getElementById('lista-ingresos');
          lista.innerHTML = '';
          if (Array.isArray(data)) {
              data.forEach(income => {
                  const li = document.createElement('li');
                  li.textContent = `ID: ${income.id}, Descripción: ${income.description}, Monto: ${income.amount}`;
                  lista.appendChild(li);
              });
          } else {
              lista.innerHTML = `<li>${data.message}</li>`;
          }
      })
      .catch(error => alert("Error al visualizar ingresos: " + error.message));
}

module.exports = incomeController;
