package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.repository.ProductRepository;

import br.com.pucpr.technoup.dto.request.FormDataRequest;
import br.com.pucpr.technoup.entity.ProductData;
import br.com.pucpr.technoup.mapper.ProductMapper;
import br.com.pucpr.technoup.dto.response.ApiResponse;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;
import org.springframework.stereotype.Service;

import jakarta.servlet.http.HttpServletRequest;
import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.Map;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.multipart.MultipartFile;

@Service
public class ProductServiceImplementation {
    private final ProductRepository repository;
    private final Auth auth;
    private final MediaStorage media;
    public ProductServiceImplementation(ProductRepository repository, Auth auth, MediaStorage media) {
        this.repository = repository; this.auth = auth; this.media = media;
    }
    public ApiResponse list(FormDataRequest query) {
        var products = query.containsKey("id") && !query.get("id").isBlank()
                ? repository.listById(Checks.number(query.get("id")))
                : query.containsKey("loja_id") && !query.get("loja_id").isBlank()
                ? repository.listByShop(Checks.number(query.get("loja_id")))
                : repository.listActive();
        var result = new ArrayList<Map<String, Object>>();
        for (var product : products) {
            result.add(ProductMapper.withImage(product, repository.findImage(product.get("id"))));
        }
        return ApiResponse.ok("", result);
    }
    @Transactional
    public ApiResponse create(FormDataRequest form,
                              MultipartFile image,
                              HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        ProductData data = validate(form, false);
        long id = repository.createProduct(shopId, data.name(), data.price(), data.type(), data.model(), data.brand(), data.description(), data.discount());
        var saved = registerImage(id, image);
        Map<String, Object> result = new LinkedHashMap<>();
        result.put("id", id); result.put("imagem", saved);
        return ApiResponse.ok("Produto criado com sucesso.", result);
    }
    @Transactional
    public ApiResponse update(FormDataRequest form,
                              MultipartFile image,
                              HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        ProductData data = validate(form, true);
        int id = Checks.number(form.get("id"));
        Checks.require(repository.ownedProductExists(id, shopId),
                "Produto não encontrado ou sem permissão.");
        repository.updateProduct(data.name(), data.price(), data.type(), data.model(), data.brand(), data.description(), data.discount(), id, shopId);
        return ApiResponse.ok("Produto atualizado com sucesso.", java.util.Collections.singletonMap("imagem", registerImage(id, image)));
    }
    public ApiResponse delete(FormDataRequest form, HttpServletRequest request) {
        int shopId = auth.shopId(auth.role(request, "lojista"));
        int id = Checks.number(form.get("id"));
        Checks.require(id > 0, "ID do produto é obrigatório.");
        Checks.require(repository.deleteProduct(id, shopId) > 0,
                "Produto não encontrado ou sem permissão para deletar.");
        return ApiResponse.ok("Produto deletado com sucesso.");
    }
    private ProductData validate(FormDataRequest form, boolean update) {
        String name = Checks.text(form.get("nome"));
        BigDecimal price;
        try { price = new BigDecimal(Checks.text(form.get("preco"))); }
        catch (Exception ignored) { price = BigDecimal.ZERO; }
        Checks.require(!update || Checks.number(form.get("id")) > 0, "ID, nome e preço são obrigatórios.");
        Checks.require(!name.isEmpty() && price.signum() > 0, update ? "ID, nome e preço são obrigatórios." : "Nome e preço são obrigatórios.");
        int discount = Checks.number(form.get("desconto"));
        Checks.require(discount >= 0 && discount <= 100, "Desconto deve estar entre 0 e 100.");
        return new ProductData(name, price, Checks.text(form.get("tipo")), Checks.text(form.get("modelo")),
                Checks.text(form.get("marca")), Checks.text(form.get("descricao")), discount);
    }
    private Map<String, Object> registerImage(long id, MultipartFile image) {
        var saved = media.image(image, "produtos");
        if (saved != null) repository.upsertImage(id, saved.get("arquivo"), saved.get("caminho"));
        return saved;
    }
}
