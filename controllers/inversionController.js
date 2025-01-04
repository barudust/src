const apiUrl = 'http://localhost:3000/api/inversiones';

function registrarInversion() {
    const tipo = document.getElementById('tipo').value;
    const monto = document.getElementById('monto').value;

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: tipo, amount: parseFloat(monto) }), // Asegurarse de que el monto sea un número
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('mensaje-registro').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-registro').innerText = "Error al registrar: " + error.message;
    });
}

function modificarInversion() {
    const id = document.getElementById('id-modificar').value;
    const nuevoTipo = document.getElementById('nuevo-tipo').value;
    const nuevoMonto = document.getElementById('nuevo-monto').value;

    fetch(`${apiUrl}/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ type: nuevoTipo, amount: parseFloat(nuevoMonto) }), // Asegurarse de que el nuevo monto sea un número
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('mensaje-modificacion').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-modificacion').innerText = "Error al modificar: " + error.message;
    });
}

function eliminarInversion() {
    const id = document.getElementById('id-eliminar').value;

    fetch(`${apiUrl}/${id}`, {
        method: 'DELETE',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('mensaje-eliminacion').innerText = data.message;
    })
    .catch(error => {
        document.getElementById('mensaje-eliminacion').innerText = "Error al eliminar: " + error.message;
    });
}

function visualizarInversiones() {
    fetch(apiUrl)
      .then(response => {
          if (!response.ok) {
              throw new Error('Error en la respuesta del servidor');
          }
          return response.json();
      })
      .then(data => {
          const lista = document.getElementById('lista-inversiones');
          lista.innerHTML = ''; // Limpiar lista existente
          if (Array.isArray(data)) {
              data.forEach(investment => {
                  const li = document.createElement('li');
                  li.textContent = `ID: ${investment.id}, Tipo: ${investment.type}, Monto: ${investment.amount}`;
                  lista.appendChild(li);
              });
          } else {
              lista.innerHTML = `<li>${data.message}</li>`;
          }
      })
      .catch(error => {
          alert("Error al visualizar inversiones: " + error.message);
      });
}