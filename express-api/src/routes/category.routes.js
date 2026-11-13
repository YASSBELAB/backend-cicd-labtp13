
import { Router } from 'express';

import { categoryController } from '../controllers/category.controller.js';
import { validateCategory } from '../middlewares/validation.js';

const router = Router();

// GET: Retrieve all categories
router.get('/', categoryController.getAll);

// GET: Retrieve category by ID
router.get('/:id', categoryController.getById);

// POST: Create category
router.post(
    '/',
    validateCategory,
    categoryController.create
);

// PUT: Update category
router.put(
    '/:id',
    validateCategory,
    categoryController.update
);

// DELETE: Remove category
router.delete('/:id', categoryController.remove);

export default router;
