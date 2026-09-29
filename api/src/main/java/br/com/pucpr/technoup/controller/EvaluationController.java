package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.EvaluationServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

@RestController
@RequestMapping("/api/avaliacoes")
public class EvaluationController {
    private final EvaluationServiceImplementation service;
    public EvaluationController(EvaluationServiceImplementation service) { this.service = service; }
    @PostMapping
    public ApiResponse create(@RequestParam Map<String, String> form, @RequestParam(value = "midias[]", required = false) MultipartFile[] files, HttpServletRequest request) { return service.create(new FormDataRequest(form), files, request); }
    @GetMapping("/loja")
    public ApiResponse shopList(@RequestParam(value = "status", required = false) String status, HttpServletRequest request) { return service.shopList(status, request); }
    @GetMapping("/minhas")
    public ApiResponse mine(HttpServletRequest request) { return service.mine(request); }
    @PostMapping("/responder")
    public ApiResponse answer(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.answer(new FormDataRequest(form), request); }
    @PostMapping("/status")
    public ApiResponse stage(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.stage(new FormDataRequest(form), request); }
    @PostMapping("/atendimento")
    public ApiResponse rate(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.rate(new FormDataRequest(form), request); }
}
