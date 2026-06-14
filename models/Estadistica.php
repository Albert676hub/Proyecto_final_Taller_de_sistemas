<?php
require_once __DIR__ . '/../config/conexion.php';

class Estadistica {
    private $conexion;

    public function __construct() {
        $instancia = new Conexion();
        $this->conexion = $instancia->obtenerConexion();
    }

    public function obtenerTiempos() {
        $consulta = "SELECT tiempo_segundos FROM estadisticas_desempeno";
        $sentencia = $this->conexion->query($consulta);
        return $sentencia->fetchAll(PDO::FETCH_COLUMN);
    }

    public function calcularDescriptiva($datos) {
        $n = count($datos);
        if ($n === 0) return null;

        // Media
        $media = array_sum($datos) / $n;

        // Mediana
        sort($datos);
        $mitad = floor(($n - 1) / 2);
        $mediana = ($datos[$mitad] + $datos[$mitad + 1 - $n % 2]) / 2;

        // Varianza y Desviación Estándar (Muestral)
        $sumaDiferencias = 0;
        foreach ($datos as $x) {
            $sumaDiferencias += pow($x - $media, 2);
        }
        $varianza = $sumaDiferencias / ($n - 1);
        $desviacion = sqrt($varianza);

        return [
            'n' => $n,
            'media' => round($media, 2),
            'mediana' => round($mediana, 2),
            'varianza' => round($varianza, 2),
            'desviacion' => round($desviacion, 2)
        ];
    }

    // Calcula el Intervalo de Confianza (95%) usando Z = 1.96 (para n >= 30)
    public function calcularIntervaloConfianza($media, $desviacion, $n) {
        $z = 1.96;
        $margen_error = $z * ($desviacion / sqrt($n));
        return [
            'limite_inferior' => round($media - $margen_error, 2),
            'limite_superior' => round($media + $margen_error, 2)
        ];
    }

    // Prueba de Hipótesis: H0 = El tiempo promedio es >= 120 segundos (2 minutos)
    public function pruebaHipotesis($media, $desviacion, $n, $mu_0 = 120) {
        // Estadístico de prueba Z
        $z_calc = ($media - $mu_0) / ($desviacion / sqrt($n));
        $z_critico = -1.645; // Nivel de significancia del 5% a una cola

        $decision = ($z_calc < $z_critico) ? "Se rechaza H0" : "No se rechaza H0";
        
        return [
            'z_calculado' => round($z_calc, 4),
            'z_critico' => $z_critico,
            'decision' => $decision
        ];
    }
}
?>