// Función para mostrar descripción
function mostrarDescripcion(select) {
    console.log("Función ejecutándose...");
    try {
        const descripcion = select.selectedOptions[0].getAttribute('data-descripcion');
        const descDiv = document.getElementById('descripcion-plato');
        
        if(descDiv) {
            descDiv.textContent = descripcion || 'No hay descripción disponible';
            console.log("Descripción actualizada:", descripcion);
        }
    } catch (error) {
        console.error("Error en mostrarDescripcion:", error);
    }
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM cargado - Script activo");
    
    // Asignar evento manualmente
    const selectPlatos = document.getElementById('selector-platos');
    if(selectPlatos) {
        selectPlatos.addEventListener('change', function() {
            mostrarDescripcion(this);
        });
    }
});






///agrehar y eliminar platos

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando sistema de pedidos...');

    // 1. Elementos principales
    const contenedorPlatos = document.querySelector('.contenedor-platos');
    const btnAgregar = document.getElementById('agregar-plato');
    
    // 2. Almacenar el HTML original del select
    const selectOriginal = document.querySelector('.grupo-plato select[name="plato[]"]');
    const optionsTemplate = selectOriginal.innerHTML;

    // 3. Función para mostrar descripción 
    function mostrarDescripcion(selectElement) {
        const selectedOption = selectElement.selectedOptions[0];
        if (!selectedOption) return;
        
        const descripcion = selectedOption.dataset.descripcion || '';
        const descContainer = selectElement.closest('.campos-plato').querySelector('[data-descripcion-container]');
        
        if (descContainer) {
            descContainer.textContent = descripcion;
            console.log('Mostrando descripción:', descripcion.substring(0, 20) + '...');
        }
    }

    // 4. Delegación de eventos para cambios en selects
    contenedorPlatos.addEventListener('change', function(e) {
        if (e.target.matches('select[name="plato[]"]')) {
            mostrarDescripcion(e.target);
        }
    });

    // 5. Función para crear nuevo grupo de plato
    function crearNuevoGrupo() {
        const grupoOriginal = document.querySelector('.grupo-plato');
        const nuevoGrupo = grupoOriginal.cloneNode(true);
        
        // Restaurar el select con todas las opciones
        const nuevoSelect = nuevoGrupo.querySelector('select[name="plato[]"]');
        nuevoSelect.innerHTML = optionsTemplate;
        nuevoSelect.value = '';
        
        // Resetear otros valores
        nuevoGrupo.querySelector('input[type="number"]').value = 1;
        nuevoGrupo.querySelector('[data-descripcion-container]').textContent = '';
        
        return nuevoGrupo;
    }

    // 6. Evento para agregar plato
    btnAgregar.addEventListener('click', function() {
        const nuevoGrupo = crearNuevoGrupo();
        contenedorPlatos.appendChild(nuevoGrupo);
        console.log('Nuevo plato agregado:', contenedorPlatos.children.length);
    });

    // 7. Evento para eliminar plato
    contenedorPlatos.addEventListener('click', function(e) {
        if (e.target.classList.contains('boton-eliminar-plato')) {
            const grupos = document.querySelectorAll('.grupo-plato');
            if (grupos.length > 1) {
                e.target.closest('.grupo-plato').remove();
                console.log('Plato eliminado. Restantes:', grupos.length - 1);
            }
        }
    });

    console.log('Sistema de pedidos listo!');
});











//SUMAR

document.addEventListener('DOMContentLoaded', function() {
    const contenedorPlatos = document.querySelector('.contenedor-platos');
    const totalSpan = document.getElementById('total');
    const inputTotalHidden = document.getElementById('input-total');

    // Función para calcular el total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.grupo-plato').forEach(grupo => {
            const selectPlato = grupo.querySelector('select[name="plato[]"]');
            const inputCantidad = grupo.querySelector('input[name="cantidades[]"]');
            const precio = parseFloat(selectPlato.selectedOptions[0]?.dataset.precio) || 0;
            const cantidad = parseInt(inputCantidad.value) || 0;
            total += precio * cantidad;
        });

        totalSpan.textContent = `$${total.toFixed(2)}`;
        inputTotalHidden.value = total; // Para enviar al backend
    }

    // Eventos para recalcular
    contenedorPlatos.addEventListener('change', function(e) {
        if (e.target.matches('select[name="plato[]"], input[name="cantidades[]"]')) {
            calcularTotal();
        }
    });

    // Calcular al cargar la página (por si hay platos pre-agregados)
    calcularTotal();
});