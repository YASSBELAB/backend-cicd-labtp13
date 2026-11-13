
import { Router } from 'express';

import { productController } from '../controllers/product.controller.js';
import { validateProduct } from '../middlewares/validation.js';
import { authorize } from '../middlewares/auth.js';

const router = Router();

// GET: All authenticated users
router.get('/search', productController.search);
router.get('/', productController.getAll);
router.get('/:id', productController.getById);

// POST: Admin only
router.post(
    '/with-category',
    authorize('ADMIN'),
    productController.createWithCategory
);

router.post(
    '/',
    authorize('ADMIN'),
    validateProduct,
    productController.create
);

// PUT: Admin only
router.put(
    '/:id',
    authorize('ADMIN'),
    validateProduct,
    productController.update
);

// PATCH: Admin only
router.patch(
    '/:id/stock',
    authorize('ADMIN'),
    productController.updateStock
);

router.patch(
    '/:id',
    authorize('ADMIN'),
    productController.patch
);

// DELETE: Admin only
router.delete(
    '/:id',
    authorize('ADMIN'),
    productController.remove
);

export default router;
