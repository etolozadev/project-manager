<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidRut implements ValidationRule
{
    /**
     * Valida un RUT chileno.
     * Acepta formatos: 12345678-9 | 12.345.678-9 | 123456789
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rut = self::clean($value);

        if (! self::isValid($rut)) {
            $fail('El RUT ingresado no es válido.');
        }
    }

    /**
     * Limpia el RUT eliminando puntos y guiones.
     */
    public static function clean(string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', $rut));
    }

    /**
     * Valida el RUT usando algoritmo Módulo 11.
     * Recibe el RUT limpio (ej: "123456789" donde 9 es el dígito verificador).
     */
    public static function isValid(string $rut): bool
    {
        if (strlen($rut) < 2) {
            return false;
        }

        $body   = substr($rut, 0, -1);
        $dv     = strtoupper(substr($rut, -1));

        if (! is_numeric($body)) {
            return false;
        }

        return self::calculateDv((int) $body) === $dv;
    }

    /**
     * Calcula el dígito verificador para un cuerpo de RUT.
     */
    public static function calculateDv(int $body): string
    {
        $sum      = 0;
        $sequence = [2, 3, 4, 5, 6, 7];
        $index    = 0;

        while ($body > 0) {
            $sum  += ($body % 10) * $sequence[$index % 6];
            $body  = (int) ($body / 10);
            $index++;
        }

        $remainder = 11 - ($sum % 11);

        return match ($remainder) {
            11      => '0',
            10      => 'K',
            default => (string) $remainder,
        };
    }

    /**
     * Formatea un RUT limpio al formato chileno estándar: XX.XXX.XXX-Y
     */
    public static function format(string $rut): string
    {
        $rut  = self::clean($rut);
        $body = substr($rut, 0, -1);
        $dv   = substr($rut, -1);

        return number_format((int) $body, 0, ',', '.') . '-' . strtoupper($dv);
    }
}
