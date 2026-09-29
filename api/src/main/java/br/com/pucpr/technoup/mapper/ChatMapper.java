package br.com.pucpr.technoup.mapper;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public final class ChatMapper {
    private ChatMapper() {}

    public static Map<String, Object> detail(Map<String, Object> chat, Map<String, Object> account,
                                             List<Map<String, Object>> messages) {
        Map<String, Object> data = new LinkedHashMap<>();
        var chatData = pick(chat, "id", "consumidor_id", "loja_id", "avaliacao_id", "chat_status", "criado_em");
        chatData.put("status", chatData.remove("chat_status"));
        data.put("chat", chatData);
        data.put("loja", Map.of("id", chat.get("loja_id"), "nome_loja", chat.get("nome_loja"),
                "banner_img", chat.get("banner_img") == null ? "" : chat.get("banner_img")));
        var evaluation = pick(chat, "avaliacao_id", "nome_item", "categoria", "estado", "detalhes", "status");
        evaluation.put("id", evaluation.remove("avaliacao_id"));
        data.put("avaliacao", evaluation);
        data.put("usuario", Map.of("id", account.get("id"), "nome", account.get("nome"), "tipo", account.get("tipo")));
        data.put("mensagens", messages);
        return data;
    }

    private static Map<String, Object> pick(Map<String, Object> source, String... keys) {
        var result = new LinkedHashMap<String, Object>();
        for (String key : keys) result.put(key, source.get(key));
        return result;
    }
}
