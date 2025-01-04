const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const Income = {
    create: (description, amount, callback) => {
        const sql = 'INSERT INTO incomes (description, amount) VALUES (?, ?)';
        db.run(sql, [description, amount], function(err) {
            callback(err, { id: this.lastID, description, amount });
        });
    },
    update: (id, description, amount, callback) => {
        const sql = 'UPDATE incomes SET description = ?, amount = ? WHERE id = ?';
        db.run(sql, [description, amount, id], function(err) {
            callback(err);
        });
    },
    delete: (id, callback) => {
        const sql = 'DELETE FROM incomes WHERE id = ?';
        db.run(sql, id, function(err) {
            callback(err);
        });
    },
    getAll: (callback) => {
        const sql = 'SELECT * FROM incomes';
        db.all(sql, [], callback);
    },
    getById: (id, callback) => {
        const sql = 'SELECT * FROM incomes WHERE id = ?';
        db.get(sql, [id], callback);
    }
};

module.exports = Income;
