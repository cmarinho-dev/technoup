package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.ReportRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;
import java.util.Set;

@Service
public class ReportServiceImplementation {
    private static final Set<String> STATUSES = Set.of("pendente", "em_analise", "resolvida", "recusada");
    private final ReportRepository repository;
    private final Auth auth;
    public ReportServiceImplementation(ReportRepository repository, Auth auth) { this.repository = repository; this.auth = auth; }

    public ApiResponse targets(HttpServletRequest request) {
        var account = auth.current(request);
        Checks.require(account != null, "Faça login para denunciar uma conta.");
        String type = targetType(account);
        return ApiResponse.ok("", repository.listTargets(account.get("id"), type));
    }
    public ApiResponse create(FormDataRequest form, HttpServletRequest request) {
        var account = auth.current(request);
        Checks.require(account != null, "Faça login para enviar uma denúncia.");
        int target = Checks.number(form.get("denunciado_id"));
        String reason = Checks.text(form.get("motivo"));
        Checks.require(target > 0, "Selecione a conta denunciada.");
        Checks.require(target != ((Number) account.get("id")).intValue(), "Você não pode denunciar a própria conta.");
        Checks.require(Checks.length(reason, 10, 65535), "Informe o motivo da denúncia com pelo menos 10 caracteres.");
        String type = targetType(account);
        Checks.require(repository.targetExists(target, type),
                "Conta denunciada não encontrada.");
        long id = repository.createReport(account.get("id"), target, reason);
        return ApiResponse.ok("Denúncia registrada com sucesso.", Map.of("id", id));
    }
    public ApiResponse list(String status, HttpServletRequest request) {
        auth.role(request, "administrador");
        var rows = status != null && STATUSES.contains(status)
                ? repository.listByStatus(status)
                : repository.listAll();
        return ApiResponse.ok("", rows);
    }
    public ApiResponse update(FormDataRequest form, HttpServletRequest request) {
        auth.role(request, "administrador");
        int id = Checks.number(form.get("id"));
        String status = Checks.text(form.get("status"));
        Checks.require(id > 0, "ID da denúncia é obrigatório.");
        Checks.require(STATUSES.contains(status), "Status inválido.");
        Checks.require(repository.reportExists(id), "Denúncia não encontrada.");
        repository.updateStatus(status, id);
        return ApiResponse.ok("Status da denúncia atualizado.");
    }
    private String targetType(Map<String, Object> account) {
        return switch ((String) account.get("tipo")) {
            case "consumidor" -> "lojista";
            case "lojista" -> "consumidor";
            default -> throw new ApiException("Apenas clientes e lojistas podem enviar denúncias.");
        };
    }
}
