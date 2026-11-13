
import { body, validationResult } from 'express-validator';

// Product validation rules
export const validateProduct = [
    body('name')
        .notEmpty()
        .withMessage('Product name is required')
        .isString()
        .withMessage('Product name must be a string')
        .isLength({ min: 3 })
        .withMessage('Product name must contain at least 3 characters'),

    body('price')
        .notEmpty()
        .withMessage('Price is required')
        .isFloat({ gt: 0 })
        .withMessage('Price must be a positive number'),

    body('stock')
        .optional()
        .isInt({ min: 0 })
        .withMessage('Stock must be a non-negative integer'),

    body('category_id')
        .optional({ values: 'null' })
        .isInt({ gt: 0 })
        .withMessage('Category ID must be a positive integer'),

    (req, res, next) => {
        const errors = validationResult(req);

        if (!errors.isEmpty()) {
            return res.status(400).json({
                success: false,
                errors: errors.array()
            });
        }

        next();
    }
];

// Category validation rules
export const validateCategory = [
    body('name')
        .notEmpty()
        .withMessage('Category name is required')
        .isString()
        .withMessage('Category name must be a string')
        .isLength({ min: 2 })
        .withMessage('Category name must contain at least 2 characters'),

    (req, res, next) => {
        const errors = validationResult(req);

        if (!errors.isEmpty()) {
            return res.status(400).json({
                success: false,
                errors: errors.array()
            });
        }

        next();
    }
];
