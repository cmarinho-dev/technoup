package br.com.pucpr.technoup.exception;



public class ApiException extends RuntimeException {
    public ApiException(String message) { super(message); }
}
