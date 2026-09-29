package br.com.pucpr.technoup.repository;



import java.sql.PreparedStatement;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.PreparedStatementCreator;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.stereotype.Repository;

@Repository
public class Database {
    private static final DateTimeFormatter LEGACY_DATE = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
    private final JdbcTemplate jdbc;
    public Database(JdbcTemplate jdbc) { this.jdbc = jdbc; }

    public List<Map<String, Object>> rows(String sql, Object... args) {
        var result = new ArrayList<Map<String, Object>>();
        for (var row : jdbc.queryForList(sql, args)) {
            var normalized = new LinkedHashMap<String, Object>();
            row.forEach((key, value) -> normalized.put(key, value instanceof Boolean booleanValue ?
                    (booleanValue ? 1 : 0) : value instanceof Timestamp timestamp ?
                    timestamp.toLocalDateTime().format(LEGACY_DATE) : value));
            result.add(normalized);
        }
        return result;
    }
    public Map<String, Object> row(String sql, Object... args) {
        var rows = rows(sql, args);
        return rows.isEmpty() ? null : rows.get(0);
    }
    public boolean exists(String sql, Object... args) { return row(sql, args) != null; }
    public int update(String sql, Object... args) { return jdbc.update(sql, args); }
    public long insert(String sql, Object... args) {
        var key = new GeneratedKeyHolder();
        PreparedStatementCreator statement = connection -> {
            PreparedStatement prepared = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            for (int i = 0; i < args.length; i++) prepared.setObject(i + 1, args[i]);
            return prepared;
        };
        jdbc.update(statement, key);
        return key.getKey().longValue();
    }
}
