const ReportModel = require('../models/reportModel');

const reportController = {
    generateReport: (req, res) => {
        ReportModel.generateReport((err, data) => {
            if (err) return res.status(500).json({ message: "Error al generar el informe financiero." });
            res.json(data);
        });
    },

    deleteReports: (req, res) => {
        const { type } = req.params; // 'all' o el tipo específico

        ReportModel.deleteReports(type, (err) => {
            if (err) return res.status(500).json({ message: "Error al eliminar los informes financieros." });
            res.json({ message: "Informes financieros eliminados exitosamente." });
        });
    }
};

module.exports = reportController;

const apiUrlReports = 'http://localhost:3000/api/informes-financieros';

function obtenerInformesFinancieros() {
    fetch(apiUrlReports)
        .then(response => response.json())
        .then(data => {
            const listaInformes = document.getElementById('lista-informes');
            listaInformes.innerHTML = ''; // Limpiar lista existente
            
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(informe => {
                    const li = document.createElement('li');
                    li.textContent = `${informe.date}: ${informe.type} - ${informe.description} por $${informe.amount}`;
                    listaInformes.appendChild(li);
                });
            } else {
                listaInformes.innerHTML = '<li>No hay informes disponibles.</li>';
            }
        })
        .catch(error => {
            alert("Error al obtener los informes financieros: " + error.message);
        });
}

function eliminarInformesFinancieros(type) {
    fetch(`${apiUrlReports}/${type}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Mensaje de confirmación
        obtenerInformesFinancieros(); // Actualizar la lista después de eliminar si es necesario.
    })
    .catch(error => {
        alert("Error al eliminar los informes financieros: " + error.message);
    });
}
