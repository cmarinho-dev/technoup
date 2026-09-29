package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class ShopRepository {
    private static final String BASE_SQL = """
                SELECT loja.*,COALESCE(notas.media_atendimento,0) AS media_atendimento,
                COALESCE(notas.total_avaliacoes_atendimento,0) AS total_avaliacoes_atendimento
                FROM loja LEFT JOIN (SELECT loja_id,ROUND(AVG(nota),1) AS media_atendimento,
                COUNT(id) AS total_avaliacoes_atendimento FROM avaliacao_atendimento GROUP BY loja_id)
                notas ON notas.loja_id=loja.id
                """;
    private final Database db;
    public ShopRepository(Database db) { this.db = db; }
    public boolean shopExistsForAccount(Object... args) {
        return db.exists("SELECT id FROM loja WHERE conta_id=?", args);
    }
    public boolean cnpjExists(Object... args) {
        return db.exists("SELECT id FROM loja WHERE cnpj=?", args);
    }
    public long createShop(Object... args) {
        return db.insert("""
                INSERT INTO loja (conta_id,nome_loja,telefone,cnpj,cep,estado,cidade,bairro,logradouro,numero)
                VALUES (?,?,?,?,?,?,?,?,?,?)
                """, args);
    }
    public boolean cnpjExistsForOtherAccount(Object... args) {
        return db.exists("SELECT id FROM loja WHERE cnpj=? AND conta_id<>?", args);
    }
    public int updateShop(Object... args) {
        return db.update("""
                UPDATE loja SET nome_loja=?,telefone=?,cnpj=?,cep=?,estado=?,cidade=?,bairro=?,logradouro=?,numero=?
                WHERE conta_id=?
                """, args);
    }
    public List<Map<String, Object>> listByAccount(Object id) { return db.rows(BASE_SQL + " WHERE loja.conta_id=?", id); }
    public List<Map<String, Object>> listById(Object id) { return db.rows(BASE_SQL + " WHERE loja.id=?", id); }
    public List<Map<String, Object>> listActive() { return db.rows(BASE_SQL + " JOIN conta ON loja.conta_id=conta.id WHERE conta.tipo='lojista' AND conta.ativo=1 ORDER BY nome_loja"); }
}
