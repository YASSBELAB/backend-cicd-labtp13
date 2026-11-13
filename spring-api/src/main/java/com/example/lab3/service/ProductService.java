
package com.example.lab3.service;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import com.example.lab3.dto.ProductDto;
import com.example.lab3.entity.Category;
import com.example.lab3.entity.Product;
import com.example.lab3.repository.CategoryRepository;
import com.example.lab3.repository.ProductRepository;

import jakarta.persistence.EntityNotFoundException;

import lombok.RequiredArgsConstructor;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
@RequiredArgsConstructor
@Transactional
public class ProductService {

    private final ProductRepository productRepo;
    private final CategoryRepository categoryRepo;

    // CREATE
    public ProductDto create(ProductDto dto) {

        Category cat = categoryRepo.findById(dto.categoryId())
                .orElseThrow(() ->
                        new EntityNotFoundException("Category not found"));

        Product p = new Product(
                null,
                dto.name(),
                dto.price(),
                cat
        );

        Product saved = productRepo.save(p);

        return new ProductDto(
                saved.getId(),
                saved.getName(),
                saved.getPrice(),
                cat.getId()
        );
    }

    // READ ALL
    @Transactional(readOnly = true)
    public List<ProductDto> findAll() {

        return productRepo.findAll()
                .stream()
                .map(p -> new ProductDto(
                        p.getId(),
                        p.getName(),
                        p.getPrice(),
                        p.getCategory() != null
                                ? p.getCategory().getId()
                                : null
                ))
                .toList();
    }

    // READ BY ID
    @Transactional(readOnly = true)
    public ProductDto findById(Long id) {

        Product p = productRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Product not found"));

        return new ProductDto(
                p.getId(),
                p.getName(),
                p.getPrice(),
                p.getCategory() != null
                        ? p.getCategory().getId()
                        : null
        );
    }

    // UPDATE
    public ProductDto update(Long id, ProductDto dto) {

        Product p = productRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Product not found"));

        Category cat = categoryRepo.findById(dto.categoryId())
                .orElseThrow(() ->
                        new EntityNotFoundException("Category not found"));

        p.setName(dto.name());
        p.setPrice(dto.price());
        p.setCategory(cat);

        Product saved = productRepo.save(p);

        return new ProductDto(
                saved.getId(),
                saved.getName(),
                saved.getPrice(),
                cat.getId()
        );
    }

    // UPDATE PRICE
    public ProductDto updatePrice(Long id, Double price) {

        Product p = productRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Product not found"));

        p.setPrice(price);
        Product saved = productRepo.save(p);

        return new ProductDto(
                saved.getId(),
                saved.getName(),
                saved.getPrice(),
                saved.getCategory() != null
                        ? saved.getCategory().getId()
                        : null
        );
    }

    // DELETE
    public void delete(Long id) {

        if (!productRepo.existsById(id)) {
            throw new EntityNotFoundException("Product not found");
        }

        productRepo.deleteById(id);
    }
    @Transactional(readOnly = true)
    public Page<ProductDto> findAll(Pageable pageable) {
        return productRepo.findAll(pageable)
                .map(product -> new ProductDto(
                        product.getId(),
                        product.getName(),
                        product.getPrice(),
                        product.getCategory().getId()
                ));
    }

    // SEARCH BY NAME
    @Transactional(readOnly = true)
    public List<ProductDto> search(String keyword) {

        return productRepo.findByNameContainingIgnoreCase(keyword)
                .stream()
                .map(p -> new ProductDto(
                        p.getId(),
                        p.getName(),
                        p.getPrice(),
                        p.getCategory() != null
                                ? p.getCategory().getId()
                                : null
                ))
                .toList();
    }
    @Transactional(readOnly = true)
    public List<ProductDto> findByCategoryId(Long categoryId) {
        return productRepo.findByCategoryId(categoryId)
                .stream()
                .map(product -> new ProductDto(
                        product.getId(),
                        product.getName(),
                        product.getPrice(),
                        product.getCategory().getId()
                ))
                .toList();
    }
}
