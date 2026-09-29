package br.com.pucpr.technoup.util;

import br.com.pucpr.technoup.exception.ApiException;

import java.util.regex.Pattern;

public final class Checks {
    private Checks() {}
    public static void require(boolean condition, String message) { if (!condition) throw new ApiException(message); }
    public static String text(String value) { return value == null ? "" : value.trim(); }
    public static String digits(String value) { return text(value).replaceAll("\\D", ""); }
    public static int number(String value) { try { return Integer.parseInt(text(value)); } catch (Exception e) { return 0; } }
    public static int databaseInt(Object value) {
        if (value instanceof Boolean booleanValue) return booleanValue ? 1 : 0;
        if (value instanceof Number numberValue) return numberValue.intValue();
        return number(String.valueOf(value));
    }
    public static boolean length(String value, int min, int max) { int size = value.codePointCount(0, value.length()); return size >= min && size <= max; }
    public static boolean email(String value) { return Pattern.matches("^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$", value); }
    public static boolean cpf(String input) {
        String value = digits(input);
        if (value.length() != 11 || value.chars().distinct().count() == 1) return false;
        for (int position = 9; position < 11; position++) {
            int sum = 0;
            for (int i = 0; i < position; i++) sum += (value.charAt(i) - '0') * (position + 1 - i);
            if (value.charAt(position) - '0' != ((10 * sum) % 11) % 10) return false;
        }
        return true;
    }
    public static boolean cnpj(String input) {
        String value = digits(input);
        if (value.length() != 14 || value.chars().distinct().count() == 1) return false;
        int[][] weights = {{5,4,3,2,9,8,7,6,5,4,3,2}, {6,5,4,3,2,9,8,7,6,5,4,3,2}};
        for (int position = 12; position < 14; position++) {
            int sum = 0;
            for (int i = 0; i < position; i++) sum += (value.charAt(i) - '0') * weights[position - 12][i];
            int digit = sum % 11 < 2 ? 0 : 11 - sum % 11;
            if (value.charAt(position) - '0' != digit) return false;
        }
        return true;
    }
}
