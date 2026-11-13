package com.example.lab3.config;

import com.example.lab3.security.ApiKeyFilter;
import com.example.lab3.security.JwtAuthenticationFilter;

import java.util.List;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.http.HttpMethod;
import org.springframework.http.HttpStatus;

import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.method.configuration.EnableMethodSecurity;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;

import org.springframework.web.cors.CorsConfiguration;

@Configuration
@EnableMethodSecurity
public class SecurityConfig {

    private final JwtAuthenticationFilter jwtFilter;
    private final ApiKeyFilter apiKeyFilter;

    public SecurityConfig(JwtAuthenticationFilter jwtFilter, ApiKeyFilter apiKeyFilter) {
        this.jwtFilter = jwtFilter;
        this.apiKeyFilter = apiKeyFilter;
    }

    @Bean
    public SecurityFilterChain filterChain(HttpSecurity http) throws Exception {

        http
                .cors(cors -> cors.configurationSource(request -> {
                    CorsConfiguration cfg = new CorsConfiguration();

                    cfg.setAllowedOrigins(List.of(
                            "http://localhost:3000",
                            "http://127.0.0.1:5500",
                            "http://localhost:5173"
                    ));

                    cfg.setAllowedMethods(List.of(
                            "GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"
                    ));

                    cfg.setAllowedHeaders(List.of(
                            "Authorization", "Content-Type"
                    ));

                    cfg.setAllowCredentials(true);
                    return cfg;
                }))

                .csrf(csrf -> csrf.disable())

                .sessionManagement(session ->
                        session.sessionCreationPolicy(SessionCreationPolicy.STATELESS)
                )

                .exceptionHandling(exception -> exception
                        .authenticationEntryPoint((request, response, authException) ->
                                response.sendError(
                                        HttpStatus.UNAUTHORIZED.value(),
                                        "Unauthorized"
                                )
                        )
                        .accessDeniedHandler((request, response, accessDeniedException) ->
                                response.sendError(
                                        HttpStatus.FORBIDDEN.value(),
                                        "Forbidden"
                                )
                        )
                )

                .authorizeHttpRequests(auth -> auth

                        // Public authentication and Swagger endpoints
                        .requestMatchers(
                                "/api/auth/**",
                                "/swagger-ui.html",
                                "/swagger-ui/**",
                                "/v3/api-docs/**",
                                "/v3/api-docs"
                        ).permitAll()

                        // Products: USER, MANAGER and ADMIN
                        .requestMatchers("/api/products/**")
                        .hasAnyRole("USER", "MANAGER", "ADMIN")

                        // Categories: GET for USER, MANAGER and ADMIN
                        .requestMatchers(HttpMethod.GET, "/api/categories/**")
                        .hasAnyRole("USER", "MANAGER", "ADMIN")

                        // Other category operations: ADMIN only
                        .requestMatchers("/api/categories/**")
                        .hasRole("ADMIN")

                        // Orders: USER, MANAGER and ADMIN
                        .requestMatchers("/api/orders/**")
                        .hasAnyRole("USER", "MANAGER", "ADMIN")

                        .anyRequest().authenticated()
                )

                .addFilterBefore(
                        apiKeyFilter,
                        UsernamePasswordAuthenticationFilter.class
                )
                .addFilterBefore(
                        jwtFilter,
                        UsernamePasswordAuthenticationFilter.class
                );

        return http.build();
    }

    @Bean
    public AuthenticationManager authenticationManager(
            AuthenticationConfiguration authConfig) throws Exception {

        return authConfig.getAuthenticationManager();
    }
}
