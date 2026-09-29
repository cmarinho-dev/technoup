package br.com.pucpr.technoup.mapper;

import java.util.LinkedHashMap;
import java.util.Map;

public final class ProductMapper {
    private ProductMapper() {}
    public static Map<String, Object> withImage(Map<String, Object> product, Map<String, Object> image) {
        var result = new LinkedHashMap<>(product);
        result.put("imagem", image);
        return result;
    }
}
