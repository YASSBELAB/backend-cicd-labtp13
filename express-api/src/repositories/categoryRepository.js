
import pool from '../config/db.js';

export const categoryRepository = {
    // Get all categories
    getAll: async () => {
        const [rows] = await pool.execute(
            'SELECT * FROM categories'
        );

        return rows;
    },

    // Get category by ID
    getById: async (id) => {
        const [rows] = await pool.execute(
            'SELECT * FROM categories WHERE id = ?',
            [id]
        );

        return rows[0] || null;
    },

    // Create category
    create: async (category) => {
        const [result] = await pool.execute(
            'INSERT INTO categories (name) VALUES (?)',
            [category.name]
        );

        return {
            id: result.insertId,
            ...category
        };
    },

    // Update category
    update: async (id, category) => {
        const [result] = await pool.execute(
            'UPDATE categories SET name = ? WHERE id = ?',
            [category.name, id]
        );

        if (result.affectedRows === 0) {
            return null;
        }

        return await categoryRepository.getById(id);
    },

    // Delete category
    remove: async (id) => {
        const [result] = await pool.execute(
            'DELETE FROM categories WHERE id = ?',
            [id]
        );

        return result.affectedRows > 0;
    }
};
