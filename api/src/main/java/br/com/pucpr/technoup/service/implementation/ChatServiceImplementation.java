package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.ChatRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.mapper.ChatMapper;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import org.springframework.transaction.annotation.Transactional;

@Service
public class ChatServiceImplementation {
    private final ChatRepository repository;
    private final Auth auth;
    public ChatServiceImplementation(ChatRepository repository, Auth auth) { this.repository = repository; this.auth = auth; }

    public ApiResponse list(HttpServletRequest request) {
        var account = auth.required(request);
        Object[] args = scopeArgs(account);
        var rows = repository.listChats((String) account.get("tipo"), args);
        return ApiResponse.ok("", rows);
    }
    public ApiResponse get(FormDataRequest query, HttpServletRequest request) {
        var account = auth.required(request);
        var chat = allowed(account, query, false);
        int lastId = Checks.number(query.get("ultimo_id"));
        var messages = repository.listMessages(chat.get("id"), lastId);
        return ApiResponse.ok("", ChatMapper.detail(chat, account, messages));
    }
    @Transactional
    public ApiResponse send(FormDataRequest form, HttpServletRequest request) {
        var account = auth.required(request);
        String message = Checks.text(form.get("mensagem"));
        Checks.require(!message.isEmpty(), "Digite uma mensagem.");
        Checks.require(Checks.length(message, 0, 1000), "A mensagem deve ter no máximo 1000 caracteres.");
        Checks.require(Checks.number(form.get("chat_id")) > 0 || Checks.number(form.get("avaliacao_id")) > 0,
                "Informe uma avaliação aceita ou um chat.");
        var chat = allowed(account, form, true);
        Checks.require("aberto".equals(chat.get("chat_status")), "Este chat foi fechado.");
        int isCliente = "consumidor".equals(account.get("tipo")) ? 0 : 1;
        long id = repository.createMessage(isCliente, chat.get("id"), message);
        repository.touchChat(chat.get("id"));
        return ApiResponse.ok("Mensagem enviada.", repository.findMessage(id));
    }
    public ApiResponse close(FormDataRequest form, HttpServletRequest request) {
        var account = auth.required(request);
        int id = Checks.number(form.get("chat_id"));
        Checks.require(id > 0, "Chat inválido.");
        Checks.require("lojista".equals(account.get("tipo")), "Apenas a loja pode fechar este chat.");
        int shopId = auth.shopId(account);
        Checks.require(repository.closeChat(id, shopId) > 0,
                "Não foi possível fechar este chat.");
        return ApiResponse.ok("Chat fechado.");
    }
    public ApiResponse markRead(FormDataRequest form, HttpServletRequest request) {
        var account = auth.required(request);
        int id = Checks.number(form.get("chat_id"));
        Checks.require(id > 0, "Chat inválido.");
        switch ((String) account.get("tipo")) {
            case "consumidor" -> repository.markConsumerRead(id, account.get("id"));
            case "lojista" -> repository.markShopRead(id, auth.shopId(account));
            case "administrador" -> repository.markAdminRead(id);
            default -> throw new ApiException("Acesso restrito.");
        }
        return ApiResponse.ok("Chat marcado como lido.");
    }
    private Map<String, Object> allowed(Map<String, Object> account, FormDataRequest params, boolean requiredId) {
        int chatId = Checks.number(params.get("chat_id"));
        int evaluationId = Checks.number(params.get("avaliacao_id"));
        String filter;
        Object[] args;
        if (chatId > 0) { filter = "chat"; args = new Object[]{chatId}; }
        else if (evaluationId > 0) { filter = "evaluation"; args = new Object[]{evaluationId}; }
        else { filter = "accepted"; args = new Object[0]; }
        if (requiredId) Checks.require(chatId > 0 || evaluationId > 0, "Informe uma avaliação aceita ou um chat.");
        Object[] scoped = scopeArgs(account);
        Object[] all = java.util.Arrays.copyOf(args, args.length + scoped.length);
        System.arraycopy(scoped, 0, all, args.length, scoped.length);
        var chat = repository.findAllowedChat(filter, (String) account.get("tipo"), all);
        Checks.require(chat != null, "Nenhum chat liberado. A loja precisa aceitar uma solicitação de avaliação primeiro.");
        Checks.require("aceita".equals(chat.get("status")), "Este chat ainda não foi liberado pela loja.");
        return chat;
    }
    private Object[] scopeArgs(Map<String, Object> account) {
        return switch ((String) account.get("tipo")) {
            case "consumidor" -> new Object[]{account.get("id")};
            case "lojista" -> new Object[]{auth.shopId(account)};
            default -> new Object[0];
        };
    }
}
