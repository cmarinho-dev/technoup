package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.ShopServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/lojas")
public class ShopController {
    private final ShopServiceImplementation service;
    public ShopController(ShopServiceImplementation service) { this.service = service; }
    @GetMapping
    public ApiResponse list(@RequestParam Map<String, String> query) { return service.list(new FormDataRequest(query)); }
    @PostMapping
    public ApiResponse create(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.create(new FormDataRequest(form), request); }
    @PostMapping("/alterar")
    public ApiResponse update(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.update(new FormDataRequest(form), request); }
}
