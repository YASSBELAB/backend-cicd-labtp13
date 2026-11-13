
package com.example.lab3.service;

import com.example.lab3.dto.CategoryDto;
import com.example.lab3.entity.Category;
import com.example.lab3.repository.CategoryRepository;

import jakarta.persistence.EntityNotFoundException;

import lombok.RequiredArgsConstructor;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.ArrayList;
import java.util.List;

@Service
@RequiredArgsConstructor
@Transactional
public class CategoryService {

    private final CategoryRepository categoryRepo;

    // CREATE
    public CategoryDto create(CategoryDto dto) {

        Category category = new Category(
                null,
                dto.name(),
                new ArrayList<>()
        );

        Category saved = categoryRepo.save(category);

        return new CategoryDto(
                saved.getId(),
                saved.getName()
        );
    }

    // GET BY ID
    @Transactional(readOnly = true)
    public CategoryDto getById(Long id) {

        Category category = categoryRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Category not found"));

        return new CategoryDto(
                category.getId(),
                category.getName()
        );
    }

    // GET ALL
    @Transactional(readOnly = true)
    public List<CategoryDto> findAll() {

        return categoryRepo.findAll()
                .stream()
                .map(category -> new CategoryDto(
                        category.getId(),
                        category.getName()
                ))
                .toList();
    }

    // UPDATE
    public CategoryDto update(Long id, CategoryDto dto) {

        Category category = categoryRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Category not found"));

        category.setName(dto.name());

        Category updated = categoryRepo.save(category);

        return new CategoryDto(
                updated.getId(),
                updated.getName()
        );
    }

    // DELETE
    public void delete(Long id) {

        Category category = categoryRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Category not found"));

        categoryRepo.delete(category);
    }
}
