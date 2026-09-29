package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class ProductRepository {
    private static final String BASE_SQL = """
                SELECT produto.*,loja.nome_loja,loja.conta_id AS loja_conta_id,
                COALESCE(notas.media_atendimento,0) AS media_atendimento,
                COALESCE(notas.total_avaliacoes_atendimento,0) AS total_avaliacoes_atendimento
                FROM produto JOIN loja ON produto.loja_id=loja.id JOIN conta ON loja.conta_id=conta.id
                LEFT JOIN (SELECT loja_id,ROUND(AVG(nota),1) AS media_atendimento,
                COUNT(id) AS total_avaliacoes_atendimento FROM avaliacao_atendimento GROUP BY loja_id)
                notas ON notas.loja_id=loja.id WHERE conta.tipo='lojista' AND conta.ativo=1
                """;
    private final Database db;
    public ProductRepository(Database db) { this.db = db; }
    public Map<String, Object> findImage(Object... args) {
        return db.row("SELECT * FROM imagem_produto WHERE produto_id=? LIMIT 1", args);
    }
    public long createProduct(Object... args) {
        return db.insert("""
                INSERT INTO produto (loja_id,nome,preco,tipo,modelo,marca,descricao,desconto)
                VALUES (?,?,?,?,?,?,?,?)
                """, args);
    }
    public boolean ownedProductExists(Object... args) {
        return db.exists("SELECT id FROM produto WHERE id=? AND loja_id=?", args);
    }
    public int updateProduct(Object... args) {
        return db.update("""
                UPDATE produto SET nome=?,preco=?,tipo=?,modelo=?,marca=?,descricao=?,desconto=? WHERE id=? AND loja_id=?
                """, args);
    }
    public int deleteProduct(Object... args) {
        return db.update("DELETE FROM produto WHERE id=? AND loja_id=?", args);
    }
    public int upsertImage(Object... args) {
        return db.update("""
                INSERT INTO imagem_produto (produto_id,arquivo,caminho,descricao) VALUES (?,?,?,'imagem do produto')
                ON DUPLICATE KEY UPDATE arquivo=VALUES(arquivo),caminho=VALUES(caminho),descricao=VALUES(descricao)
                """, args);
    }
    public List<Map<String, Object>> listById(Object id) { return db.rows(BASE_SQL + " AND produto.id=?", id); }
    public List<Map<String, Object>> listByShop(Object id) { return db.rows(BASE_SQL + " AND produto.loja_id=? ORDER BY produto.criado_em DESC", id); }
    public List<Map<String, Object>> listActive() { return db.rows(BASE_SQL + " ORDER BY produto.desconto DESC,produto.criado_em DESC"); }
}
