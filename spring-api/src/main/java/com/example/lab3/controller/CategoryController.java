
package com.example.lab3.controller;

import com.example.lab3.dto.CategoryDto;
import com.example.lab3.service.CategoryService;
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
import org.springframework.web.bind.annotation.*;

import java.util.List;
@SecurityRequirement(name = "Bearer Authentication")

@RestController
@RequestMapping("/api/categories")
@RequiredArgsConstructor
public class CategoryController {

    private final CategoryService categoryService;

    // =========================
    // CREATE - ADMIN only
    // =========================

    @Operation(
            summary = "Créer une catégorie",
            description = "Ajoute une nouvelle catégorie dans la base de données. Accessible uniquement aux administrateurs."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "201", description = "Catégorie créée avec succès"),
            @ApiResponse(responseCode = "400", description = "Données invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content)
    })
    @PostMapping
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<CategoryDto> create(
            @Valid @RequestBody CategoryDto dto) {

        return new ResponseEntity<>(
                categoryService.create(dto),
                HttpStatus.CREATED
        );
    }

    // =========================
    // GET BY ID - USER or ADMIN
    // =========================

    @Operation(
            summary = "Récupérer une catégorie",
            description = "Recherche une catégorie par son identifiant. Accessible aux utilisateurs et aux administrateurs."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Catégorie trouvée"),
            @ApiResponse(responseCode = "401", description = "Non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content),
            @ApiResponse(responseCode = "404", description = "Catégorie introuvable", content = @Content)
    })
    @GetMapping("/{id}")
    @PreAuthorize("hasAnyRole('USER', 'ADMIN')")
    public ResponseEntity<CategoryDto> get(
            @Parameter(description = "Identifiant de la catégorie", example = "1")
            @PathVariable Long id) {

        return ResponseEntity.ok(
                categoryService.getById(id)
        );
    }

    // =========================
    // GET ALL - USER or ADMIN
    // =========================

    @Operation(
            summary = "Lister les catégories",
            description = "Retourne toutes les catégories enregistrées dans la base de données."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Liste des catégories récupérée"),
            @ApiResponse(responseCode = "401", description = "Non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès interdit", content = @Content)
    })
    @GetMapping
    @PreAuthorize("hasAnyRole('USER', 'ADMIN')")
    public ResponseEntity<List<CategoryDto>> list() {

        return ResponseEntity.ok(
                categoryService.findAll()
        );
    }

    // =========================
    // UPDATE - ADMIN only
    // =========================

    @Operation(
            summary = "Modifier une catégorie",
            description = "Met à jour les informations d'une catégorie existante. Accessible uniquement aux administrateurs."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "200", description = "Catégorie modifiée avec succès"),
            @ApiResponse(responseCode = "400", description = "Données invalides", content = @Content),
            @ApiResponse(responseCode = "401", description = "Non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content),
            @ApiResponse(responseCode = "404", description = "Catégorie introuvable", content = @Content)
    })
    @PutMapping("/{id}")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<CategoryDto> update(
            @Parameter(description = "Identifiant de la catégorie à modifier", example = "1")
            @PathVariable Long id,
            @Valid @RequestBody CategoryDto dto) {

        return ResponseEntity.ok(
                categoryService.update(id, dto)
        );
    }

    // =========================
    // DELETE - ADMIN only
    // =========================

    @Operation(
            summary = "Supprimer une catégorie",
            description = "Supprime une catégorie à partir de son identifiant. Accessible uniquement aux administrateurs."
    )
    @ApiResponses({
            @ApiResponse(responseCode = "204", description = "Catégorie supprimée avec succès"),
            @ApiResponse(responseCode = "401", description = "Non authentifié", content = @Content),
            @ApiResponse(responseCode = "403", description = "Accès réservé aux administrateurs", content = @Content),
            @ApiResponse(responseCode = "404", description = "Catégorie introuvable", content = @Content)
    })
    @DeleteMapping("/{id}")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<Void> delete(
            @Parameter(description = "Identifiant de la catégorie à supprimer", example = "1")
            @PathVariable Long id) {

        categoryService.delete(id);

        return ResponseEntity.noContent().build();
    }
}
