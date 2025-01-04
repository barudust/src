const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const ReportModel = {
    generateReport: (callback) => {
        const sql = `
            SELECT 'Ingreso' AS type, description, amount, date FROM incomes
            UNION ALL
            SELECT 'Inversión' AS type, type AS description, amount, date FROM investments
            UNION ALL
            SELECT 'Adeudo' AS type, description, amount, date FROM debts
            UNION ALL
            SELECT 'Presupuesto' AS type, description, amount, date FROM budgets;
        `;
        db.all(sql, [], callback);
    },

    deleteReports: (type, callback) => {
        let sql;
        if (type === 'all') {
            sql = `
                DELETE FROM incomes;
                DELETE FROM investments;
                DELETE FROM debts;
                DELETE FROM budgets;
            `;
        } else {
            sql = `DELETE FROM ${type};`;
        }
        db.exec(sql, callback);
    }
};

module.exports = ReportModel;
