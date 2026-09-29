package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.AuthRepository;

import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.exception.ApiException;

import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpSession;
import java.util.LinkedHashMap;
import java.util.Map;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Service;

@Service
public class Auth {
    private final AuthRepository repository;
    private final BCryptPasswordEncoder passwords = new BCryptPasswordEncoder();
    public Auth(AuthRepository repository) { this.repository = repository; }

    public ApiResponse login(String email, String password, HttpServletRequest request) {
        email = Checks.text(email);
        password = Checks.text(password);
        Checks.require(!email.isEmpty() && !password.isEmpty(), "Email e senha são obrigatórios.");
        Checks.require(Checks.email(email), "Email inválido.");
        var account = repository.findByEmail(email);
        Checks.require(account != null && matches(password, (String) account.get("senha")), "Credenciais inválidas.");
        if (Checks.databaseInt(account.get("ativo")) == 0) {
            String message = "lojista".equals(account.get("tipo"))
                    ? "Sua unidade de trabalho está inativa. Entre em contato com o administrador para mais informações."
                    : "Sua conta está inativa. Entre em contato com o administrador para mais informações.";
            throw new ApiException(message);
        }
        if (!((String) account.get("senha")).startsWith("$2"))
            repository.upgradePassword(passwords.encode(password), account.get("id"));
        var old = request.getSession(false);
        if (old != null) old.invalidate();
        request.getSession(true).setAttribute("accountId", ((Number) account.get("id")).intValue());
        account.remove("senha");
        Map<String, Object> data = new LinkedHashMap<>();
        data.put("usuario", account);
        data.put("loja", shopOrNull(account));
        return ApiResponse.ok("Login realizado com sucesso.", data);
    }

    private boolean matches(String plain, String stored) {
        return stored != null && (stored.startsWith("$2") ? passwords.matches(plain, stored) : stored.equals(plain));
    }
    public String hash(String plain) { return passwords.encode(plain); }
    public Map<String, Object> current(HttpServletRequest request) {
        HttpSession session = request.getSession(false);
        if (session == null || session.getAttribute("accountId") == null) return null;
        var account = repository.findById(session.getAttribute("accountId"));
        if (account == null || Checks.databaseInt(account.get("ativo")) == 0) {
            session.invalidate();
            return null;
        }
        return account;
    }
    public Map<String, Object> required(HttpServletRequest request) {
        var account = current(request);
        Checks.require(account != null, "Você precisa estar logado.");
        return account;
    }
    public Map<String, Object> role(HttpServletRequest request, String role) {
        var account = current(request);
        Checks.require(account != null || !"administrador".equals(role), "Acesso restrito ao administrador.");
        Checks.require(account != null && role.equals(account.get("tipo")), "Acesso restrito ao " + role + ".");
        return account;
    }
    public Map<String, Object> shop(Map<String, Object> account) {
        return repository.findShopForAccount(account.get("id"));
    }
    public Object shopOrNull(Map<String, Object> account) { return "lojista".equals(account.get("tipo")) ? shop(account) : null; }
    public int shopId(Map<String, Object> account) {
        var shop = shop(account);
        Checks.require(shop != null, "Loja não encontrada na sessão.");
        return ((Number) shop.get("id")).intValue();
    }
    public ApiResponse session(HttpServletRequest request) {
        var account = current(request);
        if (account == null) return ApiResponse.nok("Sessão não iniciada.");
        Map<String, Object> data = new LinkedHashMap<>();
        data.put("usuario", account);
        data.put("loja", shopOrNull(account));
        return ApiResponse.ok("", data);
    }
}
