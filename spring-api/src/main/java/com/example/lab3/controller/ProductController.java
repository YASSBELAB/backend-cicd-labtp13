
package com.example.lab3.controller;

import com.example.lab3.dto.PriceUpdateDto;
import com.example.lab3.dto.ProductDto;
import com.example.lab3.service.ProductService;
import io.swagger.v3.oas.annotations.security.SecurityRequirement;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.media.Content;

import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;

import lombok.RequiredArgsConstructor;

import java.util.List;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import jakarta.validation.Valid;

import org.springframework.security.access.prepost.PreAuthorize;
@SecurityRequirement(name = "Bearer Authentication")

@RestController
@RequestMapping("/api/products")
@RequiredArgsConstructor
@SecurityRequirement(name = "Bearer Authentication")

public class ProductController {

    private final ProductService productService;

    // =========================
    // CREATE - MANAGER and ADMIN
    // =========================

    @Operation(
            summary = "Créer un produit",
            description = "Ajoute un nouveau produit. Cette opération est réservée aux rôles MANAGER et ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "201", description = "Produit créé avec succès"),
            @ApiResponse(responseCode = "400", description = "Données du produit invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux MANAGER et ADMIN", content = @Content)
    })
    @PostMapping
    @PreAuthorize("hasAnyRole('MANAGER', 'ADMIN')")
    public ResponseEntity<ProductDto> create(
            @Valid @RequestBody ProductDto dto) {

        ProductDto created = productService.create(dto);
        return new ResponseEntity<>(created, HttpStatus.CREATED);
    }

    // =========================
    // READ ONE - USER, MANAGER and ADMIN
    // =========================

    @Operation(
            summary = "Récupérer un produit",
            description = "Recherche un produit par son identifiant. Accessible aux rôles USER, MANAGER et ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Produit trouvé"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content),
            @ApiResponse(responseCode = "404", description = "Produit introuvable", content = @Content)
    })
    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public ResponseEntity<ProductDto> get(
            @Parameter(
                    description = "Identifiant du produit",
                    example = "1"
            )
            @PathVariable Long id) {

        return ResponseEntity.ok(productService.findById(id));
    }

    // =========================
    // UPDATE - ADMIN only
    // =========================

    @Operation(
            summary = "Modifier un produit",
            description = "Modifie les informations d'un produit existant. Cette opération est réservée au rôle ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Produit modifié avec succès"),
            @ApiResponse(responseCode = "400", description = "Données du produit invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content),
            @ApiResponse(responseCode = "404", description = "Produit introuvable", content = @Content)
    })
    @PutMapping("/{id}")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<ProductDto> update(
            @Parameter(
                    description = "Identifiant du produit à modifier",
                    example = "1"
            )
            @PathVariable Long id,
            @Valid @RequestBody ProductDto dto) {

        return ResponseEntity.ok(productService.update(id, dto));
    }

    // =========================
    // UPDATE PRICE - MANAGER and ADMIN
    // =========================

    @Operation(
            summary = "Mettre à jour le prix d'un produit",
            description = "Met à jour uniquement le prix d'un produit existant. Cette opération est réservée aux rôles MANAGER et ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Prix du produit mis à jour avec succès"),
            @ApiResponse(responseCode = "400", description = "Prix invalide", content = @Content),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux MANAGER et ADMIN", content = @Content),
            @ApiResponse(responseCode = "404", description = "Produit introuvable", content = @Content)
    })
    @PatchMapping("/{id}/price")
    @PreAuthorize("hasAnyRole('MANAGER', 'ADMIN')")
    public ResponseEntity<ProductDto> updatePrice(
            @Parameter(
                    description = "Identifiant du produit",
                    example = "1"
            )
            @PathVariable Long id,
            @Valid @RequestBody PriceUpdateDto dto) {

        return ResponseEntity.ok(productService.updatePrice(id, dto.price()));
    }

    // =========================
    // DELETE - ADMIN only
    // =========================

    @Operation(
            summary = "Supprimer un produit",
            description = "Supprime un produit à partir de son identifiant. Cette opération est réservée au rôle ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "204", description = "Produit supprimé avec succès"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content),
            @ApiResponse(responseCode = "404", description = "Produit introuvable", content = @Content),
            @ApiResponse(responseCode = "409", description = "Suppression impossible : produit utilisé dans une commande", content = @Content)
    })
    @DeleteMapping("/{id}")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<Void> delete(
            @Parameter(
                    description = "Identifiant du produit à supprimer",
                    example = "1"
            )
            @PathVariable Long id) {

        productService.delete(id);
        return ResponseEntity.noContent().build();
    }

    // =========================
    // GET ALL - USER, MANAGER and ADMIN
    // =========================

    @Operation(
            summary = "Lister tous les produits",
            description = "Retourne la liste de tous les produits. Accessible aux rôles USER, MANAGER et ADMIN."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Liste des produits récupérée"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public ResponseEntity<?> getAll() {

        return ResponseEntity.ok(productService.findAll());
    }

    // =========================
    // SEARCH
    // =========================

    @Operation(
            summary = "Rechercher des produits par nom",
            description = "Recherche les produits dont le nom correspond au paramètre fourni."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Résultats de la recherche récupérés"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping("/search")
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public ResponseEntity<?> search(
            @Parameter(
                    description = "Nom ou partie du nom du produit à rechercher",
                    example = "Laptop"
            )
            @RequestParam String name) {

        return ResponseEntity.ok(productService.search(name));
    }

    // =========================
    // PAGINATION
    // =========================

    @Operation(
            summary = "Lister les produits avec pagination",
            description = "Retourne une page de produits avec un nombre d'éléments et un champ de tri configurables."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Page de produits récupérée"),
            @ApiResponse(responseCode = "400", description = "Paramètres de pagination ou de tri invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping("/page")
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public Page<ProductDto> list(

            @Parameter(description = "Numéro de la page, à partir de 0", example = "0")
            @RequestParam(defaultValue = "0") int page,

            @Parameter(description = "Nombre de produits par page", example = "5")
            @RequestParam(defaultValue = "5") int size,

            @Parameter(description = "Champ utilisé pour le tri", example = "id")
            @RequestParam(defaultValue = "id") String sort) {

        Pageable pageable = PageRequest.of(
                page,
                size,
                Sort.by(sort)
        );

        return productService.findAll(pageable);
    }

    // =========================
    // FILTER BY CATEGORY
    // =========================

    @Operation(
            summary = "Filtrer les produits par catégorie",
            description = "Retourne tous les produits associés à une catégorie donnée."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Produits de la catégorie récupérés"),
            @ApiResponse(responseCode = "401", description = "Utilisateur non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping("/category/{categoryId}")
    @PreAuthorize("hasAnyRole('USER', 'MANAGER', 'ADMIN')")
    public List<ProductDto> findByCategory(

            @Parameter(
                    description = "Identifiant de la catégorie",
                    example = "1"
            )
            @PathVariable Long categoryId) {

        return productService.findByCategoryId(categoryId);
    }
}
