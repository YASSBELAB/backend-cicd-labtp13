package com.example.lab3.controller;

import com.example.lab3.entity.Category;
import com.example.lab3.entity.Product;
import com.example.lab3.entity.User;
import com.example.lab3.repository.CategoryRepository;
import com.example.lab3.repository.ProductRepository;
import com.example.lab3.repository.UserRepository;
import com.example.lab3.security.JwtUtil;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.webmvc.test.autoconfigure.AutoConfigureMockMvc;
import org.springframework.http.MediaType;
import org.springframework.test.web.servlet.MockMvc;
import org.springframework.transaction.annotation.Transactional;

import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.patch;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;

@SpringBootTest
@AutoConfigureMockMvc
@Transactional
class ProductControllerTest {

    @Autowired
    private MockMvc mvc;

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private CategoryRepository categoryRepository;

    @Autowired
    private ProductRepository productRepository;

    @Autowired
    private JwtUtil jwtUtil;

    private String createTokenForRole(String role, String suffix) {
        User user = userRepository.save(new User(
                null,
                "test_" + role.toLowerCase() + "_" + suffix,
                "test_" + role.toLowerCase() + "_" + suffix + "@example.com",
                "encoded-password",
                role
        ));
        return jwtUtil.generateToken(user.getUsername(), user.getRole());
    }

    private Product createSampleProduct(String suffix) {
        Category category = categoryRepository.save(new Category(null, "Périphériques " + suffix, null));
        return productRepository.save(new Product(null, "Clavier mécanique " + suffix, 79.99, category));
    }

    @Test
    void shouldReturnAll() throws Exception {
        String suffix = String.valueOf(System.nanoTime());
        String token = createTokenForRole("USER", suffix);
        createSampleProduct(suffix);

        mvc.perform(get("/api/products")
                        .header("Authorization", "Bearer " + token))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$[0].id").exists());
    }

    @Test
    void shouldAuthenticateWithApiKey() throws Exception {
        String suffix = String.valueOf(System.nanoTime());
        Product product = createSampleProduct(suffix);

        mvc.perform(patch("/api/products/{id}/price", product.getId())
                        .header("Authorization", "Bearer secret-api-key-123")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"price\": 99.99}"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.id").value(product.getId()))
                .andExpect(jsonPath("$.price").value(99.99));
    }

    @Test
    void shouldRejectInvalidBearerToken() throws Exception {
        mvc.perform(get("/api/products")
                        .header("Authorization", "Bearer wrong-api-key"))
                .andExpect(status().isUnauthorized());
    }

    @Test
    void shouldUpdateProductPrice() throws Exception {
        String suffix = String.valueOf(System.nanoTime());
        String token = createTokenForRole("MANAGER", suffix);
        Product product = createSampleProduct(suffix);

        mvc.perform(patch("/api/products/{id}/price", product.getId())
                        .header("Authorization", "Bearer " + token)
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"price\": 89.99}"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.id").value(product.getId()))
                .andExpect(jsonPath("$.price").value(89.99));
    }

    @Test
    void shouldForbidPriceUpdateForUserRole() throws Exception {
        String suffix = String.valueOf(System.nanoTime());
        String token = createTokenForRole("USER", suffix);
        Product product = createSampleProduct(suffix);

        mvc.perform(patch("/api/products/{id}/price", product.getId())
                        .header("Authorization", "Bearer " + token)
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"price\": 89.99}"))
                .andExpect(status().isForbidden());
    }

    @Test
    void shouldExposePriceEndpointAndSecuritySchemeInOpenApi() throws Exception {
        mvc.perform(get("/v3/api-docs"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.paths['/api/products/{id}/price'].patch").exists())
                .andExpect(jsonPath("$.components.schemas.PriceUpdateDto").exists())
                .andExpect(jsonPath("$.components.securitySchemes['Bearer Authentication'].scheme").value("bearer"))
                .andExpect(jsonPath("$.security[0]['Bearer Authentication']").isArray());
    }
}
