const FinancialAnalysis = {
    // Método existente para obtener movimientos
    getMovements: (callback) => {
        const sql = `
            SELECT 'Ingreso' AS type, description, amount, date FROM incomes WHERE date >= date('now', '-6 months')
            UNION ALL
            SELECT 'Inversión' AS type, type AS description, amount, date FROM investments WHERE date >= date('now', '-6 months')
            UNION ALL
            SELECT 'Adeudo' AS type, description, amount, date FROM debts WHERE date >= date('now', '-6 months')
            UNION ALL
            SELECT 'Presupuesto' AS type, description, amount, date FROM budgets WHERE date >= date('now', '-6 months')
            ORDER BY date DESC;
        `;
        db.all(sql, [], callback);
    },

    // Método para eliminar todos los registros de análisis financiero
    deleteFinancialAnalysis: (callback) => {
        const sql = `
            DELETE FROM incomes WHERE date < date('now', '-6 months');
            DELETE FROM investments WHERE date < date('now', '-6 months');
            DELETE FROM debts WHERE date < date('now', '-6 months');
            DELETE FROM budgets WHERE date < date('now', '-6 months');
        `;
        db.exec(sql, callback);
    }
};

module.exports = FinancialAnalysis;
