const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./data/database.sqlite');

const UserModel = {
    createUser: (nombre, apellidos, telefono, correo, callback) => {
        const sql = 'INSERT INTO users (nombre, apellidos, telefono, correo) VALUES (?, ?, ?, ?)';
        db.run(sql, [nombre, apellidos, telefono, correo], function(err) {
            callback(err, { id: this.lastID, nombre, apellidos, telefono, correo });
        });
    },
    updateUser: (id, nombre, apellidos, telefono, correo, callback) => {
        const sql = 'UPDATE users SET nombre = ?, apellidos = ?, telefono = ?, correo = ? WHERE id = ?';
        db.run(sql, [nombre, apellidos, telefono, correo, id], function(err) {
            callback(err);
        });
    },
    deleteUser: (id, callback) => {
        const sql = 'DELETE FROM users WHERE id = ?';
        db.run(sql, id, function(err) {
            callback(err);
        });
    },
    getUserById: (id, callback) => {
        const sql = 'SELECT * FROM users WHERE id = ?';
        db.get(sql, [id], callback);
    },
    getAllUsers: (callback) => {
        const sql = 'SELECT * FROM users';
        db.all(sql, [], callback);
    }
};

module.exports = UserModel;

