package br.com.pucpr.technoup.mapper;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public final class EvaluationMapper {
    private EvaluationMapper() {}
    public static Map<String, Object> withMedia(Map<String, Object> evaluation, List<Map<String, Object>> media) {
        var result = new LinkedHashMap<>(evaluation);
        result.put("midias", media);
        return result;
    }
}
