
import { productService } from '../services/product.service.js';

export const productController = {

    // GET: Retrieve all products with search and pagination
    getAll: async (req, res, next) => {
        try {
            const search = req.query.search || '';
            const page = Math.max(1, parseInt(req.query.page, 10) || 1);
            const limit = Math.max(1, parseInt(req.query.limit, 10) || 10);

            const result = await productService.getAllProducts(
                search,
                page,
                limit
            );

            res.status(200).json({
                success: true,
                ...result
            });
        } catch (error) {
            next(error);
        }
    },

    // GET: Retrieve product by ID
    getById: async (req, res, next) => {
        try {
            const id = parseInt(req.params.id, 10);
            const product = await productService.getProductById(id);

            res.status(200).json({
                success: true,
                data: product
            });
        } catch (error) {
            next(error);
        }
    },

    // GET: Advanced product search with filters
    search: async (req, res, next) => {
        try {
            const products = await productService.searchProducts(req.query);

            res.status(200).json({
                success: true,
                count: products.length,
                data: products
            });
        } catch (error) {
            next(error);
        }
    },

    // POST: Create a new product
    create: async (req, res, next) => {
        try {
            const { name, price, stock, category_id } = req.body;

            const newProduct = await productService.createProduct({
                name,
                price,
                stock,
                category_id
            });

            res.status(201).json({
                success: true,
                message: 'Product created successfully',
                data: newProduct
            });
        } catch (error) {
            next(error);
        }
    },

    // POST: Create product and category in one transaction
    createWithCategory: async (req, res, next) => {
        try {
            const { name, price, stock, categoryName } = req.body;

            const newProduct = await productService.createProductWithCategory(
                {
                    name,
                    price,
                    stock
                },
                categoryName
            );

            res.status(201).json({
                success: true,
                message: 'Product and category created successfully',
                data: newProduct
            });
        } catch (error) {
            next(error);
        }
    },

    // PUT: Complete product update
    update: async (req, res, next) => {
        try {
            const id = parseInt(req.params.id, 10);
            const { name, price, stock, category_id } = req.body;

            const updatedProduct = await productService.updateProduct(id, {
                name,
                price,
                stock,
                category_id
            });

            res.status(200).json({
                success: true,
                message: 'Product updated successfully',
                data: updatedProduct
            });
        } catch (error) {
            next(error);
        }
    },

    // PATCH: Partial product update
    patch: async (req, res, next) => {
        try {
            const id = parseInt(req.params.id, 10);

            const existing = await productService.getProductById(id);
            const updatedData = { ...existing, ...req.body };

            const updatedProduct = await productService.updateProduct(
                id,
                updatedData
            );

            res.status(200).json({
                success: true,
                message: 'Product partially updated successfully',
                data: updatedProduct
            });
        } catch (error) {
            next(error);
        }
    },

    // PATCH: Update only stock quantity
    updateStock: async (req, res, next) => {
        try {
            const id = parseInt(req.params.id, 10);
            const stock = req.body.stock;

            const updatedProduct = await productService.updateStock(id, stock);

            res.status(200).json({
                success: true,
                message: 'Stock updated successfully',
                data: updatedProduct
            });
        } catch (error) {
            next(error);
        }
    },

    // DELETE: Remove a product
    remove: async (req, res, next) => {
        try {
            const id = parseInt(req.params.id, 10);

            await productService.deleteProduct(id);

            res.status(204).send();
        } catch (error) {
            next(error);
        }
    }
};
