package br.com.pucpr.technoup.repository;

import java.util.List;
import java.util.Map;
import org.springframework.stereotype.Repository;

@Repository
public class AuthRepository {
    private final Database db;
    public AuthRepository(Database db) { this.db = db; }
    public Map<String, Object> findByEmail(Object... args) {
        return db.row("SELECT * FROM conta WHERE email = ?", args);
    }
    public int upgradePassword(Object... args) {
        return db.update("UPDATE conta SET senha = ? WHERE id = ?", args);
    }
    public Map<String, Object> findById(Object... args) {
        return db.row("SELECT id,nome,cpf,email,tipo,ativo,criado_em FROM conta WHERE id = ?", args);
    }
    public Map<String, Object> findShopForAccount(Object... args) {
        return db.row("SELECT * FROM loja WHERE conta_id = ?", args);
    }
}
