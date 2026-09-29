package br.com.pucpr.technoup.config;

import br.com.pucpr.technoup.service.implementation.MediaStorage;

import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.ResourceHandlerRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

@Configuration
public class StaticFiles implements WebMvcConfigurer {
    private final MediaStorage storage;
    public StaticFiles(MediaStorage storage) { this.storage = storage; }
    @Override public void addResourceHandlers(ResourceHandlerRegistry registry) {
        for (String path : new String[] {"frontend", "imagens", "videos"})
            registry.addResourceHandler("/" + path + "/**")
                    .addResourceLocations(storage.root().resolve(path).toUri().toString());
    }
}
