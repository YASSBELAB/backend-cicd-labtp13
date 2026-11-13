
package com.example.lab3.controller;

import com.example.lab3.dto.OrderDto;
import com.example.lab3.service.OrderService;
import io.swagger.v3.oas.annotations.security.SecurityRequirement;

import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.media.Content;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.web.bind.annotation.*;

import java.util.List;
@SecurityRequirement(name = "Bearer Authentication")

@RestController
@RequestMapping("/api/orders")
@RequiredArgsConstructor
public class OrderController {

    private final OrderService orderService;

    // =========================
    // CREATE ORDER - USER only
    // =========================

    @Operation(
            summary = "Créer une commande",
            description = "Crée une nouvelle commande pour l'utilisateur actuellement authentifié. Accessible uniquement au rôle USER."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "201", description = "Commande créée avec succès"),
            @ApiResponse(responseCode = "400", description = "Données de commande invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux utilisateurs", content = @Content)
    })
    @PostMapping
    @PreAuthorize("hasRole('USER')")
    public ResponseEntity<OrderDto> create(
            @AuthenticationPrincipal UserDetails user,
            @Valid @RequestBody OrderDto dto) {

        OrderDto created = orderService.create(
                user.getUsername(),
                dto
        );

        return new ResponseEntity<>(created, HttpStatus.CREATED);
    }

    // =========================
    // GET ALL ORDERS
    // USER sees own orders
    // MANAGER and ADMIN see all
    // =========================

    @Operation(
            summary = "Lister les commandes",
            description = "Retourne les commandes de l'utilisateur connecté. Les rôles MANAGER et ADMIN peuvent consulter toutes les commandes."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Liste des commandes récupérée"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public ResponseEntity<List<OrderDto>> list(
            @AuthenticationPrincipal UserDetails user) {

        boolean canReadAll = user.getAuthorities()
                .stream()
                .anyMatch(a ->
                        a.getAuthority().equals("ROLE_ADMIN")
                                || a.getAuthority().equals("ROLE_MANAGER")
                );

        List<OrderDto> orders;

        if (canReadAll) {
            orders = orderService.findAll();
        } else {
            orders = orderService.findByUsername(user.getUsername());
        }

        return ResponseEntity.ok(orders);
    }

    // =========================
    // GET ORDER BY ID
    // USER sees own order
    // MANAGER and ADMIN see all
    // =========================

    @Operation(
            summary = "Récupérer une commande",
            description = "Recherche une commande par identifiant. Un utilisateur USER peut consulter uniquement ses propres commandes, tandis que MANAGER et ADMIN peuvent consulter toutes les commandes."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Commande trouvée"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Commande appartenant à un autre utilisateur", content = @Content),
            @ApiResponse(responseCode = "404", description = "Commande introuvable", content = @Content)
    })
    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public ResponseEntity<OrderDto> get(
            @Parameter(
                    description = "Identifiant de la commande",
                    example = "1"
            )
            @PathVariable Long id,
            @AuthenticationPrincipal UserDetails user) {

        boolean canReadAll = user.getAuthorities()
                .stream()
                .anyMatch(a ->
                        a.getAuthority().equals("ROLE_ADMIN")
                                || a.getAuthority().equals("ROLE_MANAGER")
                );

        return ResponseEntity.ok(
                orderService.getById(
                        id,
                        user.getUsername(),
                        canReadAll
                )
        );
    }

    // =========================
    // DELETE ORDER - ADMIN only
    // =========================

    @Operation(
            summary = "Supprimer une commande",
            description = "Supprime une commande à partir de son identifiant. Cette opération est réservée au rôle ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "204", description = "Commande supprimée avec succès"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content),
            @ApiResponse(responseCode = "404", description = "Commande introuvable", content = @Content)
    })
    @DeleteMapping("/{id}")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<Void> delete(
            @Parameter(
                    description = "Identifiant de la commande à supprimer",
                    example = "1"
            )
            @PathVariable Long id) {

        orderService.delete(id);

        return ResponseEntity.noContent().build();
    }
}
