
package com.example.lab3.service;

import com.example.lab3.dto.OrderDto;
import com.example.lab3.dto.OrderItemDto;
import com.example.lab3.entity.Order;
import com.example.lab3.entity.OrderItem;
import com.example.lab3.entity.Product;
import com.example.lab3.entity.User;
import com.example.lab3.repository.OrderRepository;
import com.example.lab3.repository.ProductRepository;
import com.example.lab3.repository.UserRepository;

import jakarta.persistence.EntityNotFoundException;

import lombok.RequiredArgsConstructor;

import org.springframework.security.access.AccessDeniedException;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

@Service
@RequiredArgsConstructor
@Transactional
public class OrderService {

    private final OrderRepository orderRepo;
    private final ProductRepository productRepo;
    private final UserRepository userRepo;

    // CREATE ORDER - USER only (authorization is in controller)
    public OrderDto create(String username, OrderDto dto) {

        User user = userRepo.findByUsername(username)
                .orElseThrow(() ->
                        new EntityNotFoundException("User not found"));

        Order order = new Order(
                null,
                dto.orderDate() != null
                        ? dto.orderDate()
                        : LocalDateTime.now(),
                new ArrayList<>(),
                user
        );

        for (OrderItemDto itemDto : dto.items()) {

            Product product = productRepo.findById(itemDto.productId())
                    .orElseThrow(() ->
                            new EntityNotFoundException("Product not found"));

            OrderItem item = new OrderItem(
                    null,
                    product,
                    itemDto.quantity(),
                    order
            );

            order.getItems().add(item);
        }

        Order saved = orderRepo.save(order);

        return toDto(saved);
    }

    // GET ALL ORDERS - ADMIN and MANAGER
    @Transactional(readOnly = true)
    public List<OrderDto> findAll() {

        return orderRepo.findAll()
                .stream()
                .map(this::toDto)
                .toList();
    }

    // GET ORDERS BY USERNAME - USER only
    @Transactional(readOnly = true)
    public List<OrderDto> findByUsername(String username) {

        return orderRepo.findByUser_Username(username)
                .stream()
                .map(this::toDto)
                .toList();
    }

    // GET ORDER BY ID
    // ADMIN and MANAGER can access all orders.
    // USER can access only their own orders.
    @Transactional(readOnly = true)
    public OrderDto getById(
            Long id,
            String username,
            boolean canReadAll) {

        Order order = orderRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Order not found"));

        if (!canReadAll &&
                !order.getUser().getUsername().equals(username)) {

            throw new AccessDeniedException(
                    "You cannot access another user's order"
            );
        }

        return toDto(order);
    }

    // DELETE ORDER - ADMIN only (authorization is in controller)
    public void delete(Long id) {

        Order order = orderRepo.findById(id)
                .orElseThrow(() ->
                        new EntityNotFoundException("Order not found"));

        orderRepo.delete(order);
    }

    // CONVERT ENTITY TO DTO
    private OrderDto toDto(Order order) {

        List<OrderItemDto> items = order.getItems()
                .stream()
                .map(item -> new OrderItemDto(
                        item.getId(),
                        item.getProduct().getId(),
                        item.getQuantity()
                ))
                .toList();

        return new OrderDto(
                order.getId(),
                order.getOrderDate(),
                items
        );
    }
}
