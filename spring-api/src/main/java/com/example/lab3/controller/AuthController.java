
package com.example.lab3.controller;

import com.example.lab3.dto.RegisterRequest;
import com.example.lab3.dto.LoginRequest;
import com.example.lab3.dto.JwtResponse;
import com.example.lab3.entity.User;
import com.example.lab3.repository.UserRepository;
import com.example.lab3.security.JwtUtil;

import jakarta.validation.Valid;
import io.swagger.v3.oas.annotations.security.SecurityRequirement;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.media.Content;
import io.swagger.v3.oas.annotations.media.Schema;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/auth")
public class AuthController {

    private final UserRepository userRepo;
    private final PasswordEncoder encoder;
    private final AuthenticationManager authenticationManager;
    private final JwtUtil jwtUtil;

    public AuthController(
            UserRepository userRepo,
            PasswordEncoder encoder,
            AuthenticationManager authenticationManager,
            JwtUtil jwtUtil) {

        this.userRepo = userRepo;
        this.encoder = encoder;
        this.authenticationManager = authenticationManager;
        this.jwtUtil = jwtUtil;
    }

    // =========================
    // REGISTER
    // =========================

    @Operation(
            summary = "Inscrire un utilisateur",
            description = "Crée un nouveau compte utilisateur. Le mot de passe est chiffré et le rôle USER est attribué automatiquement."
    )
    @ApiResponses({
            @ApiResponse(
                    responseCode = "201",
                    description = "Utilisateur créé avec succès"
            ),
            @ApiResponse(
                    responseCode = "400",
                    description = "Données invalides",
                    content = @Content
            ),
            @ApiResponse(
                    responseCode = "409",
                    description = "Username ou email déjà utilisé",
                    content = @Content
            )
    })
    @PostMapping("/register")
    public ResponseEntity<String> register(
            @Valid @RequestBody
            @io.swagger.v3.oas.annotations.parameters.RequestBody(
                    description = "Informations nécessaires pour créer un compte",
                    required = true
            )
            RegisterRequest req) {

        if (userRepo.findByUsername(req.getUsername()).isPresent()) {
            return ResponseEntity.status(HttpStatus.CONFLICT)
                    .body("Username already exists");
        }

        if (userRepo.findByEmail(req.getEmail()).isPresent()) {
            return ResponseEntity.status(HttpStatus.CONFLICT)
                    .body("Email already exists");
        }

        User user = new User();

        user.setUsername(req.getUsername());
        user.setEmail(req.getEmail());
        user.setPassword(encoder.encode(req.getPassword()));

        // New registrations receive USER role
        user.setRole("USER");

        userRepo.save(user);

        return ResponseEntity.status(HttpStatus.CREATED)
                .body("User registered successfully");
    }

    // =========================
    // LOGIN
    // =========================

    @Operation(
            summary = "Connecter un utilisateur",
            description = "Authentifie un utilisateur avec son username et son mot de passe et génère un JWT."
    )
    @ApiResponses({
            @ApiResponse(
                    responseCode = "200",
                    description = "Connexion réussie",
                    content = @Content(
                            schema = @Schema(implementation = JwtResponse.class)
                    )
            ),
            @ApiResponse(
                    responseCode = "400",
                    description = "Données invalides",
                    content = @Content
            ),
            @ApiResponse(
                    responseCode = "401",
                    description = "Username ou mot de passe incorrect",
                    content = @Content
            )
    })
    @PostMapping("/login")
    public ResponseEntity<?> login(
            @Valid @RequestBody LoginRequest req) {

        try {
            Authentication auth = authenticationManager.authenticate(
                    new UsernamePasswordAuthenticationToken(
                            req.getUsername(),
                            req.getPassword()
                    )
            );

            UserDetails user = (UserDetails) auth.getPrincipal();

            String role = user.getAuthorities()
                    .iterator()
                    .next()
                    .getAuthority()
                    .replace("ROLE_", "");

            String jwt = jwtUtil.generateToken(
                    user.getUsername(),
                    role
            );

            return ResponseEntity.ok(new JwtResponse(jwt));

        } catch (AuthenticationException e) {

            return ResponseEntity.status(HttpStatus.UNAUTHORIZED)
                    .body("Invalid username or password");
        }
    }

    // =========================
    // REFRESH TOKEN
    // =========================

    @Operation(
            summary = "Renouveler un token JWT",
            description = "Vérifie le token Bearer existant et génère un nouveau token si l'ancien est valide et proche de son expiration."
    )
    @ApiResponses({
            @ApiResponse(
                    responseCode = "200",
                    description = "Token renouvelé avec succès",
                    content = @Content(
                            schema = @Schema(implementation = JwtResponse.class)
                    )
            ),
            @ApiResponse(
                    responseCode = "400",
                    description = "Le token n'est pas proche de son expiration",
                    content = @Content
            ),
            @ApiResponse(
                    responseCode = "401",
                    description = "Token manquant, invalide, expiré ou utilisateur introuvable",
                    content = @Content
            )
    })
    @PostMapping("/refresh")
    public ResponseEntity<?> refreshToken(

            @Parameter(
                    description = "Token JWT au format Bearer",
                    example = "Bearer eyJhbGciOiJIUzI1NiJ9..."
            )
            @RequestHeader(
                    value = "Authorization",
                    required = false
            )
            String authorizationHeader) {

        // Check Authorization header
        if (authorizationHeader == null ||
                !authorizationHeader.startsWith("Bearer ")) {

            return ResponseEntity.status(HttpStatus.UNAUTHORIZED)
                    .body("Missing or invalid Authorization header");
        }

        // Extract token
        String token = authorizationHeader.substring(7);

        // Check token validity
        if (!jwtUtil.validateToken(token)) {

            return ResponseEntity.status(HttpStatus.UNAUTHORIZED)
                    .body("Invalid or expired token");
        }

        // Check whether token is close to expiration
        if (!jwtUtil.isCloseToExpiration(token)) {

            return ResponseEntity.status(HttpStatus.BAD_REQUEST)
                    .body("Token is not close to expiration");
        }

        // Extract username
        String username = jwtUtil.extractUsername(token);

        // Retrieve current user and role from database
        User user = userRepo.findByUsername(username)
                .orElse(null);

        if (user == null) {
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED)
                    .body("User not found");
        }

        // Generate new token
        String newToken = jwtUtil.generateToken(
                user.getUsername(),
                user.getRole()
        );

        return ResponseEntity.ok(new JwtResponse(newToken));
    }
}
