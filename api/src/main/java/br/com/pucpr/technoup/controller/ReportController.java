package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.ReportServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/denuncias")
public class ReportController {
    private final ReportServiceImplementation service;
    public ReportController(ReportServiceImplementation service) { this.service = service; }
    @GetMapping("/alvos")
    public ApiResponse targets(HttpServletRequest request) { return service.targets(request); }
    @PostMapping
    public ApiResponse create(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.create(new FormDataRequest(form), request); }
    @GetMapping
    public ApiResponse list(@RequestParam(value = "status", required = false) String status, HttpServletRequest request) { return service.list(status, request); }
    @PostMapping("/status")
    public ApiResponse update(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.update(new FormDataRequest(form), request); }
}
