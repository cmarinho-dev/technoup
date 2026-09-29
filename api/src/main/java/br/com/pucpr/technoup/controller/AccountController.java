package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.AccountServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api")
public class AccountController {
    private final AccountServiceImplementation service;
    public AccountController(AccountServiceImplementation service) { this.service = service; }
    @PostMapping("/autenticacao/entrar")
    public ApiResponse login(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.login(new FormDataRequest(form), request); }
    @GetMapping("/autenticacao/sessao")
    public ApiResponse session(HttpServletRequest request) { return service.session(request); }
    @PostMapping("/autenticacao/sair")
    public ApiResponse logout(HttpServletRequest request) { return service.logout(request); }
    @PostMapping("/contas")
    public ApiResponse create(@RequestParam Map<String, String> form) { return service.create(new FormDataRequest(form)); }
    @PostMapping("/contas/perfil")
    public ApiResponse update(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.update(new FormDataRequest(form), request); }
    @GetMapping("/contas")
    public ApiResponse list(HttpServletRequest request) { return service.list(request); }
    @GetMapping("/contas/lojistas")
    public ApiResponse shopAccounts(HttpServletRequest request) { return service.shopAccounts(request); }
    @PostMapping("/contas/status")
    public ApiResponse toggle(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.toggle(new FormDataRequest(form), request); }
    @PostMapping("/contas/excluir")
    public ApiResponse delete(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.delete(new FormDataRequest(form), request); }
}
