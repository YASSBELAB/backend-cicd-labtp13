
import pool from '../config/db.js';
import { productRepository } from '../repositories/product.repository.js';
import { categoryRepository } from '../repositories/categoryRepository.js';

export const productService = {

    // Get all products with search and pagination
    getAllProducts: async (search = '', page = 1, limit = 10) => {
        const products = await productRepository.findAll(search);

        const total = products.length;
        const startIndex = (page - 1) * limit;

        return {
            products: products.slice(startIndex, startIndex + limit),
            total,
            page,
            limit,
            totalPages: Math.ceil(total / limit)
        };
    },

    // Get product by ID
    getProductById: async (id) => {
        const product = await productRepository.findById(id);

        if (!product) {
            throw new Error('Product not found');
        }

        return product;
    },

    // Create product
    createProduct: async (productData) => {
        if (!productData.name || productData.name.length < 3) {
            throw new Error('Product name must contain at least 3 characters');
        }

        if (!Number.isFinite(Number(productData.price)) ||
            Number(productData.price) <= 0) {
            throw new Error('Price must be positive');
        }

        if (productData.stock !== undefined &&
            (!Number.isInteger(Number(productData.stock)) ||
             Number(productData.stock) < 0)) {
            throw new Error('Stock must be a non-negative integer');
        }

        if (productData.category_id !== undefined &&
            productData.category_id !== null &&
            productData.category_id !== '') {

            const category = await categoryRepository.getById(
                productData.category_id
            );

            if (!category) {
                throw new Error('Invalid category_id');
            }
        }

        return await productRepository.create(productData);
    },

    // Create product and category in one transaction
    createProductWithCategory: async (productData, categoryName) => {

        // Validate product before starting the transaction
        if (!productData.name || productData.name.length < 3) {
            throw new Error('Product name must contain at least 3 characters');
        }

        if (!Number.isFinite(Number(productData.price)) ||
            Number(productData.price) <= 0) {
            throw new Error('Price must be positive');
        }

        if (productData.stock !== undefined &&
            (!Number.isInteger(Number(productData.stock)) ||
             Number(productData.stock) < 0)) {
            throw new Error('Stock must be a non-negative integer');
        }

        const connection = await pool.getConnection();

        try {
            await connection.beginTransaction();

            let categoryId = null;

            // Find or create category
            if (categoryName && categoryName.trim()) {

                const [catRows] = await connection.execute(
                    'SELECT id FROM categories WHERE name = ?',
                    [categoryName.trim()]
                );

                if (catRows.length > 0) {
                    categoryId = catRows[0].id;
                } else {
                    const [catResult] = await connection.execute(
                        'INSERT INTO categories (name) VALUES (?)',
                        [categoryName.trim()]
                    );

                    categoryId = catResult.insertId;
                }
            }

            // Insert product
            const [result] = await connection.execute(
                `INSERT INTO products (name, price, stock, category_id)
                 VALUES (?, ?, ?, ?)`,
                [
                    productData.name,
                    Number(productData.price),
                    productData.stock ?? 0,
                    categoryId
                ]
            );

            // Commit transaction
            await connection.commit();

            return {
                id: result.insertId,
                ...productData,
                price: Number(productData.price),
                stock: productData.stock ?? 0,
                category_id: categoryId
            };

        } catch (error) {
            await connection.rollback();
            throw error;

        } finally {
            connection.release();
        }
    },

    // Update product
    updateProduct: async (id, productData) => {
        await productService.getProductById(id);

        if (!productData.name || productData.name.length < 3) {
            throw new Error('Product name must contain at least 3 characters');
        }

        if (!Number.isFinite(Number(productData.price)) ||
            Number(productData.price) <= 0) {
            throw new Error('Price must be positive');
        }

        if (productData.stock !== undefined &&
            (!Number.isInteger(Number(productData.stock)) ||
             Number(productData.stock) < 0)) {
            throw new Error('Stock must be a non-negative integer');
        }

        if (productData.category_id !== undefined &&
            productData.category_id !== null &&
            productData.category_id !== '') {

            const category = await categoryRepository.getById(
                productData.category_id
            );

            if (!category) {
                throw new Error('Invalid category_id');
            }
        }

        return await productRepository.update(id, productData);
    },

    // Update stock
    updateStock: async (id, stock) => {
        if (!Number.isInteger(Number(stock)) ||
            Number(stock) < 0 ||
            stock === undefined ||
            stock === null ||
            stock === '') {
            throw new Error('Stock must be a non-negative integer');
        }

        await productService.getProductById(id);

        return await productRepository.updateStock(id, Number(stock));
    },

    // Delete product
    deleteProduct: async (id) => {
        await productService.getProductById(id);

        return await productRepository.delete(id);
    },

    // Advanced search with filters, sorting and pagination
    searchProducts: async (filters) => {
        const {
            minPrice,
            maxPrice,
            categoryId,
            limit = 10,
            offset = 0
        } = filters;

        if (minPrice !== undefined && minPrice !== '') {
            if (!Number.isFinite(Number(minPrice)) ||
                Number(minPrice) < 0) {
                throw new Error('Invalid minimum price');
            }
        }

        if (maxPrice !== undefined && maxPrice !== '') {
            if (!Number.isFinite(Number(maxPrice)) ||
                Number(maxPrice) < 0) {
                throw new Error('Invalid maximum price');
            }
        }

        if (minPrice !== undefined && maxPrice !== undefined &&
            minPrice !== '' && maxPrice !== '' &&
            Number(minPrice) > Number(maxPrice)) {
            throw new Error('Minimum price cannot exceed maximum price');
        }

        if (categoryId !== undefined && categoryId !== '') {
            if (!Number.isInteger(Number(categoryId)) ||
                Number(categoryId) <= 0) {
                throw new Error('Invalid categoryId');
            }

            const category = await categoryRepository.getById(
                Number(categoryId)
            );

            if (!category) {
                throw new Error('Invalid categoryId');
            }
        }

        if (!Number.isInteger(Number(limit)) ||
            Number(limit) <= 0 ||
            !Number.isInteger(Number(offset)) ||
            Number(offset) < 0) {
            throw new Error('Invalid pagination parameters');
        }

        return await productRepository.search(filters);
    }

};
