
 // Espera a que el DOM esté completamente cargado
 document.addEventListener('DOMContentLoaded', function() {
        const alerta = document.getElementById('alerta-exito');
        
        if(alerta) {
            // Oculta la alerta después de 3 segundos (3000 milisegundos)
            setTimeout(() => {
                alerta.style.opacity = '0';
                // Elimina completamente el elemento después de la transición
                setTimeout(() => alerta.remove(), 500);
            }, 3000);
        }
 });

 
