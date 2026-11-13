
import { categoryService } from '../services/category.service.js';

export const categoryController = {

  // GET: Retrieve all categories
  getAll: async (req, res, next) => {
    try {
      const categories = await categoryService.listCategories();

      res.status(200).json({
        success: true,
        data: categories
      });
    } catch (error) {
      next(error);
    }
  },

  // GET: Retrieve category by ID
  getById: async (req, res, next) => {
    try {
      const id = parseInt(req.params.id, 10);

      const category = await categoryService.getCategory(id);

      res.status(200).json({
        success: true,
        data: category
      });
    } catch (error) {
      next(error);
    }
  },

  // POST: Create a new category
  create: async (req, res, next) => {
    try {
      const newCategory = await categoryService.createCategory(req.body);

      res.status(201).json({
        success: true,
        message: 'Category created successfully',
        data: newCategory
      });
    } catch (error) {
      next(error);
    }
  },

  // PUT: Update category
  update: async (req, res, next) => {
    try {
      const id = parseInt(req.params.id, 10);

      const updatedCategory = await categoryService.updateCategory(
        id,
        req.body
      );

      res.status(200).json({
        success: true,
        message: 'Category updated successfully',
        data: updatedCategory
      });
    } catch (error) {
      next(error);
    }
  },

  // DELETE: Remove category
  remove: async (req, res, next) => {
    try {
      const id = parseInt(req.params.id, 10);

      await categoryService.deleteCategory(id);

      res.status(204).send();
    } catch (error) {
      next(error);
    }
  }

};
