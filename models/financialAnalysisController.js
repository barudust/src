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

const apiUrlAnalysis = 'analisis_financiero.php?action=getFinancialData';

const financialAnalysisController = {
    /**
     * Obtiene los datos financieros del backend.
     */
    obtenerAnalisisFinanciero: () => {
        fetch(apiUrlAnalysis)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                financialAnalysisController.crearGraficas(data);
            })
            .catch(error => console.error('Error al obtener datos financieros:', error));
    },

    /**
     * Genera las gráficas de pastel y barras con los datos financieros.
     * @param {Object} data - Datos financieros (deudas, adeudos, inversiones, ingresos).
     */
    crearGraficas: (data) => {
        const labels = ['Deudas', 'Adeudos', 'Inversiones', 'Ingresos'];
        const values = [data.deudas, data.adeudos, data.inversiones, data.ingresos];

        // Mostrar el contenedor de gráficas
        document.getElementById('graficas').style.display = 'block';

        // Gráfica de pastel
        const ctxPie = document.getElementById('graficoPastel').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            }
        });

        // Gráfica de barras
        const ctxBar = document.getElementById('graficoBarras').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad',
                    data: values,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
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
