package br.com.pucpr.technoup.exception;

import br.com.pucpr.technoup.dto.response.ApiResponse;

import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.dao.DataAccessException;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.RestControllerAdvice;
import org.springframework.web.multipart.MaxUploadSizeExceededException;

@RestControllerAdvice
public class ApiErrors {
    @ExceptionHandler(ApiException.class)
    public ApiResponse validation(ApiException error) { return ApiResponse.nok(error.getMessage()); }

    @ExceptionHandler(DataIntegrityViolationException.class)
    public ApiResponse conflict(DataIntegrityViolationException error) {
        return ApiResponse.nok("Dados duplicados ou vinculados a outro registro.");
    }

    @ExceptionHandler(DataAccessException.class)
    public ApiResponse database(DataAccessException error) {
        return ApiResponse.nok("Não foi possível acessar o banco de dados.");
    }

    @ExceptionHandler(MaxUploadSizeExceededException.class)
    public ResponseEntity<ApiResponse> large(MaxUploadSizeExceededException error) {
        return ResponseEntity.status(HttpStatus.PAYLOAD_TOO_LARGE).body(ApiResponse.nok("Arquivo muito grande."));
    }
}
