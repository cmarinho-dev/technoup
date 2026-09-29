package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class ReportRepository {
    private static final String BASE_SQL = """
                SELECT d.id,d.motivo,d.status,d.criado_em,d.atualizado_em,
                reporter.id AS denunciante_id,reporter.nome AS denunciante_nome,
                reporter.email AS denunciante_email,reporter.tipo AS denunciante_tipo,
                target.id AS denunciado_id,target.nome AS denunciado_nome,
                target.email AS denunciado_email,target.tipo AS denunciado_tipo,
                l.nome_loja AS denunciado_nome_loja
                FROM denuncia_conta d JOIN conta reporter ON reporter.id=d.denunciante_id
                JOIN conta target ON target.id=d.denunciado_id LEFT JOIN loja l ON l.conta_id=target.id
                """;
    private final Database db;
    public ReportRepository(Database db) { this.db = db; }
    public List<Map<String, Object>> listTargets(Object... args) {
        return db.rows("""
                SELECT c.id,c.nome,c.email,c.tipo,l.nome_loja FROM conta c
                LEFT JOIN loja l ON l.conta_id=c.id
                WHERE c.id<>? AND c.tipo=? AND c.ativo=1
                ORDER BY c.tipo DESC,COALESCE(l.nome_loja,c.nome)
                """, args);
    }
    public boolean targetExists(Object... args) {
        return db.exists("SELECT id FROM conta WHERE id=? AND tipo=? AND ativo=1", args);
    }
    public long createReport(Object... args) {
        return db.insert("INSERT INTO denuncia_conta (denunciante_id,denunciado_id,motivo) VALUES (?,?,?)", args);
    }
    public boolean reportExists(Object... args) {
        return db.exists("SELECT id FROM denuncia_conta WHERE id=?", args);
    }
    public int updateStatus(Object... args) {
        return db.update("UPDATE denuncia_conta SET status=? WHERE id=?", args);
    }
    public List<Map<String, Object>> listByStatus(Object status) { return db.rows(BASE_SQL + " WHERE d.status=? ORDER BY d.criado_em DESC", status); }
    public List<Map<String, Object>> listAll() { return db.rows(BASE_SQL + " ORDER BY d.criado_em DESC"); }
}
