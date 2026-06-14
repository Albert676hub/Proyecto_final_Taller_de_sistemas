/**
 * Alterna la visibilidad de los metodos de pago
 * @param {string} metodo - 'qr' o 'tarjeta'
 */
function mostrarMetodo(metodo) {
    document.getElementById('pago-qr').classList.add('oculto');
    document.getElementById('pago-tarjeta').classList.add('oculto');
    
    // Quitar clase activa de los botones
    document.getElementById('btn-qr').classList.remove('activo');
    document.getElementById('btn-tarjeta').classList.remove('activo');
    
    // Mostrar lo seleccionado
    document.getElementById('pago-' + metodo).classList.remove('oculto');
    document.getElementById('btn-' + metodo).classList.add('activo');
}

/**
 * Simula el proceso de verificacion de pago QR con el servidor
 */
function simularVerificacion() {
    alert("Consultando con la entidad financiera...");
    
    // Simulacion de una peticion asincrona al servidor (Fetch API)
    setTimeout(() => {
        alert("¡Pago verificado correctamente! Su pedido esta en proceso.");
        window.location.href = "../../index.php"; 
    }, 2000);
}

/**
 * Simula el procesamiento de una tarjeta de credito/debito
 * @param {Event} evento - Evento de envio del formulario
 */
function procesarTarjeta(evento) {
    if (evento) evento.preventDefault();
    
    // Calculamos el tiempo total para las estadísticas
    let inicio = sessionStorage.getItem('inicio_compra');
    let tiempoTotal = inicio ? (Date.now() - parseInt(inicio)) / 1000 : 0;
    
    // Determinamos si está pagando con tarjeta o QR revisando qué panel está oculto
    let panelQR = document.getElementById('pago-qr');
    let metodoSeleccionado = panelQR && !panelQR.classList.contains('oculto') ? 'qr' : 'tarjeta';

    let datosPago = {
        metodo: metodoSeleccionado,
        tiempo_segundos: tiempoTotal
    };

    // 1. Aparece la pantalla de carga (Loader)
    mostrarPantallaCarga("Validando con la entidad financiera...");

    // 2. Simulamos un retraso de 2.5 segundos para la "validación segura"
    setTimeout(() => {
        
        // 3. Después de los 2.5s, se hace la petición real al servidor
        fetch('../../controllers/pago_controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosPago)
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                sessionStorage.removeItem('inicio_compra');
                
                // Transformamos el loader en una pantalla de éxito
                document.querySelector('.cargando-overlay h3').innerText = "¡Pago Exitoso!";
                document.querySelector('.spinner').style.display = 'none'; // Oculta la rueda giratoria
                document.querySelector('.cargando-overlay').innerHTML += `<div style="font-size:4rem; color:#28a745; margin-top:10px;">✅</div>`;
                
                // Tras 1.5 segundos extras viendo el "check verde", redirige a la tienda
                setTimeout(() => {
                    window.location.href = "index.php"; 
                }, 1500);
            } else {
                // Si el backend da error (ej. carrito vacío o sin stock)
                alert(data.message);
                document.querySelector('.cargando-overlay').remove();
            }
        })
        .catch(error => {
            alert("Ocurrió un error en el servidor.");
            let overlay = document.querySelector('.cargando-overlay');
            if(overlay) overlay.remove();
        });

    }, 2500); // 2500 milisegundos = 2.5 segundos de simulación
}

// --- Lógica del Carrito de Compras CRUD ---

document.addEventListener('DOMContentLoaded', () => {
    const btnToggleCarrito = document.getElementById('btn-toggle-carrito');
    const dropdownCarrito = document.getElementById('dropdown-carrito');

    // Mostrar/Ocultar el menú desplegable del carrito
    if (btnToggleCarrito) {
        btnToggleCarrito.addEventListener('click', () => {
            dropdownCarrito.classList.toggle('oculto');
            // Cargar datos al abrir
            if (!dropdownCarrito.classList.contains('oculto')) {
                peticionCarrito({ accion: 'obtener' });
            }
        });
    }

    // Cargar el contador al iniciar la página
    peticionCarrito({ accion: 'obtener' });
});

