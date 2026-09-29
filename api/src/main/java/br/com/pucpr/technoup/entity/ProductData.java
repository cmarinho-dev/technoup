package br.com.pucpr.technoup.entity;

import java.math.BigDecimal;
public record ProductData(String name, BigDecimal price, String type, String model,
                          String brand, String description, int discount) {}
