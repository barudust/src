const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const Debt = {
    create: (description, amount, callback) => {
        const sql = 'INSERT INTO debts (description, amount) VALUES (?, ?)';
        db.run(sql, [description, amount], function(err) {
            callback(err, { id: this.lastID, description, amount });
        });
    },
    update: (id, description, amount, callback) => {
        const sql = 'UPDATE debts SET description = ?, amount = ? WHERE id = ?';
        db.run(sql, [description, amount, id], function(err) {
            callback(err);
        });
    },
    delete: (id, callback) => {
        const sql = 'DELETE FROM debts WHERE id = ?';
        db.run(sql, id, function(err) {
            callback(err);
        });
    },
    getAll: (callback) => {
        const sql = 'SELECT * FROM debts';
        db.all(sql, [], callback);
    },
    getById: (id, callback) => {
        const sql = 'SELECT * FROM debts WHERE id = ?';
        db.get(sql, [id], callback);
    }
};

module.exports = Debt;