// Función central para hacer peticiones Fetch al controlador del carrito
function peticionCarrito(datos) {
    fetch('/sistema_ventas/controllers/carrito_controller.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            renderizarCarrito(data.carrito, data.total_articulos, data.total_precio);
        }
    })
    .catch(error => console.error("Error en el carrito:", error));
}

// CREATE / UPDATE: Añadir producto
function agregarAlCarrito(id, nombre, precio) {
    peticionCarrito({
        accion: 'agregar',
        id: id,
        nombre: nombre,
        precio: precio
    });
    
    // Usamos el toast en lugar del alert molesto
    mostrarToast(nombre + " añadido al carrito.");
}

// DELETE: Quitar producto del carrito
function eliminarDelCarrito(id) {
    peticionCarrito({ accion: 'eliminar', id: id });
}

// UPDATE: Cambiar cantidad
function cambiarCantidad(id, nuevaCantidad) {
    peticionCarrito({ accion: 'actualizar', id: id, cantidad: nuevaCantidad });
}

// READ: Actualizar la interfaz del Dropdown
function renderizarCarrito(carrito, totalArticulos, totalPrecio) {
    const listaCarrito = document.getElementById('lista-carrito');
    const contador = document.getElementById('contador-carrito');
    const spanTotal = document.getElementById('total-precio-carrito');

    if (!listaCarrito) return;

    contador.innerText = totalArticulos;
    spanTotal.innerText = totalPrecio;
    listaCarrito.innerHTML = '';

    if (Object.keys(carrito).length === 0) {
        listaCarrito.innerHTML = '<p class="carrito-vacio">El carrito está vacío</p>';
        return;
    }

    for (let id in carrito) {
        let item = carrito[id];
        listaCarrito.innerHTML += `
            <div class="item-carrito">
                <div class="info-item-carrito">
                    <span class="nombre-item">${item.nombre}</span>
                    <span class="precio-item">Bs. ${item.precio} x ${item.cantidad}</span>
                </div>
                <div class="controles-item">
                    <input type="number" min="1" value="${item.cantidad}" onchange="cambiarCantidad(${id}, this.value)" class="input-cantidad">
                    <button onclick="eliminarDelCarrito(${id})" class="btn-eliminar-item" title="Eliminar">🗑️</button>
                </div>
            </div>
        `;
    }
}

function mostrarPantallaCarga(mensaje) {
    const overlay = document.createElement('div');
    overlay.className = 'cargando-overlay';
    overlay.innerHTML = `
        <div class="spinner"></div>
        <h3>${mensaje}</h3>
        <p style="color:#666;">Por favor, no cierre esta ventana.</p>
    `;
    document.body.appendChild(overlay);
}

function mostrarToast(mensaje) {
    let contenedor = document.getElementById('toast-container');
    
    // Si no existe el contenedor en el HTML, lo creamos dinámicamente
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'toast-container';
        document.body.appendChild(contenedor);
    }
    
    const toast = document.createElement('div');
    toast.className = 'toast-mensaje';
    toast.innerHTML = `<span>✔️</span> ${mensaje}`; // Icono de éxito
    contenedor.appendChild(toast);

    // Desaparece automáticamente después de 3 segundos
    setTimeout(() => {
        toast.classList.add('ocultar');
        setTimeout(() => toast.remove(), 300); // Espera la animación CSS antes de borrar del DOM
    }, 3000);
}
function agregarAlCarrito(id, nombre, precio) {
    peticionCarrito({
        accion: 'agregar',
        id: id,
        nombre: nombre,
        precio: precio
    });
    
    mostrarToast(nombre + " añadido al carrito.");
}

function verificarCarritoVacio(evento) {
    const totalItems = document.getElementById('contador-carrito').innerText;
    
    // Si el contador es 0, detenemos el enlace y mostramos una advertencia
    if (parseInt(totalItems) === 0) {
        evento.preventDefault(); 
        mostrarToast("⚠️ El carrito está vacío. Seleccione un producto primero.");
    }
}