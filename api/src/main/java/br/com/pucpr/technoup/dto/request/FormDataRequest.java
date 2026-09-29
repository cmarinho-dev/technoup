package br.com.pucpr.technoup.dto.request;

import java.util.Map;

/** Preserves the existing form field names while giving services a request DTO. */
public record FormDataRequest(Map<String, String> values) {
    public FormDataRequest { values = Map.copyOf(values); }
    public String get(String name) { return values.get(name); }
    public String getOrDefault(String name, String fallback) { return values.getOrDefault(name, fallback); }
    public boolean containsKey(String name) { return values.containsKey(name); }
}
