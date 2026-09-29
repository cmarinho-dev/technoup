package br.com.pucpr.technoup;

import br.com.pucpr.technoup.controller.AccountController;
import br.com.pucpr.technoup.exception.ApiErrors;
import br.com.pucpr.technoup.service.implementation.Auth;
import br.com.pucpr.technoup.service.implementation.AccountServiceImplementation;
import br.com.pucpr.technoup.util.Checks;
import br.com.pucpr.technoup.service.implementation.MediaStorage;
import br.com.pucpr.technoup.repository.AuthRepository;
import java.util.HashMap;
import java.util.Map;
import java.nio.file.Files;
import java.nio.file.Path;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.io.TempDir;
import org.springframework.mock.web.MockMultipartFile;
import org.springframework.mock.web.MockHttpSession;
import org.springframework.test.web.servlet.setup.MockMvcBuilders;

import static org.junit.jupiter.api.Assertions.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

class ApiContractTests {
    @TempDir Path temporary;

    @Test void legacyEnvelopeAndValidation() throws Exception {
        var auth = new Auth(null);
        var mvc = MockMvcBuilders.standaloneSetup(new AccountController(new AccountServiceImplementation(null, auth)))
                .setControllerAdvice(new ApiErrors()).build();
        mvc.perform(get("/api/autenticacao/sessao"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.status").value("nok"))
                .andExpect(jsonPath("$.data").isArray());
        mvc.perform(post("/api/autenticacao/entrar").param("email", "bad").param("senha", "x"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.mensagem").value("Email inválido."));
    }

    @Test void documentNumbersMatchLegacyRules() {
        assertTrue(Checks.cpf("529.982.247-25"));
        assertFalse(Checks.cpf("111.111.111-11"));
        assertTrue(Checks.cnpj("04.252.011/0001-10"));
        assertFalse(Checks.cnpj("00.000.000/0000-00"));
    }

    @Test void loginCreatesSessionWithoutExposingPassword() throws Exception {
        var accounts = new AuthRepository(null) {
            @Override public Map<String, Object> findByEmail(Object... args) {
                return new HashMap<>(Map.of("id", 42, "nome", "Cliente Teste", "email", "cliente@teste.com",
                        "senha", "secret123", "tipo", "consumidor", "ativo", 1));
            }
            @Override public Map<String, Object> findById(Object... args) {
                return Map.of("id", 42, "nome", "Cliente Teste", "email", "cliente@teste.com",
                        "tipo", "consumidor", "ativo", 1);
            }
            @Override public int upgradePassword(Object... args) { return 1; }
        };
        var auth = new Auth(accounts);
        var mvc = MockMvcBuilders.standaloneSetup(new AccountController(new AccountServiceImplementation(null, auth)))
                .setControllerAdvice(new ApiErrors()).build();
        var login = mvc.perform(post("/api/autenticacao/entrar")
                        .param("email", "cliente@teste.com").param("senha", "secret123"))
                .andExpect(jsonPath("$.status").value("ok"))
                .andExpect(jsonPath("$.data.usuario.senha").doesNotExist())
                .andReturn();
        var session = (MockHttpSession) login.getRequest().getSession(false);
        mvc.perform(get("/api/autenticacao/sessao").session(session))
                .andExpect(jsonPath("$.status").value("ok"))
                .andExpect(jsonPath("$.data.usuario.id").value(42));
        mvc.perform(post("/api/autenticacao/sair").session(session))
                .andExpect(jsonPath("$.status").value("ok"));
    }

    @Test void uploadUsesContentAndLegacyPath() {
        var storage = new MediaStorage(temporary.toString());
        byte[] png = {(byte) 0x89, 'P', 'N', 'G', 13, 10, 26, 10};
        var image = new MockMultipartFile("imagem", "ignored.html", "text/html", png);
        var saved = storage.image(image, "produtos");
        assertEquals("../imagens/produtos/", saved.get("caminho"));
        assertTrue(((String) saved.get("arquivo")).endsWith(".png"));
        assertTrue(Files.exists(temporary.resolve("imagens/produtos").resolve((String) saved.get("arquivo"))));
        var invalid = new MockMultipartFile("imagem", "fake.png", "image/png", "<html>".getBytes());
        assertThrows(RuntimeException.class, () -> storage.image(invalid, "produtos"));
        byte[] mp4 = {0, 0, 0, 32, 'f', 't', 'y', 'p', 'i', 's', 'o', 'm', 0, 0, 2, 0,
                'i', 's', 'o', 'm', 'i', 's', 'o', '2', 'a', 'v', 'c', '1', 'm', 'p', '4', '1'};
        var video = new MockMultipartFile("midias[]", "clip.mp4", "video/mp4", mp4);
        var savedVideo = storage.evaluation(new MockMultipartFile[]{video}).get(0);
        assertEquals("video", savedVideo.get("tipo_arquivo"));
        assertEquals("../videos/avaliacoes/", savedVideo.get("caminho"));
    }
}
