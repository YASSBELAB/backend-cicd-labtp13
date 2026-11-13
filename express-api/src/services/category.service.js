
import { categoryRepository } from '../repositories/categoryRepository.js';
import { productRepository } from '../repositories/product.repository.js';

export const categoryService = {
  // Get all categories
  listCategories: async () => {
    return await categoryRepository.getAll();
  },

  // Get category by ID
  getCategory: async (id) => {
    const category = await categoryRepository.getById(id);

    if (!category) {
      throw new Error('Category not found');
    }

    return category;
  },

  // Create category
  createCategory: async (data) => {
    return await categoryRepository.create(data);
  },

  // Update category
  updateCategory: async (id, data) => {
    await categoryService.getCategory(id);

    return await categoryRepository.update(id, data);
  },

  // Delete category
  deleteCategory: async (id) => {
    await categoryService.getCategory(id);

    // Check whether products are linked to this category
    const products = await productRepository.findAll();

    const linkedProducts = products.filter(
      product => Number(product.category_id) === Number(id)
    );

    if (linkedProducts.length > 0) {
      throw new Error('Cannot delete category with linked products');
    }

    return await categoryRepository.remove(id);
  }
};
