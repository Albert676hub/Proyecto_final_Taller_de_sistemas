</main>
        </div> </div> <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const btnCerrar = document.getElementById('btn-cerrar-sidebar');
            const btnAbrir = document.getElementById('btn-abrir-sidebar');

            // Funcionalidad para colapsar/expandir el sidebar
            btnCerrar.addEventListener('click', () => {
                sidebar.classList.toggle('colapsado');
            });

            // Para versiones móviles
            btnAbrir.addEventListener('click', () => {
                sidebar.classList.toggle('activo');
            });
        });
    </script>
</body>
</html> 