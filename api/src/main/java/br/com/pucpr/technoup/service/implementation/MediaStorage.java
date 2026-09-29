package br.com.pucpr.technoup.service.implementation;

import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.exception.ApiException;

import java.io.IOException;
import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardCopyOption;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import org.apache.tika.Tika;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.transaction.support.TransactionSynchronization;
import org.springframework.transaction.support.TransactionSynchronizationManager;
import org.springframework.web.multipart.MultipartFile;

@Service
public class MediaStorage {
    private final Tika tika = new Tika();
    private final Path root;
    public MediaStorage(@Value("${technoup.workspace}") String workspace) {
        root = Path.of(workspace).toAbsolutePath().normalize();
    }
    public Path root() { return root; }

    public Map<String, Object> image(MultipartFile file, String folder) {
        return file == null || file.isEmpty() ? null : save(file, "imagens/" + folder, true);
    }
    public List<Map<String, Object>> evaluation(MultipartFile[] files) {
        if (files == null) return List.of();
        var present = new ArrayList<MultipartFile>();
        for (var file : files) if (file != null && !file.isEmpty()) present.add(file);
        Checks.require(present.size() <= 8, "Envie no máximo 8 arquivos.");
        var result = new ArrayList<Map<String, Object>>();
        for (var file : present) {
            boolean image = detectedType(file).startsWith("image/");
            var saved = new java.util.LinkedHashMap<>(save(file, image ? "imagens/avaliacoes" : "videos/avaliacoes", image));
            saved.put("tipo_arquivo", image ? "imagem" : "video");
            result.add(saved);
        }
        return result;
    }
    private Map<String, Object> save(MultipartFile file, String folder, boolean image) {
        String type = detectedType(file);
        String extension = switch (type) {
            case "image/jpeg" -> "jpg";
            case "image/png" -> "png";
            case "image/webp" -> "webp";
            case "video/mp4" -> "mp4";
            case "video/webm" -> "webm";
            case "video/quicktime" -> "mov";
            default -> throw new ApiException("Tipo de arquivo não permitido.");
        };
        Checks.require(type.startsWith(image ? "image/" : "video/"), "Envie imagens ou vídeos válidos.");
        int limit = image ? 5 : 20;
        Checks.require(file.getSize() <= limit * 1024L * 1024L, "Arquivo muito grande. O limite é " + limit + "MB.");
        String name = UUID.randomUUID() + "." + extension;
        Path directory = root.resolve(folder).normalize();
        Checks.require(directory.startsWith(root), "Destino de upload inválido.");
        Path target = directory.resolve(name);
        try {
            Files.createDirectories(directory);
            try (InputStream input = file.getInputStream()) { Files.copy(input, target, StandardCopyOption.REPLACE_EXISTING); }
        } catch (IOException e) { throw new ApiException("Não foi possível salvar o arquivo enviado."); }
        if (TransactionSynchronizationManager.isSynchronizationActive()) {
            TransactionSynchronizationManager.registerSynchronization(new TransactionSynchronization() {
                @Override public void afterCompletion(int status) {
                    if (status != STATUS_COMMITTED) try { Files.deleteIfExists(target); } catch (IOException ignored) { }
                }
            });
        }
        return Map.of("arquivo", name, "caminho", "../" + folder + "/", "tipo", type);
    }
    private String detectedType(MultipartFile file) {
        try (InputStream input = file.getInputStream()) {
            return tika.detect(input);
        } catch (IOException e) { throw new ApiException("Erro no upload do arquivo."); }
    }
}
