package br.com.pucpr.technoup;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

@SpringBootApplication
public class TechnoupApplication {

	public static void main(String[] args) {
		SpringApplication.run(TechnoupApplication.class, args);
		System.out.println("\n\n-> App is running! Access at http://localhost:8080/frontend/home.html\n\n");
	}

}
