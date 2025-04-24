<?php
/**
 * Clase abstracta base para los controladores.
 */
abstract class BaseController {
    private static $filePath = __DIR__ . '/../config/disponibilidad.txt';

    public static function setDisponibilidad(bool $estado) {
        file_put_contents(self::$filePath, $estado ? 'true' : 'false', LOCK_EX);
    }

    public static function getDisponibilidad(): bool {
        if (!file_exists(self::$filePath)) return true; // Valor por defecto
        return trim(file_get_contents(self::$filePath)) === 'true';
    }
}
