<?php
require_once __DIR__ . '/../config/conexion.php';

class Estadistica {
    private $conexion;

    public function __construct() {
        $db = new Conexion();
        $this->conexion = $db->obtenerConexion();
    }

    /**
     * Extrae los tiempos de procesamiento de la base de datos.
     */
    public function obtenerTiempos() {
        $tiempos = [];
        try {
            $sql = "SELECT tiempo_segundos FROM estadisticas_desempeno";
            $stmt = $this->conexion->query($sql);
            
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tiempos[] = floatval($fila['tiempo_segundos']);
            }
        } catch (PDOException $e) {
            die("Error en la extracción de métricas: " . $e->getMessage());
        }
        return $tiempos;
    }

    /**
     * Calcula las medidas descriptivas (Punto 6).
     */
    public function calcularDescriptiva($datos) {
        $n = count($datos);
        
        if ($n < 2) return false; // Se requieren al menos 2 datos para inferencia

        // 1. Media
        $media = array_sum($datos) / $n;

        // 2. Mediana
        sort($datos);
        $mitad = floor(($n - 1) / 2);
        $mediana = ($n % 2) ? $datos[$mitad] : ($datos[$mitad] + $datos[$mitad + 1]) / 2.0;

        // 3. Moda (Optimizada para evitar colisiones de precisión de coma flotante)
        $datos_redondeados = array_map(function($val) { return (string)round($val, 1); }, $datos);
        $frecuencias = array_count_values($datos_redondeados);
        $max_frecuencia = max($frecuencias);
        
        if ($max_frecuencia == 1) {
            $moda = "Amodal";
        } else {
            $modas_array = array_keys($frecuencias, $max_frecuencia);
            $moda = implode(", ", $modas_array); 
        }

        // 4. Varianza Muestral
        $suma_dif_cuadrado = 0;
        foreach ($datos as $valor) {
            $suma_dif_cuadrado += pow($valor - $media, 2);
        }
        $varianza = $suma_dif_cuadrado / ($n - 1);

        // 5. Desviación Estándar Muestral (S)
        $desviacion = sqrt($varianza);

        return [
            'n' => $n,
            'media' => round($media, 2),
            'mediana' => round($mediana, 2),
            'moda' => $moda,
            'varianza' => round($varianza, 2),
            'desviacion' => round($desviacion, 2)
        ];
    }

    /**
     * Calcula el Intervalo de Confianza al 95% (Punto 8).
     */
    public function calcularIntervaloConfianza($media, $desviacion, $n) {
        // Para 95% de confianza Z = 1.96
        // Nota: Para ser 100% puristas, si n<30 aquí también iría t_student, 
        // pero la convención estándar permite usar Z o t dependiendo del rigor. 
        // Aplicaremos Z para mantener la fórmula general solicitada.
        $z = 1.96; 
        $error_estandar = $desviacion / sqrt($n);
        $margen_error = $z * $error_estandar;

        return [
            'limite_inferior' => round($media - $margen_error, 2),
            'limite_superior' => round($media + $margen_error, 2)
        ];
    }

    /**
     * Motor de Prueba de Hipótesis Dinámico (Puntos 7 y 9).
     * Decide automáticamente entre Z y t de Student basado en n.
     */
    public function pruebaHipotesis($media_muestral, $desviacion, $n) {
        $mu_objetivo = 120; // H0: mu >= 120
        $error_estandar = $desviacion / sqrt($n);
        $estadistico_calculado = ($media_muestral - $mu_objetivo) / $error_estandar;
        
        // Asignación dinámica de la distribución según el Teorema del Límite Central
        if ($n >= 30) {
            // Distribución Normal (Z) - Cola inferior, alfa = 0.05
            $valor_critico = -1.645;
        } else {
            // Distribución t de Student (Grados de libertad = n - 1)
            $grados_libertad = $n - 1;
            // Tabla t de Student para alfa = 0.05 (1 cola) del gl 1 al 29
            $tabla_t = [
                1 => -6.314, 2 => -2.920, 3 => -2.353, 4 => -2.132, 5 => -2.015,
                6 => -1.943, 7 => -1.895, 8 => -1.860, 9 => -1.833, 10 => -1.812,
                11 => -1.796, 12 => -1.782, 13 => -1.771, 14 => -1.761, 15 => -1.753,
                16 => -1.746, 17 => -1.740, 18 => -1.734, 19 => -1.729, 20 => -1.725,
                21 => -1.721, 22 => -1.717, 23 => -1.714, 24 => -1.711, 25 => -1.708,
                26 => -1.706, 27 => -1.703, 28 => -1.701, 29 => -1.699
            ];
            $valor_critico = $tabla_t[$grados_libertad];
        }
        
        // Decisión estadística
        if ($estadistico_calculado < $valor_critico) {
            $decision = "Se rechaza H0";
        } else {
            $decision = "No se rechaza H0";
        }

        return [
            // Mantenemos la llave 'z_calculado' para no romper la vista del frontend
            'z_calculado' => round($estadistico_calculado, 3), 
            'valor_critico' => $valor_critico,
            'decision' => $decision
        ];
    }
}
?>