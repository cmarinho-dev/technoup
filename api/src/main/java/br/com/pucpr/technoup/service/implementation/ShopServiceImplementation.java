package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.ShopRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.entity.ShopData;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.util.Map;

@Service
public class ShopServiceImplementation {
    private final ShopRepository repository;
    private final Auth auth;
    public ShopServiceImplementation(ShopRepository repository, Auth auth) { this.repository = repository; this.auth = auth; }

    public ApiResponse list(FormDataRequest query) {
        var rows = query.containsKey("conta_id") && !query.get("conta_id").isBlank()
                ? repository.listByAccount(Checks.number(query.get("conta_id")))
                : query.containsKey("id") && !query.get("id").isBlank()
                ? repository.listById(Checks.number(query.get("id")))
                : repository.listActive();
        return rows.isEmpty() ? ApiResponse.nok("Não há registros") : ApiResponse.ok("Sucesso, consulta efetuada.", rows);
    }
    public ApiResponse create(FormDataRequest form, HttpServletRequest request) {
        var account = auth.role(request, "lojista");
        ShopData data = validate(form);
        Checks.require(!repository.shopExistsForAccount(account.get("id")), "Esta conta já possui uma loja cadastrada.");
        Checks.require(!repository.cnpjExists(data.cnpj()), "Este CNPJ já está cadastrado.");
        long id = repository.createShop(account.get("id"), data.name(), data.phone(), data.cnpj(), data.cep(), data.state(),
                data.city(), data.neighborhood(), data.street(), data.number());
        return ApiResponse.ok("Loja criada com sucesso.", Map.of("id", id));
    }
    public ApiResponse update(FormDataRequest form, HttpServletRequest request) {
        var account = auth.role(request, "lojista");
        ShopData data = validate(form);
        Checks.require(!repository.cnpjExistsForOtherAccount(data.cnpj(), account.get("id")),
                "Este CNPJ já está cadastrado.");
        Checks.require(repository.updateShop(data.name(), data.phone(), data.cnpj(), data.cep(), data.state(), data.city(),
                data.neighborhood(), data.street(), data.number(), account.get("id")) >= 0, "Não foi possível atualizar a loja.");
        return ApiResponse.ok("Loja atualizada com sucesso.");
    }
    private ShopData validate(FormDataRequest form) {
        String name = Checks.text(form.get("nome_loja")), cnpj = Checks.digits(form.get("cnpj"));
        Checks.require(!name.isEmpty() && !cnpj.isEmpty(), "Nome da loja e CNPJ são obrigatórios.");
        Checks.require(Checks.length(name, 3, 100), "Informe um nome de loja com 3 a 100 caracteres.");
        Checks.require(Checks.cnpj(cnpj), "CNPJ inválido.");
        String phone = Checks.digits(form.get("telefone")), cep = Checks.digits(form.get("cep"));
        String state = Checks.text(form.get("estado")).toUpperCase(), city = Checks.text(form.get("cidade"));
        Checks.require(phone.isEmpty() || phone.length() == 10 || phone.length() == 11, "Telefone deve ter DDD e 10 ou 11 dígitos.");
        Checks.require(cep.isEmpty() || cep.length() == 8, "CEP deve ter 8 dígitos.");
        Checks.require(state.isEmpty() || state.matches("[A-Z]{2}"), "Estado deve ser informado pela sigla com 2 letras.");
        Checks.require(city.isEmpty() || Checks.length(city, 2, 100), "Cidade deve ter entre 2 e 100 caracteres.");
        return new ShopData(name, cnpj, phone, cep, state, city, Checks.text(form.get("bairro")),
                Checks.text(form.get("logradouro")), Checks.text(form.get("numero")));
    }
}
