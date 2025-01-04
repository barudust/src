const express = require('express');
const bodyParser = require('body-parser');
const investmentRoutes = require('./routes/investmentRoutes');
const debtRoutes = require('./routes/debtRoutes'); // Importar rutas de adeudos
const incomeRoutes = require('./routes/incomeRoutes'); // Importar rutas de ingresos
const budgetRoutes = require('./routes/budgetRoutes'); // Importar rutas de presupuestos
const financialAnalysisRoutes = require('./routes/financialAnalysisRoutes'); // Importar rutas de análisis financiero
const exportRoutes = require('./routes/exportRoutes'); // Importar rutas de exportación
const reportRoutes = require('./routes/reportRoutes'); // Importar rutas de informes
const userRoutes = require('./routes/userRoutes'); // Importar rutas de usuarios

const app = express();
app.use(bodyParser.json());
app.use('/api', investmentRoutes); // Prefijo para las rutas de inversiones
app.use('/api', debtRoutes); // Prefijo para las rutas de adeudos
app.use('/api', incomeRoutes); // Prefijo para las rutas de ingresos
app.use('/api', budgetRoutes); // Prefijo para las rutas de presupuestos
app.use('/api', financialAnalysisRoutes); // Prefijo para las rutas de análisis financiero
app.use('/api', exportRoutes); // Prefijo para las rutas de exportación
app.use('/api', reportRoutes); // Prefijo para las rutas de informes
app.use('/api', userRoutes); // Prefijo para las rutas de usuarios

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});


