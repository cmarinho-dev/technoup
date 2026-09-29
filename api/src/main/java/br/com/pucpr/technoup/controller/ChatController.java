package br.com.pucpr.technoup.controller;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.service.implementation.ChatServiceImplementation;
import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/chats")
public class ChatController {
    private final ChatServiceImplementation service;
    public ChatController(ChatServiceImplementation service) { this.service = service; }
    @GetMapping
    public ApiResponse list(HttpServletRequest request) { return service.list(request); }
    @GetMapping("/detalhes")
    public ApiResponse get(@RequestParam Map<String, String> query, HttpServletRequest request) { return service.get(new FormDataRequest(query), request); }
    @PostMapping("/mensagens")
    public ApiResponse send(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.send(new FormDataRequest(form), request); }
    @PostMapping("/fechar")
    public ApiResponse close(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.close(new FormDataRequest(form), request); }
    @PostMapping("/marcar-lido")
    public ApiResponse markRead(@RequestParam Map<String, String> form, HttpServletRequest request) { return service.markRead(new FormDataRequest(form), request); }
}
