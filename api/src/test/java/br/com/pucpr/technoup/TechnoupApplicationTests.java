package br.com.pucpr.technoup;

import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.webmvc.test.autoconfigure.AutoConfigureMockMvc;
import org.springframework.test.web.servlet.MockMvc;

import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@SpringBootTest
@AutoConfigureMockMvc
class TechnoupApplicationTests {
	@Autowired MockMvc mvc;

	@Test
	void frontendAndApiRoutesAreServedTogether() throws Exception {
		mvc.perform(get("/frontend/login.html")).andExpect(status().isOk());
		mvc.perform(get("/imagens/produtos/webcam_fullhd.jpg")).andExpect(status().isOk());
		mvc.perform(get("/api/autenticacao/sessao"))
				.andExpect(status().isOk())
				.andExpect(jsonPath("$.status").value("nok"));
	}

	@Test
	void protectedRoutesRejectAnonymousRequests() throws Exception {
		for (String route : new String[] {"/api/contas", "/api/chats", "/api/denuncias/alvos"})
			mvc.perform(get(route)).andExpect(status().isOk()).andExpect(jsonPath("$.status").value("nok"));
		for (String route : new String[] {"/api/lojas", "/api/produtos/excluir", "/api/avaliacoes/status"})
			mvc.perform(post(route)).andExpect(status().isOk()).andExpect(jsonPath("$.status").value("nok"));
	}

}
