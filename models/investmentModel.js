const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const Investment = {
    create: (type, amount, callback) => {
        const sql = 'INSERT INTO investments (type, amount) VALUES (?, ?)';
        db.run(sql, [type, amount], function(err) {
            callback(err, { id: this.lastID, type, amount });
        });
    },
    update: (id, type, amount, callback) => {
        const sql = 'UPDATE investments SET type = ?, amount = ? WHERE id = ?';
        db.run(sql, [type, amount, id], function(err) {
            callback(err);
        });
    },
    delete: (id, callback) => {
        const sql = 'DELETE FROM investments WHERE id = ?';
        db.run(sql, id, function(err) {
            callback(err);
        });
    },
    getAll: (callback) => {
        const sql = 'SELECT * FROM investments';
        db.all(sql, [], callback);
    },
    getById: (id, callback) => {
        const sql = 'SELECT * FROM investments WHERE id = ?';
        db.get(sql, [id], callback);
    }
};

module.exports = Investment;
