package br.com.pucpr.technoup.entity;

public record ShopData(String name, String cnpj, String phone, String cep, String state,
                       String city, String neighborhood, String street, String number) {}
