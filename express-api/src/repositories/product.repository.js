
import pool from '../config/db.js';

export const productRepository = {

    // Get all products, with optional name search
    findAll: async (search = '') => {
        const [rows] = await pool.execute(
            'SELECT * FROM products WHERE name LIKE ?',
            [`%${search}%`]
        );

        return rows;
    },

    // Get product by ID
    findById: async (id) => {
        const [rows] = await pool.execute(
            'SELECT * FROM products WHERE id = ?',
            [id]
        );

        return rows[0] || null;
    },

    // Create product
    create: async (productData) => {
        const {
            name,
            price,
            category_id,
            stock = 0
        } = productData;

        const [result] = await pool.execute(
            `INSERT INTO products (name, price, category_id, stock)
             VALUES (?, ?, ?, ?)`,
            [name, price, category_id ?? null, stock]
        );

        return await productRepository.findById(result.insertId);
    },

    // Update product
    update: async (id, productData) => {
        const {
            name,
            price,
            category_id,
            stock
        } = productData;

        const [result] = await pool.execute(
            `UPDATE products
             SET name = ?, price = ?, category_id = ?, stock = ?
             WHERE id = ?`,
            [name, price, category_id ?? null, stock ?? 0, id]
        );

        if (result.affectedRows === 0) {
            return null;
        }

        return await productRepository.findById(id);
    },

    // Update only stock quantity
    updateStock: async (id, stock) => {
        const [result] = await pool.execute(
            'UPDATE products SET stock = ? WHERE id = ?',
            [stock, id]
        );

        if (result.affectedRows === 0) {
            return null;
        }

        return await productRepository.findById(id);
    },

    // Delete product
    delete: async (id) => {
        const [result] = await pool.execute(
            'DELETE FROM products WHERE id = ?',
            [id]
        );

        return result.affectedRows > 0;
    },

    // Advanced search, filtering, sorting and pagination
    search: async ({
        name,
        minPrice,
        maxPrice,
        categoryId,
        limit = 10,
        offset = 0,
        sort = 'name'
    }) => {
        let sql = 'SELECT * FROM products WHERE 1=1';
        const params = [];

        // Filter by name
        if (name) {
            sql += ' AND name LIKE ?';
            params.push(`%${name}%`);
        }

        // Filter by minimum price
        if (minPrice !== undefined && minPrice !== '') {
            sql += ' AND price >= ?';
            params.push(minPrice);
        }

        // Filter by maximum price
        if (maxPrice !== undefined && maxPrice !== '') {
            sql += ' AND price <= ?';
            params.push(maxPrice);
        }

        // Filter by category
        if (categoryId !== undefined && categoryId !== '') {
            sql += ' AND category_id = ?';
            params.push(categoryId);
        }

        // Allow only specific sorting columns
        const allowedSort = ['name', 'price', 'id'];
        const sortColumn = allowedSort.includes(sort)
            ? sort
            : 'name';

        sql += ` ORDER BY ${sortColumn} LIMIT ? OFFSET ?`;

        params.push(
            Math.max(1, parseInt(limit, 10) || 10),
            Math.max(0, parseInt(offset, 10) || 0)
        );

        const [rows] = await pool.execute(sql, params);

        return rows;
    }
};
