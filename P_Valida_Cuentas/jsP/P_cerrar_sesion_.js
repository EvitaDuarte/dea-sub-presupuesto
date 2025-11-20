var tiempoInactividad = 1500000; // 25 minutos
var tiempoUltimaActividad;

function cerrarSesion() {
    // cerrar sesión en el servidor
    fetch("P_Cuentas00_Salir.php")
        .finally(() => {
            // redirigir al login
            window.location.href = "index.html";
        });
}

function reiniciarTiempo() {
    clearTimeout(tiempoUltimaActividad);
    tiempoUltimaActividad = setTimeout(cerrarSesion, tiempoInactividad);
}

["mousemove", "keydown", "click", "scroll", "touchstart"].forEach(evt =>
    document.addEventListener(evt, reiniciarTiempo)
);

window.addEventListener("load", reiniciarTiempo);
