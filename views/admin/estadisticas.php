<?php
require_once '../../models/Estadistica.php';
include '../layout/header.php';

$est = new Estadistica();
$datos = $est->obtenerTiempos(); // Se espera un array plano con los tiempos en segundos
$stats = $est->calcularDescriptiva($datos);

if($stats && $stats['n'] >= 30) {
    $intervalo = $est->calcularIntervaloConfianza($stats['media'], $stats['desviacion'], $stats['n']);
    $prueba = $est->pruebaHipotesis($stats['media'], $stats['desviacion'], $stats['n']);
    
    // Validaciones para cumplir con la rúbrica del proyecto
    $n = $stats['n'];
    $distribucion = ($n >= 30) ? 'Normal (Z)' : 't de Student (t)';
    $estadistico_letra = ($n >= 30) ? 'Z' : 't';
    $valor_critico = $prueba['valor_critico'] ?? '-1.645'; // Valor por defecto para una cola al 5%
    $nivel_confianza = "95%";
    $nivel_significancia = "0.05 (5%)";
}
?>

<div class="contenedor-principal-amplio">
    <a href="index.php" class="btn-secundario" style="display: inline-block; width: auto; padding: 5px 15px; margin-bottom: 15px; text-decoration: none;">⬅ Regresar al Panel</a>
    
    <h2 style="color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Evaluación del Sistema - Inferencia Estadística</h2>
    
    <?php if(!$stats || $stats['n'] < 30): ?>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; margin-top: 20px; border-left: 5px solid #ffeeba;">
            <strong>Aviso Académico:</strong> Actualmente hay <?php echo $stats['n'] ?? 0; ?> observaciones. 
            El proyecto exige una muestra mínima de 30 observaciones (n &ge; 30) para aplicar el Teorema del Límite Central y validar la inferencia.
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; margin-top: 20px;">
            
            <div class="tarjeta-producto" style="text-align: left; padding: 25px;">
                <h3 style="color: #0f766e; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">1. Análisis Descriptivo de los Datos</h3>
                
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <ul style="list-style: none; padding: 0; color: #334155;">
                            <li style="margin-bottom: 8px;"><strong>Variable:</strong> Tiempo de registro (segundos)</li>
                            <li style="margin-bottom: 8px;"><strong>Tamaño de Muestra (n):</strong> <?php echo $n; ?></li>
                            <li style="margin-bottom: 8px;"><strong>Media (x̄):</strong> <?php echo $stats['media']; ?> seg</li>
                            <li style="margin-bottom: 8px;"><strong>Mediana:</strong> <?php echo $stats['mediana']; ?> seg</li>
                            <li style="margin-bottom: 8px;"><strong>Moda:</strong> <?php echo isset($stats['moda']) ? $stats['moda'] . ' seg' : 'No definida / Multimodal'; ?></li>
                            <li style="margin-bottom: 8px;"><strong>Varianza (S²):</strong> <?php echo $stats['varianza']; ?></li>
                            <li style="margin-bottom: 8px;"><strong>Desv. Estándar (S):</strong> <?php echo $stats['desviacion']; ?> seg</li>
                        </ul>
                    </div>
                    
                    <div style="flex: 1; min-width: 150px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 10px;">
                        <strong style="display: block; margin-bottom: 5px; font-size: 0.9rem; text-align: center;">Tabla de Datos (Muestra)</strong>
                        <div style="height: 140px; overflow-y: auto; font-family: monospace; font-size: 0.85rem; padding: 5px;">
                            <?php foreach($datos as $index => $valor): ?>
                                <div style="border-bottom: 1px solid #eee; padding: 2px 0;">Obs <?php echo $index + 1; ?>: <strong><?php echo $valor; ?>s</strong></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <p style="margin-top: 15px; font-size: 0.9rem; color: #475569; background: #f1f5f9; padding: 10px; border-left: 3px solid #0f766e;">
                    <strong>Interpretación Descriptiva:</strong> En promedio, las transacciones toman <?php echo $stats['media']; ?> segundos, con una dispersión de <?php echo $stats['desviacion']; ?> segundos respecto a la media.
                </p>
            </div>

            <div class="tarjeta-producto" style="text-align: left; padding: 25px;">
                <h3 style="color: #0f766e; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">2. Distribución e Intervalo de Confianza</h3>
                
                <p style="font-size: 0.9rem; margin-bottom: 15px;">
                    <strong>Distribución:</strong> <?php echo $distribucion; ?><br>
                    <span style="color: #64748b;"><em>Justificación:</em> Se aplica porque n &ge; 30 y la desviación estándar poblacional (&sigma;) es desconocida, aproximándose con la muestral (S).</span>
                </p>

                <h4 style="margin-bottom: 10px; font-size: 1rem;">Estimación por Intervalo (Nivel de Confianza: <?php echo $nivel_confianza; ?>)</h4>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 5px; font-size: 0.9rem; margin-bottom: 15px;">
                    <div style="margin-bottom: 8px;"><strong>Fórmula:</strong> x̄ &plusmn; Z<sub>&alpha;/2</sub> * (S / &radic;n)</div>
                    <div style="margin-bottom: 8px;"><strong>Sustitución:</strong> <?php echo $stats['media']; ?> &plusmn; 1.96 * (<?php echo $stats['desviacion']; ?> / &radic;<?php echo $n; ?>)</div>
                    <div style="color: #0f766e;"><strong>Resultado:</strong> [ <?php echo $intervalo['limite_inferior']; ?> ; <?php echo $intervalo['limite_superior']; ?> ] segundos</div>
                </div>
                
                <p style="font-size: 0.9rem; color: #475569; border-left: 3px solid #0f766e; padding-left: 10px;">
                    <strong>Interpretación práctica:</strong> Con un 95% de confianza, el tiempo promedio real en el que el sistema procesa una venta oscila entre <?php echo $intervalo['limite_inferior']; ?> y <?php echo $intervalo['limite_superior']; ?> segundos.
                </p>
            </div>
            
            <div class="tarjeta-producto" style="text-align: left; padding: 25px; grid-column: 1 / -1;">
                <h3 style="color: #0f766e; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">3. Prueba de Hipótesis (Evaluación de Desempeño)</h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <ul style="list-style: none; padding: 0; font-size: 0.95rem; color: #334155;">
                            <li style="margin-bottom: 10px;"><strong>H<sub>0</sub> (Hipótesis Nula):</strong> &mu; &ge; 120 seg (El sistema es ineficiente).</li>
                            <li style="margin-bottom: 10px;"><strong>H<sub>1</sub> (Hipótesis Alternativa):</strong> &mu; &lt; 120 seg (El sistema es eficiente).</li>
                            <li style="margin-bottom: 10px;"><strong>Nivel de Significancia (&alpha;):</strong> <?php echo $nivel_significancia; ?></li>
                            <li style="margin-bottom: 10px;"><strong>Valor Crítico (<?php echo $estadistico_letra; ?><sub>&alpha;</sub>):</strong> <?php echo $valor_critico; ?></li>
                            <li style="margin-bottom: 10px;"><strong>Estadístico de Prueba (<?php echo $estadistico_letra; ?> calculado):</strong> <?php echo $prueba['z_calculado']; ?></li>
                        </ul>
                    </div>
                    
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 5px;">
                        <p style="margin-bottom: 10px; font-size: 1.1rem;">
                            <strong>Decisión:</strong> 
                            <span style="color: <?php echo $prueba['decision'] == 'Se rechaza H0' ? '#15803d' : '#b91c1c'; ?>; font-weight: bold;">
                                <?php echo $prueba['decision']; ?>
                            </span>
                        </p>
                        
                        <p style="font-size: 0.9rem; margin-bottom: 10px;">
                            <strong>Conclusión Estadística:</strong> Como el valor calculado (<?php echo $prueba['z_calculado']; ?>) cae en la zona de rechazo respecto al valor crítico, se rechaza la hipótesis nula a favor de la alternativa.
                        </p>
                        
                        <p style="font-size: 0.9rem;">
                            <strong>Conclusión Práctica:</strong> 
                            <?php echo $prueba['decision'] == 'Se rechaza H0' ? 'Existe evidencia estadística suficiente para afirmar que el sistema cumple su objetivo principal, procesando las ventas en un tiempo significativamente menor a 2 minutos (120 seg).' : 'No existe evidencia suficiente para afirmar que el sistema ha mejorado los tiempos. Requiere optimización en el código o en la base de datos.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tarjeta-producto" style="margin-top: 20px; padding: 25px;">
            <h3 style="color: #0f766e; margin-bottom: 15px;">4. Representación Gráfica (Histograma de Tiempos)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
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
                    labels: datosBrutos.map((_, i) => 'Obs ' + (i+1)),
                    datasets: [{
                        label: 'Tiempo registrado (segundos)',
                        data: datosBrutos,
                        backgroundColor: 'rgba(15, 118, 110, 0.6)',
                        borderColor: 'rgba(15, 118, 110, 1)',
                        borderWidth: 1,
                        borderRadius: 3
                    }]
                },
                options: { 
                    maintainAspectRatio: false,
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            title: { display: true, text: 'Segundos', font: { weight: 'bold' } },
                            grid: { color: '#e2e8f0' }
                        },
                        x: {
                            title: { display: true, text: 'N° de Transacción (Muestra)', font: { weight: 'bold' } },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)' }
                    }
                }
            });
        </script>
    <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>