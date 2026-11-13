package com.example.lab3.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

public record ProductDto(
        Long id,

        @NotBlank(message = "Product name is required")
        String name,

        @NotNull(message = "Price is required")
        @Positive(message = "Price must be positive")
        Double price,

        @NotNull(message = "Category ID is required")
        Long categoryId
) {}