package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.EvaluationRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.mapper.EvaluationMapper;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.multipart.MultipartFile;

@Service
public class EvaluationServiceImplementation {
    private static final Set<String> CATEGORIES = Set.of("Computador", "Hardware", "Periférico", "Monitor", "Ergonomia", "Armazenamento", "Memória", "Placa de vídeo");
    private static final Set<String> CONDITIONS = Set.of("Funcionando perfeitamente", "Funcionando com avarias", "Não funciona");
    private static final Set<String> STAGES = Set.of("aguardando_envio", "recebido", "em_avaliacao", "avaliado", "proposta_enviada", "finalizado");
    private final EvaluationRepository repository;
    private final Auth auth;
    private final MediaStorage media;
    public EvaluationServiceImplementation(EvaluationRepository repository, Auth auth, MediaStorage media) {
        this.repository = repository; this.auth = auth; this.media = media;
    }

    @Transactional
    public ApiResponse create(FormDataRequest form,
                              MultipartFile[] files,
                              HttpServletRequest request) {
        var account = auth.role(request, "consumidor");
        int shopId = Checks.number(form.get("loja_id"));
        String name = Checks.text(form.get("nome")), category = Checks.text(form.get("tipoitem"));
        String condition = Checks.text(form.get("estado")), details = Checks.text(form.get("detalhes"));
        Checks.require(shopId > 0, "Selecione a loja de destino.");
        Checks.require(Checks.length(name, 3, 100), "Informe um nome de item com 3 a 100 caracteres.");
        Checks.require(CATEGORIES.contains(category), "Selecione um tipo de item válido.");
        Checks.require(CONDITIONS.contains(condition), "Selecione o estado do item.");
        Checks.require(Checks.length(details, 0, 1000), "Os detalhes devem ter no máximo 1000 caracteres.");
        Checks.require(repository.destinationShopExists(shopId),
                "Loja de destino não encontrada.");
        var uploads = media.evaluation(files);
        long id = repository.createEvaluation(account.get("id"), shopId, name, category, condition, details);
        for (var upload : uploads) repository.addMedia(id, upload.get("arquivo"), upload.get("caminho"), upload.get("tipo_arquivo"));
        return ApiResponse.ok("Solicitação enviada para a loja.", Map.of("id", id));
    }
    public ApiResponse shopList(String status, HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        var rows = status != null && !status.isBlank()
                ? repository.listByShopAndStatus(shopId, status)
                : repository.listByShop(shopId);
        return ApiResponse.ok("", withMedia(rows));
    }
    public ApiResponse mine(HttpServletRequest request) {
        var account = auth.role(request, "consumidor");
        var rows = repository.listMine(account.get("id"));
        return ApiResponse.ok("", withMedia(rows));
    }
    @Transactional
    public ApiResponse answer(FormDataRequest form, HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        int id = Checks.number(form.get("avaliacao_id"));
        String action = Checks.text(form.get("acao"));
        Checks.require(id > 0 && (action.equals("aceitar") || action.equals("recusar")), "Informe uma solicitação e uma ação válida.");
        var evaluation = repository.findShopEvaluation(id, shopId);
        Checks.require(evaluation != null, "Solicitação não encontrada para esta loja.");
        Checks.require("pendente".equals(evaluation.get("status")), "Esta solicitação já foi respondida.");
        String status = action.equals("aceitar") ? "aceita" : "recusada";
        repository.updateDecision(status, id, shopId);
        Long chatId = action.equals("aceitar")
                ? repository.createChat(evaluation.get("consumidor_id"), shopId, id) : null;
        Map<String, Object> data = new LinkedHashMap<>();
        data.put("avaliacao_id", id); data.put("status", status); data.put("chat_id", chatId);
        return ApiResponse.ok(action.equals("aceitar") ? "Solicitação aceita. Chat liberado." : "Solicitação recusada.", data);
    }
    public ApiResponse stage(FormDataRequest form, HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        int id = Checks.number(form.get("avaliacao_id"));
        String stage = Checks.text(form.get("status_avaliacao"));
        Checks.require(id > 0 && STAGES.contains(stage), "Informe uma avaliação e um status válido.");
        Checks.require(repository.updateStage(stage, id, shopId) > 0, "Só é possível atualizar o andamento de avaliações aceitas.");
        return ApiResponse.ok("Status da avaliação atualizado.", Map.of("avaliacao_id", id, "status_avaliacao", stage));
    }
    public ApiResponse rate(FormDataRequest form, HttpServletRequest request) {
        var account = auth.role(request, "consumidor");
        int id = Checks.number(form.get("avaliacao_id")), score = Checks.number(form.get("nota"));
        Checks.require(id > 0, "Avaliação do item é obrigatória.");
        Checks.require(score >= 1 && score <= 5, "Informe uma nota entre 1 e 5.");
        var evaluation = repository.findConsumerEvaluation(id, account.get("id"));
        Checks.require(evaluation != null, "Solicitação não encontrada para este consumidor.");
        Checks.require("aceita".equals(evaluation.get("status")) &&
                        ("finalizado".equals(evaluation.get("status_avaliacao")) || "fechado".equals(evaluation.get("status_chat"))),
                "O atendimento precisa estar finalizado para receber avaliação.");
        Checks.require(!repository.ratingExists(id), "Este atendimento já foi avaliado.");
        repository.createRating(id, account.get("id"), evaluation.get("loja_id"), score, Checks.text(form.get("comentario")));
        return ApiResponse.ok("Avaliação enviada com sucesso.");
    }
    private List<Map<String, Object>> withMedia(List<Map<String, Object>> rows) {
        var result = new ArrayList<Map<String, Object>>();
        for (var row : rows) {
            result.add(EvaluationMapper.withMedia(row, repository.listMedia(row.get("id"))));
        }
        return result;
    }
}
