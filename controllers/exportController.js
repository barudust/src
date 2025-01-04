const ExportModel = require('../models/exportModel');

const exportController = {
    getFinancialData: (req, res) => {
        ExportModel.getAllFinancialData((err, data) => {
            if (err) return res.status(500).json({ message: "Error al obtener los datos financieros." });
            res.json(data);
        });
    },

    deleteExportedData: (req, res) => {
        const { type } = req.params; // 'all' o el tipo específico

        ExportModel.deleteExportedData(type, (err) => {
            if (err) return res.status(500).json({ message: "Error al eliminar los datos exportados." });
            res.json({ message: "Datos exportados eliminados exitosamente." });
        });
    }
};

module.exports = exportController;

const apiUrlExport = 'http://localhost:3000/api/exportar-datos';

function obtenerDatosFinancieros() {
    fetch(apiUrlExport)
        .then(response => response.json())
        .then(data => {
            const listaDatos = document.getElementById('lista-datos');
            listaDatos.innerHTML = ''; // Limpiar lista existente
            
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(dato => {
                    const li = document.createElement('li');
                    li.textContent = `${dato.date}: ${dato.type} - ${dato.description} por $${dato.amount}`;
                    listaDatos.appendChild(li);
                });
            } else {
                listaDatos.innerHTML = '<li>No hay datos financieros disponibles.</li>';
            }
        })
        .catch(error => {
            alert("Error al obtener los datos financieros: " + error.message);
        });
}

function eliminarDatosExportados(type) {
    fetch(`${apiUrlExport}/${type}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Mensaje de confirmación
        obtenerDatosFinancieros(); // Actualizar la lista después de eliminar si es necesario.
    })
    .catch(error => {
        alert("Error al eliminar los datos exportados: " + error.message);
    });
}
