
package com.example.lab3.dto;

public record OrderItemDto(
        Long id,
        Long productId,
        Integer quantity
) {}
