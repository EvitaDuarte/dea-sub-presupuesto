// Código para cerrar sesión por inactividad 

var tiempoInactividad = 1500000; // 25 minutos 900000; // 180000; // 900000 15 minutos en milisegundos
var tiempoUltimaActividad;

function cerrarSesion() {
    // Redirige a la página de inicio de sesión
    window.location.href = "index.html"; 
}

function reiniciarTiempo() {
    clearTimeout(tiempoUltimaActividad);
    tiempoUltimaActividad = setTimeout(cerrarSesion, tiempoInactividad);
}

["mousemove", "keydown", "click", "scroll", "touchstart"].forEach(evt =>
    document.addEventListener(evt, reiniciarTiempo)
);

//document.addEventListener("mousemove", reiniciarTiempo);
//document.addEventListener("keydown", reiniciarTiempo);

// Reinicia el temporizador al cargar la página
window.addEventListener("load", reiniciarTiempo);