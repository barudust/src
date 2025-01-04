const Debt = require('../models/debtModel');

const debtController = {
    // Registrar un adeudo
    registerDebt: (req, res) => {
        const { description, amount } = req.body;
        if (!description || !amount) {
            return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
        }
        
        Debt.create(description, amount, (err, debt) => {
            if (err) return res.status(500).json({ message: "Error al registrar el adeudo." });
            res.status(201).json({ message: "Adeudo registrado exitosamente.", debt });
        });
    },

    // Modificar un adeudo
    modifyDebt: (req, res) => {
        const { id } = req.params;
        const { description, amount } = req.body;

        Debt.getById(id, (err, debt) => {
            if (err || !debt) {
                return res.status(404).json({ message: "Adeudo no encontrado." });
            }

            Debt.update(id, description || debt.description, amount || debt.amount, (err) => {
                if (err) return res.status(500).json({ message: "Error al modificar el adeudo." });
                res.json({ message: "Adeudo modificado exitosamente." });
            });
        });
    },

    // Eliminar un adeudo
    deleteDebt: (req, res) => {
        const { id } = req.params;

        Debt.getById(id, (err, debt) => {
            if (err || !debt) {
                return res.status(404).json({ message: "Adeudo no encontrado." });
            }

            Debt.delete(id, (err) => {
                if (err) return res.status(500).json({ message: "Error al eliminar el adeudo." });
                res.json({ message: "Adeudo eliminado exitosamente." });
            });
        });
    },

    // Visualizar todos los adeudos
    visualizeDebts: (req, res) => {
        Debt.getAll((err, debts) => {
            if (err) return res.status(500).json({ message: "Error al obtener los adeudos." });
            if (!debts.length) return res.status(404).json({ message: "No hay adeudos disponibles." });

            res.json(debts);
        });
    },

    // Registrar una deuda
    registerLoan: (req, res) => {
        const { description, amount } = req.body;
        if (!description || !amount) {
            return res.status(400).json({ message: "La descripción y el monto son obligatorios." });
        }
        
        Debt.create(description, amount, (err, loan) => { // Asumiendo que el modelo puede manejar ambos
            if (err) return res.status(500).json({ message: "Error al registrar la deuda." });
            res.status(201).json({ message: "Deuda registrada exitosamente.", loan });
        });
    },

    // Visualizar todas las deudas
    visualizeLoans: (req, res) => {
        Debt.getAll((err, loans) => { // Asumiendo que hay un método para obtener deudas
            if (err) return res.status(500).json({ message: "Error al obtener las deudas." });
            if (!loans.length) return res.status(404).json({ message: "No hay deudas disponibles." });

            res.json(loans);
        });
    }
};

module.exports = debtController;

// Código del cliente para manejar la interfaz de adeudos
const apiUrlDebts = 'http://localhost:3000/api/adeudos';

function registrarAdeudo() {
    const descripcion = document.getElementById('descripcion').value;
    const monto = document.getElementById('monto-adeudo').value;

    fetch(apiUrlDebts, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: descripcion, amount: parseFloat(monto) }), // Asegurarse de que el monto sea un número
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('mensaje-registro-adeudo').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-registro-adeudo').innerText = "Error al registrar: " + error.message;
    });
}

function modificarAdeudo() {
    const id = document.getElementById('id-modificar-adeudo').value;
    const nuevaDescripcion = document.getElementById('nuevo-descripcion').value;
    const nuevoMonto = document.getElementById('nuevo-monto-adeudo').value;

    fetch(`${apiUrlDebts}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: nuevaDescripcion, amount: parseFloat(nuevoMonto) }), // Asegurarse de que el nuevo monto sea un número
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('mensaje-modificacion-adeudo').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-modificacion-adeudo').innerText = "Error al modificar: " + error.message;
    });
}

function eliminarAdeudo() {
    const id = document.getElementById('id-eliminar-adeudo').value;

    fetch(`${apiUrlDebts}/${id}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('mensaje-eliminacion-adeudo').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-eliminacion-adeudo').innerText = "Error al eliminar: " + error.message;
    });
}

function visualizarAdeudos() {
   fetch(apiUrlDebts)
      .then(response => response.json())
      .then(data => {
          const lista = document.getElementById('lista-adeudos');
          lista.innerHTML = ''; // Limpiar lista existente
          if (Array.isArray(data)) {
              data.forEach(debt => {
                  const li = document.createElement('li');
                  li.textContent = `ID: ${debt.id}, Descripción: ${debt.description}, Monto: ${debt.amount}`;
                  lista.appendChild(li);
              });
          } else {
              lista.innerHTML = `<li>${data.message}</li>`;
          }
      })
      .catch(error => {
          alert("Error al visualizar adeudos: " + error.message);
      });
}

// Código del cliente para manejar la interfaz de deudas
const apiUrlLoans = 'http://localhost:3000/api/deudas';

function registrarDeuda() {
    const descripcion = document.getElementById('descripcion-deuda').value;
    const monto = document.getElementById('monto-deuda').value;

    fetch(apiUrlLoans, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: descripcion, amount: parseFloat(monto) }), // Asegurarse de que el monto sea un número
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('mensaje-registro-deuda').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-registro-deuda').innerText = "Error al registrar la deuda: " + error.message;
    });
}

function modificarDeuda() {
    const id = document.getElementById('id-modificar-deuda').value;
    const nuevaDescripcion = document.getElementById('nuevo-descripcion-deuda').value;
    const nuevoMonto = document.getElementById('nuevo-monto-deuda').value;

    fetch(`${apiUrlLoans}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ description: nuevaDescripcion, amount: parseFloat(nuevoMonto) }), // Asegurarse de que el nuevo monto sea un número
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('mensaje-modificacion-deuda').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-modificacion-deuda').innerText = "Error al modificar la deuda: " + error.message;
    });
}

function eliminarDeuda() {
   const id = document.getElementById('id-eliminar-deuda').value;

   fetch(`${apiUrlLoans}/${id}`, { method: 'DELETE' })
       .then(response => response.json())
       .then(data => {
           document.getElementById('mensaje-eliminacion-deuda').innerText = data.message;
       })
       .catch(error => {
           document.getElementById('mensaje-eliminacion-deuda').innerText = "Error al eliminar la deuda: " + error.message;
       });
}

function visualizarDeudas() { 
   fetch(apiUrlLoans)
       .then(response => response.json())
       .then(data => { 
           const lista = document.getElementById('lista-deudas'); 
           lista.innerHTML = ''; // Limpiar lista existente 
           if (Array.isArray(data)) { 
               data.forEach(debt => { 
                   const li = document.createElement('li'); 
                   li.textContent = `ID: ${debt.id}, Descripción: ${debt.description}, Monto: ${debt.amount}`; 
                   lista.appendChild(li); 
               }); 
           } else { 
               lista.innerHTML = `<li>${data.message}</li>`; 
           } 
       }) 
       .catch(error => { 
           alert("Error al visualizar deudas: " + error.message); 
       }); 
}
