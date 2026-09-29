package br.com.pucpr.technoup.dto.response;



import java.util.List;

public record ApiResponse(String status, String mensagem, Object data) {
    public static ApiResponse ok(String message, Object data) { return new ApiResponse("ok", message, data); }
    public static ApiResponse ok(String message) { return ok(message, List.of()); }
    public static ApiResponse nok(String message) { return new ApiResponse("nok", message, List.of()); }
}
