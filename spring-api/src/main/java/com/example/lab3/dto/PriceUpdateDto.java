package com.example.lab3.dto;

import io.swagger.v3.oas.annotations.media.Schema;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Positive;

public record PriceUpdateDto(
        @Schema(description = "Nouveau prix du produit", example = "89.99")
        @NotNull(message = "Price is required")
        @Positive(message = "Price must be positive")
        Double price
) {}
