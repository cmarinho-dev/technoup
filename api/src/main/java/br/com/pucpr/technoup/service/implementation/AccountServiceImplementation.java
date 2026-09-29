package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.AccountRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;

@Service
public class AccountServiceImplementation {
    private final AccountRepository repository;
    private final Auth auth;
    public AccountServiceImplementation(AccountRepository repository, Auth auth) { this.repository = repository; this.auth = auth; }

    public ApiResponse login(FormDataRequest form, HttpServletRequest request) {
        return auth.login(form.get("email"), form.get("senha"), request);
    }
    public ApiResponse session(HttpServletRequest request) { return auth.session(request); }
    public ApiResponse logout(HttpServletRequest request) {
        var session = request.getSession(false);
        if (session != null) session.invalidate();
        return ApiResponse.ok("Logout realizado com sucesso.");
    }
    public ApiResponse create(FormDataRequest form) {
        String name = Checks.text(form.get("nome")), email = Checks.text(form.get("email"));
        String cpf = Checks.digits(form.get("cpf")), password = Checks.text(form.get("senha"));
        String type = Checks.text(form.getOrDefault("tipo", "consumidor"));
        Checks.require(!name.isEmpty() && !email.isEmpty() && !cpf.isEmpty() && !password.isEmpty(), "Nome, CPF, email e senha são obrigatórios.");
        Checks.require(Checks.length(name, 3, 200), "Informe o nome completo com pelo menos 3 caracteres.");
        Checks.require(Checks.email(email), "Email inválido.");
        Checks.require(Checks.cpf(cpf), "CPF inválido.");
        Checks.require(type.equals("consumidor") || type.equals("lojista"), "Tipo de conta inválido.");
        Checks.require(Checks.length(password, 6, 255), "A senha deve ter pelo menos 6 caracteres.");
        Checks.require(!repository.emailExists(email), "Este email já está cadastrado.");
        Checks.require(!repository.cpfExists(cpf), "Este CPF já está cadastrado.");
        long id = repository.createAccount(name, cpf, email, auth.hash(password), type);
        return ApiResponse.ok("Conta criada com sucesso.", Map.of("id", id));
    }
    public ApiResponse update(FormDataRequest form, HttpServletRequest request) {
        var account = auth.required(request);
        String name = Checks.text(form.get("nome")), email = Checks.text(form.get("email"));
        String cpf = Checks.digits(form.get("cpf")), password = Checks.text(form.get("senha"));
        Checks.require(!name.isEmpty() && !email.isEmpty() && !cpf.isEmpty(), "Nome, CPF e email são obrigatórios.");
        Checks.require(Checks.length(name, 3, 200), "Informe o nome completo com pelo menos 3 caracteres.");
        Checks.require(Checks.email(email), "Email inválido.");
        Checks.require(Checks.cpf(cpf), "CPF inválido.");
        Checks.require(!repository.contactExistsForOtherAccount(email, cpf, account.get("id")),
                "Email ou CPF já cadastrado em outra conta.");
        if (password.isEmpty()) repository.updateProfile(name, cpf, email, account.get("id"));
        else repository.updateProfileWithPassword(name, cpf, email, auth.hash(password), account.get("id"));
        return ApiResponse.ok("Dados atualizados com sucesso.");
    }
    public ApiResponse list(HttpServletRequest request) {
        auth.role(request, "administrador");
        return ApiResponse.ok("", repository.listAccounts());
    }
    public ApiResponse shopAccounts(HttpServletRequest request) {
        auth.role(request, "administrador");
        return ApiResponse.ok("", repository.listShopAccounts());
    }
    public ApiResponse toggle(FormDataRequest form, HttpServletRequest request) {
        auth.role(request, "administrador");
        int id = Checks.number(form.get("id"));
        Checks.require(id > 0, "ID da conta é obrigatório.");
        var account = repository.findAccountStatus(id);
        Checks.require(account != null, "Conta não encontrada.");
        int active = Checks.databaseInt(account.get("ativo")) == 1 ? 0 : 1;
        repository.setAccountStatus(active, id);
        return ApiResponse.ok("Status alterado com sucesso.", Map.of("ativo", active));
    }
    public ApiResponse delete(FormDataRequest form, HttpServletRequest request) {
        auth.role(request, "administrador");
        int id = Checks.number(form.get("id"));
        Checks.require(id > 0, "ID da conta é obrigatório.");
        Checks.require(repository.deleteAccount(id) > 0, "Conta não encontrada.");
        return ApiResponse.ok("Conta e dados vinculados deletados com sucesso.");
    }
}
