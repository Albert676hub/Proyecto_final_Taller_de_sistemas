<?php
require_once '../../models/Estadistica.php';
include '../layout/header.php';

$est = new Estadistica();
$datos = $est->obtenerTiempos();
$stats = $est->calcularDescriptiva($datos);

if($stats && $stats['n'] >= 30) {
    $intervalo = $est->calcularIntervaloConfianza($stats['media'], $stats['desviacion'], $stats['n']);
    $prueba = $est->pruebaHipotesis($stats['media'], $stats['desviacion'], $stats['n']);
}
?>

<div class="contenedor-principal-amplio">
    <a href="index.php" class="btn-secundario" style="display: inline-block; width: auto; padding: 5px 15px; margin-bottom: 15px; text-decoration: none;">⬅ Regresar</a>
    
    <h2>Evaluación del Sistema - Inferencia Estadística</h2>
    
    <?php if(!$stats || $stats['n'] < 30): ?>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; margin-top: 20px;">
            <strong>Aviso:</strong> Actualmente hay <?php echo $stats['n'] ?? 0; ?> observaciones. 
            El proyecto exige un mínimo de 30 observaciones para el análisis de inferencia.
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; margin-bottom: 20px; margin-top: 20px;">
            
            <div class="tarjeta-producto" style="text-align: left;">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">1. Análisis Descriptivo</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;"><strong>Variable:</strong> Tiempo de registro de venta (seg)</li>
                    <li style="margin-bottom: 10px;"><strong>Muestra (n):</strong> <?php echo $stats['n']; ?> transacciones</li>
                    <li style="margin-bottom: 10px;"><strong>Media Aritmética:</strong> <?php echo $stats['media']; ?> seg</li>
                    <li style="margin-bottom: 10px;"><strong>Mediana:</strong> <?php echo $stats['mediana']; ?> seg</li>
                    <li style="margin-bottom: 10px;"><strong>Varianza Muestral:</strong> <?php echo $stats['varianza']; ?></li>
                    <li style="margin-bottom: 10px;"><strong>Desviación Estándar:</strong> <?php echo $stats['desviacion']; ?> seg</li>
                </ul>
            </div>

            <div class="tarjeta-producto" style="text-align: left;">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">2. Inferencia Estadística</h3>
                
                <h4 style="color: #007bff; margin-bottom: 5px;">Intervalo de Confianza (95%)</h4>
                <p style="font-size: 0.95rem; color: #444; margin-bottom: 15px;">Podemos afirmar con 95% de confianza que el tiempo promedio real de registro de ventas está entre <strong><?php echo $intervalo['limite_inferior']; ?> y <?php echo $intervalo['limite_superior']; ?> segundos.</strong></p>
                
                <h4 style="color: #007bff; margin-bottom: 5px;">Prueba de Hipótesis</h4>
                <p style="font-size: 0.95rem; background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <strong>H<sub>0</sub>:</strong> El tiempo promedio es mayor o igual a 120 seg.<br>
                    <strong>H<sub>1</sub>:</strong> El tiempo promedio es menor a 120 seg.
                </p>
                
                <p style="margin-bottom: 5px;"><strong>Estadístico Z calculado:</strong> <?php echo $prueba['z_calculado']; ?></p>
                <p style="margin-bottom: 10px;"><strong>Decisión:</strong> <span style="background: #e2e3e5; padding: 2px 8px; border-radius: 4px; font-weight: bold;"><?php echo $prueba['decision']; ?></span></p>
                
                <p style="font-size: 0.95rem; color: #444; border-left: 3px solid #28a745; padding-left: 10px;">
                    <em>Conclusión práctica:</em> <?php echo $prueba['decision'] == 'Se rechaza H0' ? 'El sistema cumple el objetivo y es altamente eficiente.' : 'El sistema aún debe optimizarse para bajar de los 2 minutos promediados.'; ?>
                </p>
            </div>
        </div>

        <div class="tarjeta-producto" style="margin-top: 20px;">
            <h3 style="margin-bottom: 15px;">Histograma de Tiempos de Respuesta</h3>
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="histograma"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const datosBrutos = <?php echo json_encode($datos); ?>;
            const ctx = document.getElementById('histograma').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: datosBrutos.map((_, i) => 'Venta ' + (i+1)),
                    datasets: [{
                        label: 'Tiempo de proceso (segundos)',
                        data: datosBrutos,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: { 
                    maintainAspectRatio: false,
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            grid: { color: '#eaeaea' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>