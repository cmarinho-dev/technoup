package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class EvaluationRepository {
    private static final String BASE_SQL = """
                SELECT a.*,c.nome AS consumidor_nome,c.email AS consumidor_email,ch.id AS chat_id
                FROM avaliacao_item a JOIN conta c ON c.id=a.consumidor_id
                LEFT JOIN chat_cotacao_item ch ON ch.avaliacao_id=a.id WHERE a.loja_id=?
                """;
    private final Database db;
    public EvaluationRepository(Database db) { this.db = db; }
    public boolean destinationShopExists(Object... args) {
        return db.exists("SELECT loja.id FROM loja JOIN conta ON conta.id=loja.conta_id WHERE loja.id=? AND conta.ativo=1", args);
    }
    public long createEvaluation(Object... args) {
        return db.insert("""
                INSERT INTO avaliacao_item (consumidor_id,loja_id,nome_item,categoria,estado,detalhes)
                VALUES (?,?,?,?,?,?)
                """, args);
    }
    public int addMedia(Object... args) {
        return db.update("""
                INSERT INTO avaliacao_item_midia (avaliacao_id,arquivo,caminho,tipo_arquivo) VALUES (?,?,?,?)
                """, args);
    }
    public List<Map<String, Object>> listMine(Object... args) {
        return db.rows("""
                SELECT a.*,l.nome_loja,l.banner_img,ch.id AS chat_id,ch.status AS status_chat,
                av.id AS atendimento_avaliacao_id,av.nota AS atendimento_nota,
                av.comentario AS atendimento_comentario,av.criado_em AS atendimento_avaliado_em
                FROM avaliacao_item a JOIN loja l ON l.id=a.loja_id
                LEFT JOIN chat_cotacao_item ch ON ch.avaliacao_id=a.id
                LEFT JOIN avaliacao_atendimento av ON av.avaliacao_id=a.id
                WHERE a.consumidor_id=? ORDER BY a.criado_em DESC
                """, args);
    }
    public Map<String, Object> findShopEvaluation(Object... args) {
        return db.row("SELECT id,consumidor_id,status FROM avaliacao_item WHERE id=? AND loja_id=?", args);
    }
    public int updateDecision(Object... args) {
        return db.update("UPDATE avaliacao_item SET status=?,respondido_em=NOW() WHERE id=? AND loja_id=?", args);
    }
    public long createChat(Object... args) {
        return db.insert("INSERT INTO chat_cotacao_item (consumidor_id,loja_id,avaliacao_id) VALUES (?,?,?)", args);
    }
    public int updateStage(Object... args) {
        return db.update("""
                UPDATE avaliacao_item SET status_avaliacao=? WHERE id=? AND loja_id=? AND status='aceita'
                """, args);
    }
    public Map<String, Object> findConsumerEvaluation(Object... args) {
        return db.row("""
                SELECT a.loja_id,a.status,a.status_avaliacao,ch.status AS status_chat
                FROM avaliacao_item a LEFT JOIN chat_cotacao_item ch ON ch.avaliacao_id=a.id
                WHERE a.id=? AND a.consumidor_id=?
                """, args);
    }
    public boolean ratingExists(Object... args) {
        return db.exists("SELECT id FROM avaliacao_atendimento WHERE avaliacao_id=?", args);
    }
    public long createRating(Object... args) {
        return db.insert("INSERT INTO avaliacao_atendimento (avaliacao_id,consumidor_id,loja_id,nota,comentario) VALUES (?,?,?,?,?)", args);
    }
    public List<Map<String, Object>> listMedia(Object... args) {
        return db.rows("SELECT id,avaliacao_id,arquivo,caminho,tipo_arquivo,criado_em FROM avaliacao_item_midia WHERE avaliacao_id=? ORDER BY id", args);
    }
    public List<Map<String, Object>> listByShopAndStatus(Object shopId, Object status) { return db.rows(BASE_SQL + " AND a.status=? ORDER BY a.criado_em DESC", shopId, status); }
    public List<Map<String, Object>> listByShop(Object shopId) { return db.rows(BASE_SQL + " ORDER BY FIELD(a.status,'pendente','aceita','recusada'),a.criado_em DESC", shopId); }
}
