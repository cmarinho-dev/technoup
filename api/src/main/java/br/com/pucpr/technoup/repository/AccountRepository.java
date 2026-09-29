package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class AccountRepository {
    private final Database db;
    public AccountRepository(Database db) { this.db = db; }
    public boolean emailExists(Object... args) {
        return db.exists("SELECT id FROM conta WHERE email = ?", args);
    }
    public boolean cpfExists(Object... args) {
        return db.exists("SELECT id FROM conta WHERE cpf = ?", args);
    }
    public long createAccount(Object... args) {
        return db.insert("INSERT INTO conta (nome, cpf, email, senha, tipo) VALUES (?,?,?,?,?)", args);
    }
    public boolean contactExistsForOtherAccount(Object... args) {
        return db.exists("SELECT id FROM conta WHERE (email = ? OR cpf = ?) AND id <> ?", args);
    }
    public int updateProfile(Object... args) {
        return db.update("UPDATE conta SET nome=?,cpf=?,email=? WHERE id=?", args);
    }
    public int updateProfileWithPassword(Object... args) {
        return db.update("UPDATE conta SET nome=?,cpf=?,email=?,senha=? WHERE id=?", args);
    }
    public List<Map<String, Object>> listAccounts(Object... args) {
        return db.rows("SELECT id,nome,cpf,email,tipo,ativo,criado_em FROM conta ORDER BY tipo DESC");
    }
    public List<Map<String, Object>> listShopAccounts(Object... args) {
        return db.rows("""
                SELECT conta.id AS conta_id,conta.nome AS nome_conta,conta.email,conta.ativo,conta.criado_em,
                loja.nome_loja,loja.telefone,loja.cnpj,loja.cidade,loja.estado,loja.logradouro,loja.numero
                FROM conta LEFT JOIN loja ON loja.conta_id=conta.id WHERE conta.tipo='lojista' ORDER BY conta.criado_em DESC
                """);
    }
    public Map<String, Object> findAccountStatus(Object... args) {
        return db.row("SELECT ativo FROM conta WHERE id=?", args);
    }
    public int setAccountStatus(Object... args) {
        return db.update("UPDATE conta SET ativo=? WHERE id=?", args);
    }
    public int deleteAccount(Object... args) {
        return db.update("DELETE FROM conta WHERE id=?", args);
    }
}
