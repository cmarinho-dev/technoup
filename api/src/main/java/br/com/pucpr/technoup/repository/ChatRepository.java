package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class ChatRepository {
    private final Database db;
    public ChatRepository(Database db) { this.db = db; }
    public List<Map<String, Object>> listMessages(Object... args) {
        return db.rows("SELECT id,is_cliente,chat_id,mensagem,criado_em FROM mensagem_cotacao_item WHERE chat_id=? AND id>? ORDER BY id ASC", args);
    }
    public long createMessage(Object... args) {
        return db.insert("INSERT INTO mensagem_cotacao_item (is_cliente,chat_id,mensagem) VALUES (?,?,?)", args);
    }
    public int touchChat(Object... args) {
        return db.update("UPDATE chat_cotacao_item SET atualizado_em=NOW() WHERE id=?", args);
    }
    public Map<String, Object> findMessage(Object... args) {
        return db.row("SELECT id,is_cliente,chat_id,mensagem,criado_em FROM mensagem_cotacao_item WHERE id=?", args);
    }
    public int closeChat(Object... args) {
        return db.update("UPDATE chat_cotacao_item SET status='fechado',fechado_em=NOW() WHERE id=? AND loja_id=?", args);
    }
    public int markConsumerRead(Object... args) {
        return db.update("UPDATE chat_cotacao_item SET lido_consumidor_em=NOW() WHERE id=? AND consumidor_id=?", args);
    }
    public int markShopRead(Object... args) {
        return db.update("UPDATE chat_cotacao_item SET lido_lojista_em=NOW() WHERE id=? AND loja_id=?", args);
    }
    public int markAdminRead(Object... args) {
        return db.update("UPDATE chat_cotacao_item SET lido_consumidor_em=NOW(),lido_lojista_em=NOW() WHERE id=?", args);
    }
    public List<Map<String, Object>> listChats(String role, Object... args) {
        return db.rows("""
                SELECT ch.id AS chat_id,ch.status AS chat_status,ch.consumidor_id,ch.avaliacao_id,
                ch.atualizado_em,ch.lido_consumidor_em,ch.lido_lojista_em,a.nome_item,a.categoria,a.estado,
                l.nome_loja,l.banner_img,c.nome AS consumidor_nome,
                ultima.mensagem AS ultima_mensagem,ultima.criado_em AS ultima_mensagem_em
                FROM chat_cotacao_item ch JOIN avaliacao_item a ON a.id=ch.avaliacao_id
                JOIN loja l ON l.id=ch.loja_id JOIN conta c ON c.id=ch.consumidor_id
                LEFT JOIN mensagem_cotacao_item ultima ON ultima.id=(
                    SELECT m.id FROM mensagem_cotacao_item m WHERE m.chat_id=ch.id ORDER BY m.id DESC LIMIT 1)
                WHERE
                """ + scope(role) + " ORDER BY ch.atualizado_em DESC,ch.id DESC", args);
    }
    public Map<String, Object> findAllowedChat(String filter, String role, Object... args) {
        String condition = switch (filter) {
            case "chat" -> "ch.id=?";
            case "evaluation" -> "a.id=?";
            case "accepted" -> "a.status='aceita'";
            default -> throw new IllegalArgumentException("Filtro de chat inválido.");
        };
        return db.row("""
                SELECT ch.id,ch.consumidor_id,ch.loja_id,ch.avaliacao_id,ch.status AS chat_status,ch.criado_em,
                l.nome_loja,l.banner_img,a.nome_item,a.categoria,a.estado,a.detalhes,a.status
                FROM chat_cotacao_item ch JOIN avaliacao_item a ON a.id=ch.avaliacao_id
                JOIN loja l ON l.id=ch.loja_id WHERE
                """ + condition + " AND " + scope(role) + " ORDER BY ch.id DESC LIMIT 1", args);
    }
    private String scope(String role) {
        return switch (role) {
            case "consumidor" -> "ch.consumidor_id=?";
            case "lojista" -> "ch.loja_id=?";
            case "administrador" -> "1=1";
            default -> throw new IllegalArgumentException("Perfil de chat inválido.");
        };
    }
}
