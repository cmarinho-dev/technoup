package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.ProductServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

@RestController
@RequestMapping("/api/produtos")
public class ProductController {
    private final ProductServiceImplementation service;
    public ProductController(ProductServiceImplementation service) { this.service = service; }
    @GetMapping
    public ApiResponse list(@RequestParam Map<String, String> query) { return service.list(new FormDataRequest(query)); }
    @PostMapping
    public ApiResponse create(@RequestParam Map<String, String> form, @RequestParam(value = "imagem", required = false) MultipartFile image, HttpServletRequest request) { return service.create(new FormDataRequest(form), image, request); }
    @PostMapping("/alterar")
    public ApiResponse update(@RequestParam Map<String, String> form, @RequestParam(value = "imagem", required = false) MultipartFile image, HttpServletRequest request) { return service.update(new FormDataRequest(form), image, request); }
    @PostMapping("/excluir")
    public ApiResponse delete(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.delete(new FormDataRequest(form), request); }
}
