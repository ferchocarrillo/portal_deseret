<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const buscandoUbicacion = ref(false);
const errorUbicacion = ref('');

function encontrarCercaDeMi() {
    errorUbicacion.value = '';

    if (!navigator.geolocation) {
        errorUbicacion.value =
            'Tu navegador no permite obtener la ubicación.';
        return;
    }

    buscandoUbicacion.value = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const latitud = position.coords.latitude;
            const longitud = position.coords.longitude;

            console.log('Latitud:', latitud);
            console.log('Longitud:', longitud);

            window.open(
                'https://local.churchofjesuschrist.org/es',
                '_blank'
            );

            buscandoUbicacion.value = false;
        },

        (error) => {
            buscandoUbicacion.value = false;

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorUbicacion.value =
                        'No permitiste el acceso a tu ubicación. Puedes buscar manualmente.';
                    break;

                case error.POSITION_UNAVAILABLE:
                    errorUbicacion.value =
                        'No pudimos determinar tu ubicación.';
                    break;

                case error.TIMEOUT:
                    errorUbicacion.value =
                        'La solicitud de ubicación tardó demasiado.';
                    break;

                default:
                    errorUbicacion.value =
                        'No fue posible obtener tu ubicación.';
            }
        },

        {
            enableHighAccuracy: false,
            timeout: 10000,
            maximumAge: 300000,
        }
    );
}



const abeja = ref(null);

let animacionAbeja = null;
let objetivoAbeja = null;

let posicionAbeja = {
    x: 0,
    y: 0,
};

let velocidadAbeja = {
    x: 0,
    y: 0,
};

// =====================================================
// ESTADO DE LA ABEJA
// =====================================================

let estadoAbeja = 'volando';
// volando | aterrizando | posada

let tiempoEstadoAbeja = 0;

let siguienteAterrizaje = 0;


// =====================================================
// CONFIGURACIÓN
// =====================================================

const configuracionAbeja = {
    velocidad: 0.55,
    velocidadMaxima: 3.2,

    distanciaEscape: 180,
    fuerzaEscape: 350,

    tiempoMinimoVuelo: 7000,
    tiempoMaximoVuelo: 14000,

    tiempoMinimoDescanso: 3000,
    tiempoMaximoDescanso: 6000,
};


// =====================================================
// UTILIDADES
// =====================================================

function numeroAleatorio(min, max) {
    return Math.random() * (max - min) + min;
}


// =====================================================
// NUEVO OBJETIVO
// =====================================================

function nuevoObjetivoAbeja() {

    const ancho = window.innerWidth;
    const alto = window.innerHeight;

    const margen = 80;

    objetivoAbeja = {
        x: margen + Math.random() * (ancho - margen * 2),
        y: margen + Math.random() * (alto - margen * 2),
    };
}


// =====================================================
// PROGRAMAR PRÓXIMO ATERRIZAJE
// =====================================================

function programarAterrizaje() {

    siguienteAterrizaje =
        performance.now() +
        numeroAleatorio(
            configuracionAbeja.tiempoMinimoVuelo,
            configuracionAbeja.tiempoMaximoVuelo
        );
}


// =====================================================
// ATERRIZAR
// =====================================================

function aterrizarAbeja() {

    estadoAbeja = 'aterrizando';

    velocidadAbeja.x *= 0.4;
    velocidadAbeja.y *= 0.4;

    // Elegimos un lugar tranquilo para descansar
    nuevoObjetivoAbeja();

    tiempoEstadoAbeja =
        performance.now() +
        1200;
}


// =====================================================
// POSARSE
// =====================================================

function posarAbeja() {

    estadoAbeja = 'posada';

    velocidadAbeja.x = 0;
    velocidadAbeja.y = 0;

    tiempoEstadoAbeja =
        performance.now() +
        numeroAleatorio(
            configuracionAbeja.tiempoMinimoDescanso,
            configuracionAbeja.tiempoMaximoDescanso
        );
}


// =====================================================
// DESPEGAR
// =====================================================

function despegarAbeja() {

    estadoAbeja = 'volando';

    nuevoObjetivoAbeja();

    // Pequeño impulso inicial
    velocidadAbeja.x =
        numeroAleatorio(-1.5, 1.5);

    velocidadAbeja.y =
        numeroAleatorio(-1.5, 1.5);

    programarAterrizaje();
}


// =====================================================
// ESCAPAR DEL MOUSE
// =====================================================

function escaparAbeja(mouseX, mouseY) {

    const dx = posicionAbeja.x - mouseX;
    const dy = posicionAbeja.y - mouseY;

    const distancia = Math.sqrt(
        dx * dx + dy * dy
    );

    if (distancia < configuracionAbeja.distanciaEscape) {

        const normalX =
            dx / (distancia || 1);

        const normalY =
            dy / (distancia || 1);

        objetivoAbeja = {

            x: Math.min(
                window.innerWidth - 60,
                Math.max(
                    60,
                    posicionAbeja.x +
                    normalX *
                    configuracionAbeja.fuerzaEscape
                )
            ),

            y: Math.min(
                window.innerHeight - 60,
                Math.max(
                    60,
                    posicionAbeja.y +
                    normalY *
                    configuracionAbeja.fuerzaEscape
                )
            ),
        };

        // Si estaba descansando, sale volando
        if (estadoAbeja === 'posada') {
            despegarAbeja();
        }
    }
}


// =====================================================
// ANIMACIÓN
// =====================================================

function animarAbeja() {

    if (!abeja.value) {
        return;
    }

    const ahora = performance.now();

    // =================================================
    // ABEJA POSADA
    // =================================================

    if (estadoAbeja === 'posada') {

        // Pequeñísimo movimiento para que no parezca
        // una imagen completamente estática
        const movimientoSuave =
            Math.sin(ahora * 0.004) * 1.5;

        abeja.value.style.transform = `
            translate3d(
                ${posicionAbeja.x}px,
                ${posicionAbeja.y + movimientoSuave}px,
                0
            )
            rotate(0deg)
        `;

        // Ya terminó su descanso
        if (ahora >= tiempoEstadoAbeja) {
            despegarAbeja();
        }

        animacionAbeja =
            requestAnimationFrame(animarAbeja);

        return;
    }


    // =================================================
    // ABEJA ATERRIZANDO
    // =================================================

    if (estadoAbeja === 'aterrizando') {

        const dx =
            objetivoAbeja.x -
            posicionAbeja.x;

        const dy =
            objetivoAbeja.y -
            posicionAbeja.y;

        const distancia =
            Math.sqrt(
                dx * dx +
                dy * dy
            );

        if (distancia > 8) {

            posicionAbeja.x += dx * 0.025;
            posicionAbeja.y += dy * 0.025;

        } else {

            posicionAbeja.x =
                objetivoAbeja.x;

            posicionAbeja.y =
                objetivoAbeja.y;
        }

        const angulo =
            Math.atan2(dy, dx) *
            180 /
            Math.PI;

        abeja.value.style.transform = `
            translate3d(
                ${posicionAbeja.x}px,
                ${posicionAbeja.y}px,
                0
            )
            rotate(${angulo * 0.08}deg)
        `;

        if (ahora >= tiempoEstadoAbeja) {
            posarAbeja();
        }

        animacionAbeja =
            requestAnimationFrame(animarAbeja);

        return;
    }


    // =================================================
    // ABEJA VOLANDO
    // =================================================

    if (!objetivoAbeja) {
        nuevoObjetivoAbeja();
    }

    // Cada cierto tiempo decide aterrizar
    if (
        ahora >= siguienteAterrizaje &&
        estadoAbeja === 'volando'
    ) {
        aterrizarAbeja();

        animacionAbeja =
            requestAnimationFrame(animarAbeja);

        return;
    }


    const dx =
        objetivoAbeja.x -
        posicionAbeja.x;

    const dy =
        objetivoAbeja.y -
        posicionAbeja.y;

    const distancia =
        Math.sqrt(
            dx * dx +
            dy * dy
        );


    // Llegó al objetivo
    if (distancia < 25) {

        nuevoObjetivoAbeja();

        animacionAbeja =
            requestAnimationFrame(animarAbeja);

        return;
    }


    // =================================================
    // MOVIMIENTO SUAVE
    // =================================================

    velocidadAbeja.x +=
        (dx / (distancia || 1)) *
        configuracionAbeja.velocidad;

    velocidadAbeja.y +=
        (dy / (distancia || 1)) *
        configuracionAbeja.velocidad;


    // Fricción
    velocidadAbeja.x *= 0.96;
    velocidadAbeja.y *= 0.96;


    // Limitar velocidad
    const velocidadMaxima =
        configuracionAbeja.velocidadMaxima;

    velocidadAbeja.x =
        Math.max(
            -velocidadMaxima,
            Math.min(
                velocidadMaxima,
                velocidadAbeja.x
            )
        );

    velocidadAbeja.y =
        Math.max(
            -velocidadMaxima,
            Math.min(
                velocidadMaxima,
                velocidadAbeja.y
            )
        );


    // =================================================
    // ACTUALIZAR POSICIÓN
    // =================================================

    posicionAbeja.x +=
        velocidadAbeja.x;

    posicionAbeja.y +=
        velocidadAbeja.y;


    // =================================================
    // ORIENTACIÓN
    // =================================================

    const angulo =
        Math.atan2(
            velocidadAbeja.y,
            velocidadAbeja.x
        ) *
        180 /
        Math.PI;


    abeja.value.style.transform = `
        translate3d(
            ${posicionAbeja.x}px,
            ${posicionAbeja.y}px,
            0
        )
        rotate(${angulo * 0.12}deg)
    `;


    animacionAbeja =
        requestAnimationFrame(animarAbeja);
}


// =====================================================
// MOUSE
// =====================================================

function moverMouse(evento) {

    escaparAbeja(
        evento.clientX,
        evento.clientY
    );
}


// =====================================================
// INICIAR
// =====================================================

function iniciarAbeja() {

    posicionAbeja = {
        x: window.innerWidth * 0.15,
        y: window.innerHeight * 0.35,
    };

    velocidadAbeja = {
        x: 0,
        y: 0,
    };

    estadoAbeja = 'volando';

    nuevoObjetivoAbeja();

    programarAterrizaje();

    window.addEventListener(
        'mousemove',
        moverMouse,
        { passive: true }
    );

    animacionAbeja =
        requestAnimationFrame(animarAbeja);
}


// =====================================================
// DETENER
// =====================================================

function detenerAbeja() {

    window.removeEventListener(
        'mousemove',
        moverMouse
    );

    if (animacionAbeja) {

        cancelAnimationFrame(
            animacionAbeja
        );

        animacionAbeja = null;
    }
}


// =====================================================
// MOUNTED
// =====================================================

onMounted(() => {

    const reducido =
        window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;

    if (!reducido) {
        iniciarAbeja();
    }

});


// =====================================================
// UNMOUNT
// =====================================================

onBeforeUnmount(() => {

    detenerAbeja();

});
</script>
<style scoped>
/* =========================================================
   ABEJA PORTAL DESERET
   ========================================================= */

.abeja-contenedor {
    position: relative;
    width: 76px;
    height: 58px;

    filter: drop-shadow(0 5px 6px rgba(0, 0, 0, 0.16));

    transform-origin: center center;

    animation: abejaFlotar 0.8s ease-in-out infinite alternate;
}


/* =========================================================
   CUERPO
   ========================================================= */

.abeja-cuerpo {
    position: absolute;

    left: 22px;
    top: 16px;

    width: 43px;
    height: 30px;

    /*
     * Las rayas ahora forman parte directamente
     * del fondo del cuerpo.
     */
    background:
        repeating-linear-gradient(90deg,
            #F4B72A 0px,
            #F4B72A 9px,
            #172A3D 9px,
            #172A3D 16px);

    border: 2px solid #172A3D;

    border-radius:
        50% 55% 48% 50%;

    transform: rotate(-8deg);

    /*
     * IMPORTANTE:
     * no ocultamos la cabeza.
     */
    overflow: visible;

    z-index: 3;
}


/* =========================================================
   CABEZA
   ========================================================= */

.abeja-cabeza {
    position: absolute;

    left: -14px;
    top: 1px;

    width: 25px;
    height: 25px;

    background: #F4B72A;

    border: 2px solid #172A3D;

    border-radius: 50%;

    z-index: 10;
}


/* =========================================================
   OJOS
   ========================================================= */

.abeja-ojo {
    position: absolute;

    top: 7px;

    width: 4px;
    height: 6px;

    background: #172A3D;

    border-radius: 50%;
}

.ojo-1 {
    left: 6px;
}

.ojo-2 {
    right: 5px;
}


/* =========================================================
   SONRISA
   ========================================================= */

.abeja-sonrisa {
    position: absolute;

    left: 7px;
    bottom: 4px;

    width: 9px;
    height: 5px;

    border-bottom:
        2px solid #172A3D;

    border-radius: 50%;
}


/* =========================================================
   ANTENAS
   ========================================================= */

.abeja-cabeza::before,
.abeja-cabeza::after {
    content: "";

    position: absolute;

    top: -9px;

    width: 2px;
    height: 11px;

    background: #172A3D;

    border-radius: 2px;

    transform-origin: bottom center;
}

.abeja-cabeza::before {
    left: 6px;
    transform: rotate(-25deg);
}

.abeja-cabeza::after {
    right: 5px;
    transform: rotate(25deg);
}


/* =========================================================
   RAYAS ANTIGUAS
   ========================================================= */

/*
 * Ya no necesitamos estas tres rayas porque el cuerpo
 * utiliza directamente un repeating-linear-gradient.
 *
 * Las dejamos ocultas por si siguen en el HTML.
 */

.abeja-raya,
.abeja-rayas {
    display: none;
}


/* =========================================================
   ALAS
   ========================================================= */

.abeja-alas {
    position: absolute;

    left: 28px;
    top: 1px;

    width: 43px;
    height: 27px;

    z-index: 5;
}


.ala {
    position: absolute;

    width: 25px;
    height: 16px;

    background:
        rgba(255, 255, 255, 0.88);

    border:
        1px solid rgba(148, 163, 184, 0.55);

    border-radius:
        70% 30% 70% 30%;

    animation:
        aleteo 0.12s infinite alternate ease-in-out;
}


.ala-izquierda {
    left: -8px;
    top: 1px;

    transform:
        rotate(-25deg);
}


.ala-derecha {
    right: -6px;
    top: 1px;

    transform:
        rotate(25deg) scaleX(-1);

    animation-name:
        aleteoDerecha;
}


/* =========================================================
   ALETEO
   ========================================================= */

@keyframes aleteo {

    from {
        transform:
            rotate(-25deg) scaleY(1);
    }

    to {
        transform:
            rotate(-25deg) scaleY(0.45);
    }
}


@keyframes aleteoDerecha {

    from {
        transform:
            rotate(25deg) scaleX(-1) scaleY(1);
    }

    to {
        transform:
            rotate(25deg) scaleX(-1) scaleY(0.45);
    }
}


/* =========================================================
   FLOTACIÓN
   ========================================================= */

@keyframes abejaFlotar {

    from {
        margin-top: -2px;
    }

    to {
        margin-top: 2px;
    }
}


/* =========================================================
   PORTAL DESERET — SISTEMA VISUAL DINÁMICO
   ========================================================= */

.welcome-hero {
    background:
        radial-gradient(circle at 15% 20%, rgba(244, 183, 42, 0.12), transparent 28%),
        radial-gradient(circle at 85% 25%, rgba(89, 103, 199, 0.10), transparent 30%),
        radial-gradient(circle at 55% 100%, rgba(242, 140, 69, 0.08), transparent 32%),
        #FFFDF8;
}

.hero-grid {
    position: absolute;
    inset: 0;
    opacity: 0.24;
    background-image:
        linear-gradient(rgba(23, 42, 61, 0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(23, 42, 61, 0.035) 1px, transparent 1px);
    background-size: 42px 42px;
    mask-image: linear-gradient(to bottom, black, transparent 85%);
}

.hero-dots {
    position: absolute;
    inset: 0;
    opacity: 0.35;
    background-image: radial-gradient(rgba(217, 154, 36, 0.45) 1.2px, transparent 1.2px);
    background-size: 28px 28px;
    mask-image: radial-gradient(circle at center, black, transparent 70%);
}

.hero-blob {
    position: absolute;
    border-radius: 42% 58% 62% 38% / 45% 42% 58% 55%;
    filter: blur(2px);
    will-change: transform;
}

.hero-blob-one {
    width: 330px;
    height: 330px;
    left: -110px;
    top: 8%;
    background: rgba(244, 183, 42, 0.13);
    animation: blobMoveOne 16s ease-in-out infinite alternate;
}

.hero-blob-two {
    width: 280px;
    height: 280px;
    right: -70px;
    top: 12%;
    background: rgba(89, 103, 199, 0.11);
    animation: blobMoveTwo 18s ease-in-out infinite alternate;
}

.hero-blob-three {
    width: 230px;
    height: 230px;
    left: 38%;
    bottom: -120px;
    background: rgba(242, 140, 69, 0.10);
    animation: blobMoveThree 20s ease-in-out infinite alternate;
}

.hero-blob-four {
    width: 190px;
    height: 190px;
    right: 28%;
    bottom: 12%;
    background: rgba(85, 167, 122, 0.08);
    animation: blobMoveFour 17s ease-in-out infinite alternate;
}

.animate-float {
    animation: floatingCard 4.5s ease-in-out infinite;
}

.animate-float-slow {
    animation: floatingCard 6s ease-in-out infinite reverse;
}

.honeycomb-pattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(30deg, rgba(217, 154, 36, 0.10) 12%, transparent 12.5%, transparent 87%, rgba(217, 154, 36, 0.10) 87.5%, rgba(217, 154, 36, 0.10)),
        linear-gradient(150deg, rgba(217, 154, 36, 0.10) 12%, transparent 12.5%, transparent 87%, rgba(217, 154, 36, 0.10) 87.5%, rgba(217, 154, 36, 0.10)),
        linear-gradient(30deg, rgba(217, 154, 36, 0.10) 12%, transparent 12.5%, transparent 87%, rgba(217, 154, 36, 0.10) 87.5%, rgba(217, 154, 36, 0.10)),
        linear-gradient(150deg, rgba(217, 154, 36, 0.10) 12%, transparent 12.5%, transparent 87%, rgba(217, 154, 36, 0.10) 87.5%, rgba(217, 154, 36, 0.10)),
        linear-gradient(60deg, rgba(217, 154, 36, 0.07) 25%, transparent 25.5%, transparent 75%, rgba(217, 154, 36, 0.07) 75%);
    background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0;
    background-size: 80px 140px;
    animation: honeycombDrift 24s linear infinite;
}

.hex-card {
    position: relative;
    display: block;
    min-height: 330px;
    overflow: hidden;
    clip-path: polygon(25% 6.7%, 75% 6.7%, 100% 50%, 75% 93.3%, 25% 93.3%, 0 50%);
    isolation: isolate;
    filter: drop-shadow(0 18px 24px rgba(23, 42, 61, 0.10));
    transition: transform 450ms cubic-bezier(.2, .8, .2, 1), filter 450ms ease;
}

.hex-card:hover {
    transform: translateY(-9px) scale(1.025);
    filter: drop-shadow(0 26px 32px rgba(23, 42, 61, 0.17));
}

.hex-card-image {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 700ms cubic-bezier(.2, .8, .2, 1), filter 700ms ease;
}

.hex-card:hover .hex-card-image {
    transform: scale(1.10);
    filter: saturate(1.12);
}

.hex-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(23, 42, 61, 0.05) 5%, rgba(23, 42, 61, 0.22) 42%, rgba(23, 42, 61, 0.88) 100%);
    transition: opacity 450ms ease;
}

.hex-card:hover .hex-card-overlay {
    opacity: 0.88;
}

.hex-card-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 0 18% 18%;
    color: white;
}

.hex-icon {
    display: flex;
    width: 48px;
    height: 48px;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.92);
    color: #172A3D;
    font-size: 22px;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.14);
    transform: translateY(0);
    transition: transform 350ms ease;
}

.hex-card:hover .hex-icon {
    transform: translateY(-4px) rotate(-4deg);
}

.hex-kicker {
    margin-bottom: 2px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .16em;
    text-transform: uppercase;
    opacity: .85;
}

.hex-card h3 {
    font-size: 1.55rem;
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.025em;
}

.hex-card p {
    margin-top: 7px;
    font-size: .78rem;
    line-height: 1.45;
    color: rgba(255, 255, 255, .88);
}

.hex-arrow {
    margin-top: 12px;
    font-size: .75rem;
    font-weight: 900;
    opacity: .95;
    transform: translateX(0);
    transition: transform 300ms ease;
}

.hex-card:hover .hex-arrow {
    transform: translateX(5px);
}

.hex-blue::after,
.hex-green::after,
.hex-orange::after,
.hex-purple::after,
.hex-coral::after,
.hex-teal::after {
    content: '';
    position: absolute;
    left: 25%;
    right: 25%;
    bottom: 6.7%;
    height: 4px;
    border-radius: 999px;
    opacity: .95;
}

.hex-blue::after {
    background: #4F8CC9;
}

.hex-green::after {
    background: #55A77A;
}

.hex-orange::after {
    background: #F28C45;
}

.hex-purple::after {
    background: #7C6CE5;
}

.hex-coral::after {
    background: #E56F68;
}

.hex-teal::after {
    background: #35A8A0;
}

@keyframes blobMoveOne {
    from {
        transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
    }

    to {
        transform: translate3d(70px, 35px, 0) rotate(25deg) scale(1.12);
    }
}

@keyframes blobMoveTwo {
    from {
        transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
    }

    to {
        transform: translate3d(-55px, 45px, 0) rotate(-22deg) scale(1.10);
    }
}

@keyframes blobMoveThree {
    from {
        transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
    }

    to {
        transform: translate3d(45px, -35px, 0) rotate(30deg) scale(1.08);
    }
}

@keyframes blobMoveFour {
    from {
        transform: translate3d(0, 0, 0) rotate(0deg) scale(1);
    }

    to {
        transform: translate3d(-35px, -25px, 0) rotate(-25deg) scale(1.14);
    }
}

@keyframes floatingCard {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-8px);
    }
}

@keyframes honeycombDrift {
    from {
        background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0;
    }

    to {
        background-position: 80px 140px, 80px 140px, 100px 175px, 100px 175px, 80px 140px;
    }
}

@media (max-width: 1023px) {
    .hex-card {
        min-height: 310px;
    }

    .hex-card-content {
        padding: 0 17% 17%;
    }
}

@media (max-width: 639px) {
    .hex-card {
        min-height: 300px;
        clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0 50%);
    }

    .hex-card-content {
        padding: 0 19% 18%;
    }

    .hex-card h3 {
        font-size: 1.4rem;
    }

    .hex-card p {
        font-size: .76rem;
    }
}

@media (prefers-reduced-motion: reduce) {

    .hero-blob,
    .animate-float,
    .animate-float-slow,
    .honeycomb-pattern {
        animation: none !important;
    }

    .hex-card,
    .hex-card-image,
    .hex-icon,
    .hex-arrow {
        transition: none !important;
    }
}
</style>
<template>
    <!-- ========================================================= -->
    <!-- ABEJA VOLANDO -->
    <!-- ========================================================= -->

    <div ref="abeja" class="pointer-events-none fixed left-0 top-0 z-[100] hidden md:block" aria-hidden="true">
        <div class="abeja-contenedor">

            <!-- ALAS -->
            <div class="abeja-alas">
                <span class="ala ala-izquierda"></span>
                <span class="ala ala-derecha"></span>
            </div>

            <!-- CUERPO -->
            <div class="abeja-cuerpo">

                <!-- CABEZA -->
                <span class="abeja-cabeza">
                    <span class="abeja-ojo ojo-1"></span>
                    <span class="abeja-ojo ojo-2"></span>
                    <span class="abeja-sonrisa"></span>
                </span>

                <!-- RAYAS -->
                <span class="abeja-raya raya-1"></span>
                <span class="abeja-raya raya-2"></span>
                <span class="abeja-raya raya-3"></span>

            </div>

        </div>
    </div>

    <Head title="Portal Deseret" />

    <div class="min-h-screen bg-[#F3F0E8] text-[#263646]">

        <!-- ========================================================= -->
        <!-- NAVEGACIÓN FIJA -->
        <!-- ========================================================= -->

        <div class="fixed left-0 right-0 top-0 z-50 border-b border-[#DCD6C9] bg-[#F3F0E8]/95 backdrop-blur">

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <nav class="flex min-h-[62px] items-center justify-between gap-4">

                    <!-- ===================================================== -->
                    <!-- ENLACES PRINCIPALES - ESCRITORIO -->
                    <!-- ===================================================== -->

                    <div class="hidden flex-wrap items-center justify-center gap-x-7 gap-y-2 md:flex lg:gap-x-9">

                        <a href="#servicios-profesionales"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Servicios
                        </a>

                        <a href="#empleos"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Empleos
                        </a>

                        <a href="#marketplace"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Marketplace
                        </a>

                        <a href="#cursos"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Cursos
                        </a>

                        <a href="#emprendimientos"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Emprendimientos
                        </a>

                        <a href="#clasificados"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Clasificados
                        </a>
                        <a href="#eventos"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Eventos
                        </a>

                        <a href="#nosotros"
                            class="text-xs font-bold uppercase tracking-wide text-[#526174] transition hover:text-[#D99A24]">
                            Sobre nosotros
                        </a>

                    </div>


                    <!-- ===================================================== -->
                    <!-- ACCIONES - ESCRITORIO -->
                    <!-- ===================================================== -->

                    <div class="hidden shrink-0 items-center gap-2 md:flex">

                        <Link v-if="canLogin" :href="route('login')"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#526174] transition hover:bg-white hover:text-[#172A3D]">
                            Iniciar sesión
                        </Link>

                        <Link :href="route('solicitud.ingreso')"
                            class="rounded-xl bg-[#D99A24] px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-[#C58919]">
                            Solicitar acceso
                        </Link>

                    </div>


                    <!-- ===================================================== -->
                    <!-- MENÚ MÓVIL -->
                    <!-- ===================================================== -->

                    <details class="relative ml-auto md:hidden">

                        <summary
                            class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-[#DCD6C9] bg-white/80 px-4 py-2.5 text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-white">

                            <span class="text-lg leading-none">☰</span>

                            <span>Menú</span>

                        </summary>


                        <!-- PANEL -->

                        <div
                            class="absolute right-0 top-[calc(100%+10px)] w-[280px] overflow-hidden rounded-2xl border border-[#DCD6C9] bg-[#FFFDF8] shadow-2xl">

                            <div class="p-3">

                                <!-- ENLACES -->

                                <div class="space-y-1">

                                    <a href="#servicios-profesionales"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Servicios
                                    </a>

                                    <a href="#empleos"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Empleos
                                    </a>

                                    <a href="#marketplace"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Marketplace
                                    </a>

                                    <a href="#cursos"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Cursos
                                    </a>

                                    <a href="#emprendimientos"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Emprendimientos
                                    </a>

                                    <a href="#clasificados"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Clasificados
                                    </a>

                                    <a href="#nosotros"
                                        class="block rounded-xl px-4 py-3 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#D99A24]">
                                        Sobre nosotros
                                    </a>

                                </div>


                                <!-- SEPARADOR -->

                                <div class="my-3 border-t border-[#E8E1D5]"></div>


                                <!-- AUTENTICACIÓN -->

                                <div class="space-y-2">

                                    <Link v-if="canLogin" :href="route('login')"
                                        class="block rounded-xl border border-[#DCD6C9] bg-white px-4 py-3 text-center text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#172A3D]">
                                        Iniciar sesión
                                    </Link>

                                    <Link :href="route('solicitud.ingreso')"
                                        class="block rounded-xl bg-[#D99A24] px-4 py-3 text-center text-sm font-bold text-white shadow-md transition hover:bg-[#C58919]">
                                        Solicitar acceso
                                    </Link>

                                </div>

                            </div>

                        </div>

                    </details>

                </nav>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- HEADER / MARCA -->
        <!-- ========================================================= -->

        <header class="relative z-40 bg-[#FFFDF8] pt-[62px]">
            <div class="relative overflow-hidden border-b border-[#EFE7D8]">
                <div class="absolute -left-24 top-0 h-56 w-56 rounded-full bg-[#F4B72A]/10 blur-3xl"></div>
                <div class="absolute -right-20 bottom-0 h-64 w-64 rounded-full bg-[#7C6CE5]/10 blur-3xl"></div>

                <div class="mx-auto flex min-h-[170px] max-w-7xl items-center justify-center px-6 py-7 lg:px-8">
                    <a href="/" class="group relative">
                        <div
                            class="absolute -inset-5 rounded-full bg-[#F4B72A]/10 blur-xl transition duration-500 group-hover:bg-[#F4B72A]/20">
                        </div>
                        <img src="/images/landing/logo.png" alt="Portal Deseret"
                            class="relative block h-auto w-[250px] transition duration-500 group-hover:scale-[1.03] sm:w-[290px] lg:w-[330px]">
                    </a>
                </div>
            </div>
        </header>


        <!-- ========================================================= -->
        <!-- HERO DINÁMICO -->
        <!-- ========================================================= -->

        <section id="inicio" class="welcome-hero relative isolate overflow-hidden bg-[#FFFDF8]">

            <!-- FONDO ANIMADO -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="hero-blob hero-blob-one"></div>
                <div class="hero-blob hero-blob-two"></div>
                <div class="hero-blob hero-blob-three"></div>
                <div class="hero-blob hero-blob-four"></div>
                <div class="hero-grid"></div>
                <div class="hero-dots"></div>
            </div>

            <div
                class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-14 sm:py-16 lg:grid-cols-[0.95fr_1.05fr] lg:px-8 lg:py-20">

                <!-- TEXTO -->
                <div class="relative z-10">

                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[#F0D58A] bg-white/80 px-4 py-2 text-xs font-black text-[#8D6410] shadow-sm backdrop-blur">
                        <span class="text-base">🐝</span>
                        <span>Bienvenido a la colmena</span>
                    </div>

                    <h1
                        class="mt-6 max-w-2xl text-5xl font-black leading-[0.98] tracking-[-0.04em] text-[#172A3D] sm:text-6xl lg:text-7xl">
                        Encuentra.
                        <span class="block text-[#D99A24]">Conecta.</span>
                        <span class="block text-[#5967C7]">Crece.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-8 text-[#526174] sm:text-xl">
                        Personas, ideas y oportunidades encontrándose en un mismo lugar.
                        Explora lo que la comunidad tiene para ti y descubre dónde puedes aportar.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link v-if="canRegister" :href="route('solicitud.ingreso')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-[#172A3D] px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-[#172A3D]/15 transition duration-300 hover:-translate-y-1 hover:bg-[#243B53]">
                            <span>Quiero ser parte</span>
                            <span>→</span>
                        </Link>

                        <a href="#explorar"
                            class="inline-flex items-center gap-2 rounded-2xl border border-[#DDD5C7] bg-white/85 px-6 py-3.5 text-sm font-black text-[#34495E] shadow-sm backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-[#D99A24] hover:bg-white">
                            <span>Explorar la colmena</span>
                            <span>↓</span>
                        </a>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-bold text-[#718096]">
                        <span class="inline-flex items-center gap-2"><span
                                class="h-2.5 w-2.5 rounded-full bg-[#4F8CC9]"></span>Servicios</span>
                        <span class="inline-flex items-center gap-2"><span
                                class="h-2.5 w-2.5 rounded-full bg-[#55A77A]"></span>Empleos</span>
                        <span class="inline-flex items-center gap-2"><span
                                class="h-2.5 w-2.5 rounded-full bg-[#F28C45]"></span>Marketplace</span>
                        <span class="inline-flex items-center gap-2"><span
                                class="h-2.5 w-2.5 rounded-full bg-[#7C6CE5]"></span>Cursos</span>
                    </div>
                </div>

                <!-- PANEL VISUAL -->
                <div class="relative z-10 mx-auto w-full max-w-2xl">

                    <div
                        class="absolute -left-5 top-10 hidden rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl backdrop-blur sm:block animate-float-slow">
                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#718096]">Conecta</p>
                        <p class="mt-1 text-sm font-black text-[#172A3D]">con personas</p>
                    </div>

                    <div
                        class="absolute -right-5 bottom-16 hidden rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl backdrop-blur sm:block animate-float">
                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#718096]">Descubre</p>
                        <p class="mt-1 text-sm font-black text-[#172A3D]">oportunidades</p>
                    </div>

                    <div
                        class="relative overflow-hidden rounded-[2.5rem] border-8 border-white/80 bg-white shadow-2xl shadow-[#172A3D]/10">
                        <img src="/images/landing/hero.jpg" alt="Personas colaborando"
                            class="h-[360px] w-full object-cover sm:h-[440px] lg:h-[500px]">

                        <div class="absolute inset-0 bg-gradient-to-t from-[#172A3D]/75 via-[#172A3D]/5 to-transparent">
                        </div>

                        <div
                            class="absolute bottom-5 left-5 right-5 rounded-[1.5rem] border border-white/50 bg-white/90 p-5 shadow-xl backdrop-blur-md sm:bottom-7 sm:left-7 sm:right-7">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#F4B72A] text-xl shadow-md">
                                    🐝
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#8D6410]">Portal
                                        Deseret</p>
                                    <p class="mt-1 text-base font-black text-[#172A3D] sm:text-lg">Aquí cada conexión
                                        puede abrir una nueva posibilidad.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="absolute -bottom-7 left-1/2 hidden -translate-x-1/2 items-center gap-2 rounded-full border border-white/80 bg-white/90 px-5 py-2.5 text-xs font-black text-[#526174] shadow-lg backdrop-blur sm:flex">
                        <span>✨</span>
                        <span>Tu lugar dentro de la comunidad</span>
                    </div>
                </div>
            </div>
        </section>


        <!-- ========================================================= -->
        <!-- COLMENA / MÓDULOS -->
        <!-- ========================================================= -->

        <section id="explorar" class="relative overflow-hidden bg-[#FFF8E9] py-16 lg:py-20">

            <div class="pointer-events-none absolute inset-0 opacity-40" aria-hidden="true">
                <div class="honeycomb-pattern"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">

                <div class="mx-auto mb-12 max-w-3xl text-center">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-[#8D6410] shadow-sm">
                        <span>🐝</span>
                        Explora la colmena
                    </span>

                    <h2
                        class="mt-5 text-4xl font-black leading-tight tracking-tight text-[#172A3D] sm:text-5xl lg:text-6xl">
                        ¿Qué estás buscando <span class="text-[#D99A24]">hoy?</span>
                    </h2>

                    <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-[#526174] sm:text-lg">
                        Seis espacios, una comunidad. Elige dónde quieres encontrar,
                        aprender, trabajar, comprar, emprender o compartir.
                    </p>
                </div>

                <!-- CONTENEDOR EN MODO COLMENA (2 - 3 - 2) -->
                <div class="mx-auto grid max-w-4x2 gap-2 sm:grid-cols-2 lg:grid-cols-3 lg:gap-0 items-center px-4">

                    <!-- ================================================= -->
                    <!-- COLUMNA IZQUIERDA (2 TARJETAS)                    -->
                    <!-- ================================================= -->
                    <div class="grid gap-2 lg:gap-0 lg:-mr-6 z-10">
                        <!-- EMPLEOS -->
                        <a href="#empleos" class="hex-card hex-green group lg:-my-2">
                            <img src="/images/landing/empleos.png" alt="Bolsa de empleos" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">💼</span>
                                <span class="hex-kicker">Oportunidades</span>
                                <h3>Empleos</h3>
                                <p>Descubre oportunidades para dar el siguiente paso.</p>
                                <span class="hex-arrow">Encontrar →</span>
                            </div>
                        </a>

                        <!-- MARKETPLACE -->
                        <a href="#marketplace" class="hex-card hex-orange group lg:-my-2">
                            <img src="/images/landing/marketplace.jpg" alt="Marketplace" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">🛍️</span>
                                <span class="hex-kicker">Descubre</span>
                                <h3>Marketplace</h3>
                                <p>Compra, vende y conecta con personas de la comunidad.</p>
                                <span class="hex-arrow">Explorar →</span>
                            </div>
                        </a>
                    </div>

                    <!-- ================================================= -->
                    <!-- COLUMNA CENTRAL (3 TARJETAS)                      -->
                    <!-- ================================================= -->
                    <div class="grid gap-2 lg:gap-0 z-20">
                        <!-- SERVICIOS (Arriba) -->
                        <a href="#servicios-profesionales" class="hex-card hex-blue group lg:-my-2">
                            <img src="/images/landing/servicios01.png" alt="Servicios profesionales"
                                class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">🛠️</span>
                                <span class="hex-kicker">Conecta</span>
                                <h3>Servicios</h3>
                                <p>Encuentra personas con habilidades que pueden ayudarte.</p>
                                <span class="hex-arrow">Explorar →</span>
                            </div>
                        </a>

                        <!-- CURSOS (Centro de la colmena) -->
                        <a id="seccion-cursos" href="#cursos" class="hex-card hex-purple group lg:-my-2">
                            <img src="/images/landing/cursos.jpg" alt="Cursos" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">🎓</span>
                                <span class="hex-kicker">Aprende</span>
                                <h3>Cursos</h3>
                                <p>Desarrolla nuevas habilidades y abre nuevas posibilidades.</p>
                                <span class="hex-arrow">Aprender →</span>
                            </div>
                        </a>

                        <!-- EMPRENDIMIENTOS (Abajo) -->
                        <a id="seccion-emprendimientos" href="/comunidad/emprendimientos"
                            class="hex-card hex-coral group lg:-my-2">
                            <img src="/images/landing/emprendimientos.jpg" alt="Emprendimientos" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">🚀</span>
                                <span class="hex-kicker">Crea</span>
                                <h3>Emprendimientos</h3>
                                <p>Conoce ideas, proyectos y personas que están creando.</p>
                                <span class="hex-arrow">Descubrir →</span>
                            </div>
                        </a>
                    </div>

                    <!-- ================================================= -->
                    <!-- COLUMNA DERECHA (2 TARJETAS)                      -->
                    <!-- ================================================= -->
                    <div
                        class="grid gap-2 sm:col-span-2 sm:grid-cols-2 lg:col-span-1 lg:grid-cols-1 lg:gap-0 lg:-ml-6 z-10">
                        <!-- EVENTOS -->
                        <a id="seccion-eventos" href="#eventos" class="hex-card hex-blue group lg:-my-2">
                            <img src="/images/landing/eventos01.jpg" alt="Eventos" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">🎤</span>
                                <span class="hex-kicker">Participa</span>
                                <h3>Eventos</h3>
                                <p>Vive momentos que inspiran, conectan y hacen crecer a la comunidad.</p>
                                <span class="hex-arrow">Ver eventos →</span>
                            </div>
                        </a>

                        <!-- CLASIFICADOS -->
                        <a href="#clasificados" class="hex-card hex-teal group lg:-my-2">
                            <img src="/images/landing/clasificados.png" alt="Clasificados" class="hex-card-image">
                            <div class="hex-card-overlay"></div>
                            <div class="hex-card-content">
                                <span class="hex-icon">📌</span>
                                <span class="hex-kicker">Encuentra</span>
                                <h3>Clasificados</h3>
                                <p>Publica, busca y encuentra oportunidades dentro de la comunidad.</p>
                                <span class="hex-arrow">Ver anuncios →</span>
                            </div>
                        </a>
                    </div>

                </div>

                <div
                    class="mx-auto mt-16 max-w-3xl rounded-[2rem] border border-white/80 bg-white/75 p-6 text-center shadow-lg backdrop-blur sm:mt-20 sm:p-8">
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F4B72A] text-2xl shadow-md">
                        🐝</div>
                    <h3 class="mt-4 text-2xl font-black text-[#172A3D] sm:text-3xl">Una colmena crece cuando cada pieza
                        aporta.</h3>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-[#718096] sm:text-base">
                        En Portal Deseret puedes participar de distintas maneras. Encuentra algo para ti y descubre cómo
                        puedes aportar a los demás.
                    </p>
                </div>
            </div>
        </section>


        <!-- ========================================================= -->
        <!-- SERVICIOS -->
        <!-- ========================================================= -->

        <section id="servicios-profesionales" class="scroll-mt-20 bg-[#E9EEF2] py-10 lg:py-12">

            <div class="mx-auto max-w-7xl px-6 lg:px-8">

                <!-- ===================================================== -->
                <!-- INTRODUCCIÓN -->
                <!-- ===================================================== -->


                <!-- ===================================================== -->
                <!-- INTRODUCCIÓN -->
                <!-- ===================================================== -->

                <div class="grid items-center gap-10 lg:grid-cols-2">

                    <!-- IMAGEN -->

                    <div class="overflow-hidden rounded-[2rem] shadow-xl">
                        <img src="/images/landing/servicios01.png"
                            alt="Profesionales y personas de diferentes oficios reunidos como comunidad"
                            class="h-[420px] w-full object-cover" />
                    </div>


                    <!-- TEXTO -->

                    <div>

                        <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                            Servicios
                        </span>


                        <h2 class="mt-4 text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">
                            El conocimiento de nuestra comunidad
                            <span class="text-[#D99A24]">
                                también puede ayudarte.
                            </span>
                        </h2>


                        <div class="mt-4 h-1 w-12 rounded-full bg-[#D99A24]"></div>

                        <p class="mt-4 text-base leading-7 text-[#526174] sm:text-lg">
                            En Portal Deseret reunimos personas con conocimientos,
                            experiencia y habilidades en diferentes áreas para que
                            puedas conocer lo que saben hacer y descubrir nuevas
                            posibilidades dentro de nuestra comunidad.
                        </p>


                        <p class="mt-4 text-base leading-7 text-[#526174] sm:text-lg">
                            Técnicos, profesionales, especialistas y personas con
                            diferentes oficios forman parte de una red donde el
                            conocimiento puede convertirse en oportunidades,
                            colaboración y crecimiento.
                        </p>


                        <!-- BENEFICIOS -->

                        <div class="mt-6 space-y-3">

                            <!-- BENEFICIO 1 -->

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                    ✓
                                </div>

                                <div>

                                    <h3 class="font-bold text-[#172A3D]">
                                        Diversidad de conocimientos
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-[#718096]">
                                        Encuentra personas con diferentes profesiones,
                                        oficios, habilidades y experiencias.
                                    </p>

                                </div>

                            </div>


                            <!-- BENEFICIO 2 -->

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                    ✓
                                </div>

                                <div>

                                    <h3 class="font-bold text-[#172A3D]">
                                        Personas de confianza
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-[#718096]">
                                        Conoce quiénes hacen parte de la red y qué
                                        conocimientos o servicios pueden ofrecer.
                                    </p>

                                </div>

                            </div>


                            <!-- BENEFICIO 3 -->

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                    ✓
                                </div>

                                <div>

                                    <h3 class="font-bold text-[#172A3D]">
                                        Nuevas oportunidades
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-[#718096]">
                                        Una habilidad puede convertirse en una
                                        colaboración, una contratación o un nuevo
                                        emprendimiento.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ========================================================= -->
                <!-- EMPLEOS -->
                <!-- ========================================================= -->

                <section id="empleos" class="scroll-mt-10 bg-[#E9EEF2] py-10 lg:py-12">

                    <div class="mx-auto grid max-w-7xl items-center gap-14 px-6 lg:grid-cols-2 lg:px-8">

                        <!-- TEXTO -->
                        <div class="order-1">

                            <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                                Empleos
                            </span>

                            <h2 class="mt-4 text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">
                                Oportunidades reales para tu
                                <span class="text-[#D99A24]">crecimiento profesional.</span>
                            </h2>

                            <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]"></div>

                            <p class="mt-6 text-base leading-7 text-[#526174] sm:text-lg">
                                En Portal Deseret conectamos personas con organizaciones
                                que buscan talento, experiencia y compromiso.
                            </p>

                            <p class="mt-4 text-base leading-7 text-[#526174]">
                                Explora oportunidades laborales, conoce sus requisitos
                                y encuentra espacios donde tus capacidades puedan
                                convertirse en una nueva oportunidad.
                            </p>

                            <!-- BENEFICIOS -->
                            <div class="mt-8 space-y-5">

                                <div class="flex gap-4">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                        🔎
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-[#172A3D]">
                                            Vacantes verificadas
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-[#718096]">
                                            Conoce oportunidades publicadas por organizaciones
                                            que buscan personas como tú.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                        🤝
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-[#172A3D]">
                                            Conexiones de confianza
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-[#718096]">
                                            Acerca a quienes buscan talento con quienes
                                            tienen conocimientos y experiencia para aportar.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-lg">
                                        📈
                                    </div>

                                    <div>
                                        <h3 class="font-bold text-[#172A3D]">
                                            Crecimiento profesional
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-[#718096]">
                                            Una oportunidad laboral puede ser el comienzo
                                            de una nueva etapa.
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <!-- CTA -->
                            <div class="mt-10 flex flex-wrap gap-4">

                                <Link :href="route('solicitud.ingreso')"
                                    class="rounded-xl bg-[#D99A24] px-7 py-4 text-sm font-bold text-white shadow-md transition hover:bg-[#C58919]">
                                    Explorar oportunidades →
                                </Link>

                            </div>

                        </div>


                        <!-- IMAGEN DERECHA -->
                        <div class="order-2 overflow-hidden rounded-[2rem] shadow-xl">

                            <img src="/images/landing/empleos.png" alt="Personas celebrando una oportunidad laboral"
                                class="h-[430px] w-full object-cover lg:h-[560px]" />

                        </div>

                    </div>

                </section>
                <!-- ========================================================= -->
                <!-- MARKETPLACE -->
                <!-- ========================================================= -->

                <section id="marketplace" class="scroll-mt-10 bg-[#F3F0E8] py-12 lg:py-16">

                    <div class="mx-auto max-w-7xl px-6 lg:px-8">

                        <div class="overflow-hidden rounded-[2.5rem] bg-[#FFFDF8] shadow-xl ring-1 ring-[#DDD7CA]">

                            <div class="grid items-stretch lg:grid-cols-2">


                                <!-- ================================================= -->
                                <!-- IMAGEN — IZQUIERDA -->
                                <!-- ================================================= -->

                                <div class="relative min-h-[420px] lg:min-h-[560px]">

                                    <img src="/images/landing/marketplace.jpg"
                                        alt="Bodega con productos organizados para venta y distribución"
                                        class="absolute inset-0 h-full w-full object-cover" />


                                    <!-- DEGRADADO -->

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-[#172A3D]/80 via-transparent to-transparent">
                                    </div>


                                    <!-- ETIQUETA -->

                                    <div
                                        class="absolute bottom-6 left-6 right-6 rounded-2xl border border-white/30 bg-[#FFFDF8]/95 p-5 shadow-xl backdrop-blur">

                                        <div class="flex items-center gap-4">

                                            <div
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-xl">

                                                🛒

                                            </div>

                                            <div>

                                                <p class="text-xs font-bold uppercase tracking-wider text-[#9A6A12]">

                                                    Marketplace Deseret

                                                </p>

                                                <p class="mt-1 font-black text-[#172A3D]">

                                                    Compra, vende y conecta.

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- CONTENIDO — DERECHA -->
                                <!-- ================================================= -->

                                <div class="flex flex-col justify-center p-8 sm:p-10 lg:p-14">

                                    <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">

                                        Marketplace

                                    </span>


                                    <h2
                                        class="mt-4 max-w-xl text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">

                                        Productos de nuestra comunidad
                                        <span class="text-[#D99A24]">
                                            en un solo lugar.
                                        </span>

                                    </h2>


                                    <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]">
                                    </div>


                                    <p class="mt-6 max-w-xl text-base leading-7 text-[#526174] sm:text-lg">

                                        Descubre productos ofrecidos por personas y
                                        emprendimientos de nuestra comunidad. Encuentra
                                        artículos, compara opciones y conecta directamente
                                        con quienes los ofrecen.

                                    </p>


                                    <!-- ================================================= -->
                                    <!-- CARACTERÍSTICAS -->
                                    <!-- ================================================= -->

                                    <div class="mt-7 space-y-4">


                                        <!-- ITEM 1 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-lg">

                                                🛍️

                                            </div>

                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Productos de la comunidad

                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Conoce lo que otras personas están
                                                    ofreciendo y descubre nuevas opciones
                                                    de compra.

                                                </p>

                                            </div>

                                        </div>


                                        <!-- ITEM 2 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-lg">

                                                🤝

                                            </div>

                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Compra directamente

                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Conecta con quienes producen, venden
                                                    o distribuyen los artículos.

                                                </p>

                                            </div>

                                        </div>


                                        <!-- ITEM 3 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-lg">

                                                📦

                                            </div>

                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Una vitrina para tus productos

                                                </h3>

                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Si tienes productos para ofrecer,
                                                    este puede ser un nuevo espacio para
                                                    darlos a conocer.

                                                </p>

                                            </div>

                                        </div>


                                    </div>


                                    <!-- ================================================= -->
                                    <!-- CTA -->
                                    <!-- ================================================= -->

                                    <div class="mt-8 flex flex-wrap gap-3">

                                        <a href="#marketplace"
                                            class="inline-flex items-center rounded-xl bg-[#172A3D] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#243B53]">

                                            Explorar Marketplace →

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </section>

                <section id="cursos" class="scroll-mt-10 bg-[#F3F0E8] py-12 lg:py-16">

                    <div class="mx-auto max-w-7xl px-6 lg:px-8">

                        <div class="grid items-center gap-10 lg:grid-cols-[0.9fr_1.1fr]">


                            <!-- ================================================= -->
                            <!-- TEXTO -->
                            <!-- ================================================= -->

                            <div>

                                <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                                    Cursos
                                </span>


                                <h2 class="mt-4 max-w-xl text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">

                                    Nunca es tarde
                                    <span class="text-[#D99A24]">
                                        para aprender.
                                    </span>

                                </h2>


                                <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]">
                                </div>


                                <p class="mt-6 max-w-xl text-base leading-7 text-[#526174] sm:text-lg">

                                    Accede a cursos y capacitaciones diseñados para ayudarte
                                    a desarrollar nuevas habilidades, fortalecer tu
                                    conocimiento y abrir nuevas oportunidades dentro y fuera
                                    de nuestra comunidad.

                                </p>


                                <!-- ================================================= -->
                                <!-- BENEFICIOS -->
                                <!-- ================================================= -->

                                <div class="mt-7 space-y-4">


                                    <!-- BENEFICIO 1 -->

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                            📖

                                        </div>


                                        <div>

                                            <h3 class="font-bold text-[#172A3D]">

                                                Aprendizaje flexible

                                            </h3>


                                            <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                Estudia a tu ritmo y encuentra opciones que
                                                se adapten a tu tiempo y necesidades.

                                            </p>

                                        </div>

                                    </div>


                                    <!-- BENEFICIO 2 -->

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                            🎓

                                        </div>


                                        <div>

                                            <h3 class="font-bold text-[#172A3D]">

                                                Diversidad de temas

                                            </h3>


                                            <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                Encuentra formación en áreas profesionales,
                                                técnicas, personales y de emprendimiento.

                                            </p>

                                        </div>

                                    </div>


                                    <!-- BENEFICIO 3 -->

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                            🏅

                                        </div>


                                        <div>

                                            <h3 class="font-bold text-[#172A3D]">

                                                Certificados que suman

                                            </h3>


                                            <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                Fortalece tu perfil con nuevos conocimientos,
                                                habilidades y certificaciones.

                                            </p>

                                        </div>

                                    </div>


                                </div>


                                <!-- ================================================= -->
                                <!-- BOTONES -->
                                <!-- ================================================= -->

                                <div class="mt-8 flex flex-wrap gap-3">

                                    <a href="#cursos"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[#D99A24] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#C58919]">

                                        <span>
                                            Explorar cursos
                                        </span>

                                        <span class="text-lg">
                                            →
                                        </span>

                                    </a>
                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- IMAGEN -->
                            <!-- ================================================= -->

                            <div class="relative overflow-hidden rounded-[2.5rem] shadow-xl">

                                <img src="/images/landing/cursos01.jpg"
                                    alt="Adulto celebrando su graduación después de completar sus estudios"
                                    class="h-[460px] w-full object-cover lg:h-[600px]" />


                                <!-- DEGRADADO -->

                                <div
                                    class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-[#172A3D]/80 to-transparent">
                                </div>


                                <!-- MENSAJE SOBRE LA IMAGEN -->

                                <div
                                    class="absolute bottom-6 left-6 right-6 rounded-2xl border border-white/30 bg-[#FFFDF8]/95 p-5 shadow-xl backdrop-blur">

                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#9A6A12]">

                                        Sigue aprendiendo

                                    </p>


                                    <p class="mt-1 text-xl font-black text-[#172A3D] sm:text-2xl">

                                        Tu próxima meta puede comenzar hoy.

                                    </p>

                                </div>

                            </div>



                        </div>

                    </div>

                </section>
                <!-- ========================================================= -->
                <!-- EMPRENDIMIENTOS -->
                <!-- ========================================================= -->

                <section id="emprendimientos" class="scroll-mt-10 bg-[#F3F0E8] py-12 lg:py-16">

                    <div class="mx-auto max-w-7xl px-6 lg:px-8">

                        <!-- ===================================================== -->
                        <!-- BLOQUE PRINCIPAL -->
                        <!-- ===================================================== -->

                        <div class="overflow-hidden rounded-[2.5rem] bg-[#FFFDF8] shadow-xl ring-1 ring-[#DDD7CA]">

                            <div class="grid items-stretch lg:grid-cols-2">


                                <!-- ================================================= -->
                                <!-- IMAGEN — IZQUIERDA -->
                                <!-- ================================================= -->

                                <div class="relative min-h-[440px] lg:min-h-[580px]">

                                    <img src="/images/landing/emprendimientos.jpg"
                                        alt="Feria de emprendimientos con diferentes productos y personas de la comunidad"
                                        class="absolute inset-0 h-full w-full object-cover" />


                                    <!-- DEGRADADO -->

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-[#172A3D]/80 via-transparent to-transparent">
                                    </div>


                                    <!-- ETIQUETA SOBRE LA IMAGEN -->

                                    <div
                                        class="absolute bottom-6 left-6 right-6 rounded-2xl border border-white/30 bg-[#FFFDF8]/95 p-5 shadow-xl backdrop-blur">

                                        <div class="flex items-center gap-4">

                                            <div
                                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#D99A24] text-xl">

                                                💡

                                            </div>


                                            <div>

                                                <p class="text-xs font-bold uppercase tracking-[0.15em] text-[#9A6A12]">

                                                    Comunidad emprendedora

                                                </p>


                                                <p class="mt-1 font-black text-[#172A3D]">

                                                    Ideas que se convierten en oportunidades.

                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- CONTENIDO — DERECHA -->
                                <!-- ================================================= -->

                                <div class="flex flex-col justify-center p-8 sm:p-10 lg:p-14">

                                    <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">

                                        Emprendimientos

                                    </span>


                                    <h2
                                        class="mt-4 max-w-xl text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">

                                        Impulsa tu idea,
                                        <span class="text-[#D99A24]">
                                            inspira a otros.
                                        </span>

                                    </h2>


                                    <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]">
                                    </div>


                                    <p class="mt-6 max-w-xl text-base leading-7 text-[#526174] sm:text-lg">

                                        Conoce los emprendimientos de nuestra comunidad,
                                        descubre productos y servicios únicos y encuentra
                                        nuevas oportunidades para apoyar, colaborar y crecer
                                        juntos.

                                    </p>


                                    <!-- ================================================= -->
                                    <!-- BENEFICIOS -->
                                    <!-- ================================================= -->

                                    <div class="mt-7 space-y-4">


                                        <!-- BENEFICIO 1 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                                🏪

                                            </div>


                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Descubre emprendimientos

                                                </h3>


                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Encuentra proyectos, productos y servicios
                                                    creados por personas de nuestra comunidad.

                                                </p>

                                            </div>

                                        </div>


                                        <!-- BENEFICIO 2 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                                🤝

                                            </div>


                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Conecta y colabora

                                                </h3>


                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Genera alianzas, comparte conocimientos
                                                    y fortalece tu red de contactos.

                                                </p>

                                            </div>

                                        </div>


                                        <!-- BENEFICIO 3 -->

                                        <div class="flex items-start gap-4">

                                            <div
                                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                                📈

                                            </div>


                                            <div>

                                                <h3 class="font-bold text-[#172A3D]">

                                                    Haz crecer tu proyecto

                                                </h3>


                                                <p class="mt-1 text-sm leading-6 text-[#718096]">

                                                    Encuentra espacios, recursos y conexiones
                                                    que pueden ayudarte a llevar tu idea más lejos.

                                                </p>

                                            </div>

                                        </div>


                                    </div>


                                    <!-- ================================================= -->
                                    <!-- BOTONES -->
                                    <!-- ================================================= -->

                                    <div class="mt-8 flex flex-wrap gap-3">

                                        <a href="/comunidad/emprendimientos"
                                            class="inline-flex items-center gap-2 rounded-xl bg-[#D99A24] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#C58919]">

                                            <span>
                                                Explorar emprendimientos
                                            </span>

                                            <span class="text-lg">
                                                →
                                            </span>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>




                    </div>

                </section>

                

                <!-- ========================================================= -->
                <!-- CLASIFICADOS -->
                <!-- ========================================================= -->

                <section id="clasificados" class="mt-24 scroll-mt-10">

                    <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">


                        <!-- ================================================= -->
                        <!-- INFORMACIÓN -->
                        <!-- ================================================= -->

                        <div>

                            <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                                Clasificados
                            </span>


                            <h2 class="mt-4 max-w-xl text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">

                                Encuentra lo que
                                <span class="text-[#D99A24]">
                                    necesitas.
                                </span>

                            </h2>


                            <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]">
                            </div>


                            <p class="mt-6 max-w-xl text-base leading-7 text-[#526174] sm:text-lg">

                                Descubre anuncios y oportunidades publicadas por personas
                                de nuestra comunidad. Encuentra productos, servicios,
                                oportunidades y todo aquello que alguien está buscando
                                ofrecer o encontrar.

                            </p>


                            <!-- ================================================= -->
                            <!-- BENEFICIOS -->
                            <!-- ================================================= -->

                            <div class="mt-7 space-y-4">


                                <!-- BENEFICIO 1 -->

                                <div class="flex items-start gap-4">

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                        🔎

                                    </div>


                                    <div>

                                        <h3 class="font-bold text-[#172A3D]">

                                            Encuentra oportunidades

                                        </h3>


                                        <p class="mt-1 text-sm leading-6 text-[#718096]">

                                            Explora anuncios de personas y negocios
                                            que tienen algo que ofrecer.

                                        </p>

                                    </div>

                                </div>


                                <!-- BENEFICIO 2 -->

                                <div class="flex items-start gap-4">

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                        📌

                                    </div>


                                    <div>

                                        <h3 class="font-bold text-[#172A3D]">

                                            Publica tus anuncios

                                        </h3>


                                        <p class="mt-1 text-sm leading-6 text-[#718096]">

                                            Comparte lo que vendes, ofreces, buscas
                                            o necesitas dentro de la comunidad.

                                        </p>

                                    </div>

                                </div>


                                <!-- BENEFICIO 3 -->

                                <div class="flex items-start gap-4">

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">

                                        🤝

                                    </div>


                                    <div>

                                        <h3 class="font-bold text-[#172A3D]">

                                            Conecta con la comunidad

                                        </h3>


                                        <p class="mt-1 text-sm leading-6 text-[#718096]">

                                            Encuentra personas interesadas y genera
                                            nuevas oportunidades de contacto.

                                        </p>

                                    </div>

                                </div>


                            </div>


                            <!-- ================================================= -->
                            <!-- BOTONES -->
                            <!-- ================================================= -->

                            <div class="mt-8 flex flex-wrap gap-3">

                                <a href="#clasificados"
                                    class="inline-flex items-center gap-2 rounded-xl bg-[#D99A24] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#C58919]">

                                    <span>
                                        Explorar clasificados
                                    </span>

                                    <span class="text-lg">
                                        →
                                    </span>

                                </a>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- IMAGEN -->
                        <!-- ================================================= -->

                        <div class="relative">

                            <div class="absolute -inset-4 rounded-[2rem] bg-[#F5E4BC]/60 blur-2xl">
                            </div>


                            <div class="relative overflow-hidden rounded-[2rem] shadow-xl">

                                <img src="/images/landing/clasificados.png" alt="Clasificados"
                                    class="h-[420px] w-full object-cover transition duration-700 hover:scale-105" />

                            </div>

                        </div>


                    </div>

                </section>

                <section id="eventos" class="scroll-mt-10 bg-[#F3F0E8] py-12 lg:py-16">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">

                            <!-- ================================================= -->
                            <!-- IMAGEN (AHORA A LA IZQUIERDA) -->
                            <!-- ================================================= -->
                            <div class="relative overflow-hidden rounded-[2.5rem] shadow-xl">
                                <img src="/images/landing/eventos01.jpg"
                                    alt="Participantes disfrutando de un evento comunitario e inspirador"
                                    class="h-[460px] w-full object-cover lg:h-[600px]" />

                                <!-- DEGRADADO -->
                                <div
                                    class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-[#172A3D]/80 to-transparent">
                                </div>

                                <!-- MENSAJE SOBRE LA IMAGEN -->
                                <div
                                    class="absolute bottom-6 left-6 right-6 rounded-2xl border border-white/30 bg-[#FFFDF8]/95 p-5 shadow-xl backdrop-blur">
                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#9A6A12]">
                                        Comunidad activa
                                    </p>
                                    <p class="mt-1 text-xl font-black text-[#172A3D] sm:text-2xl">
                                        Cada encuentro es una nueva oportunidad para crecer.
                                    </p>
                                </div>
                            </div>

                            <!-- ================================================= -->
                            <!-- TEXTO (AHORA A LA DERECHA) -->
                            <!-- ================================================= -->
                            <div>
                                <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                                    Eventos
                                </span>

                                <h2 class="mt-4 max-w-xl text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">
                                    Comparte momentos que
                                    <span class="text-[#D99A24]">
                                        inspiran y conectan.
                                    </span>
                                </h2>

                                <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]"></div>

                                <p class="mt-6 max-w-xl text-base leading-7 text-[#526174] sm:text-lg">
                                    Participa en nuestros eventos, talleres y encuentros comunitarios diseñados para
                                    fomentar la integración, el aprendizaje colaborativo y el crecimiento conjunto.
                                </p>

                                <!-- ================================================= -->
                                <!-- BENEFICIOS / CARACTERÍSTICAS -->
                                <!-- ================================================= -->
                                <div class="mt-7 space-y-4">

                                    <!-- BENEFICIO 1 -->
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">
                                            🎤
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-[#172A3D]">
                                                Encuentros en vivo
                                            </h3>
                                            <p class="mt-1 text-sm leading-6 text-[#718096]">
                                                Asiste a charlas, ponencias y presentaciones con invitados especiales y
                                                expertos.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- BENEFICIO 2 -->
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">
                                            🤝
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-[#172A3D]">
                                                Networking y comunidad
                                            </h3>
                                            <p class="mt-1 text-sm leading-6 text-[#718096]">
                                                Conecta con otras personas que comparten tus mismos intereses y metas
                                                profesionales.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- BENEFICIO 3 -->
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F5E4BC] text-xl">
                                            🗓️
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-[#172A3D]">
                                                Agenda activa
                                            </h3>
                                            <p class="mt-1 text-sm leading-6 text-[#718096]">
                                                Mantente al tanto de las próximas fechas, actividades y eventos
                                                especiales del año.
                                            </p>
                                        </div>
                                    </div>

                                </div>

                                <!-- ================================================= -->
                                <!-- BOTONES -->
                                <!-- ================================================= -->
                                <div class="mt-8 flex flex-wrap gap-3">
                                    <a href="#eventos"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[#D99A24] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#C58919]">
                                        <span>Ver próximos eventos</span>
                                        <span class="text-lg">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- CATEGORÍAS -->

                <div class="mt-8">

                    <div class="flex items-end justify-between gap-4">

                        <div>

                            <span class="text-xs font-black uppercase tracking-[0.15em] text-[#718096]">
                                Explora por categoría
                            </span>

                            <h3 class="mt-2 text-2xl font-black text-[#172A3D]">
                                ¿Qué necesitas?
                            </h3>

                        </div>

                    </div>


                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <!-- HOGAR -->

                        <div
                            class="group rounded-2xl border border-[#D5DDE4] bg-[#FFFDF8] p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#D99A24] hover:shadow-lg">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E6EDF2] text-xl">
                                🏠
                            </div>

                            <h4 class="mt-4 font-bold text-[#172A3D]">
                                Hogar y mantenimiento
                            </h4>

                            <p class="mt-2 text-sm leading-6 text-[#718096]">
                                Reparaciones, electricidad, plomería, pintura y mantenimiento.
                            </p>

                        </div>


                        <!-- TECNOLOGÍA -->

                        <div
                            class="group rounded-2xl border border-[#D5DDE4] bg-[#FFFDF8] p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#D99A24] hover:shadow-lg">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E6EDF2] text-xl">
                                💻
                            </div>

                            <h4 class="mt-4 font-bold text-[#172A3D]">
                                Tecnología
                            </h4>

                            <p class="mt-2 text-sm leading-6 text-[#718096]">
                                Computadores, redes, soporte técnico, desarrollo y soluciones digitales.
                            </p>

                        </div>


                        <!-- PROFESIONALES -->

                        <div
                            class="group rounded-2xl border border-[#D5DDE4] bg-[#FFFDF8] p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#D99A24] hover:shadow-lg">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E6EDF2] text-xl">
                                📊
                            </div>

                            <h4 class="mt-4 font-bold text-[#172A3D]">
                                Servicios profesionales
                            </h4>

                            <p class="mt-2 text-sm leading-6 text-[#718096]">
                                Asesorías, contabilidad, consultoría, diseño y otros servicios especializados.
                            </p>

                        </div>


                        <!-- PERSONALES -->

                        <div
                            class="group rounded-2xl border border-[#D5DDE4] bg-[#FFFDF8] p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#D99A24] hover:shadow-lg">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#E6EDF2] text-xl">
                                ✨
                            </div>

                            <h4 class="mt-4 font-bold text-[#172A3D]">
                                Servicios personales
                            </h4>

                            <p class="mt-2 text-sm leading-6 text-[#718096]">
                                Educación, belleza, entrenamiento, cuidado y servicios especializados.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>

        <!-- ========================================================= -->
        <!-- CONTACTO -->
        <!-- ========================================================= -->

        <section id="contacto" class="scroll-mt-10 bg-[#F3F0E8] py-12 lg:py-16">

            <div class="mx-auto max-w-5xl px-6 lg:px-8">

                <div
                    class="rounded-[2rem] bg-[#FFFDF8] px-8 py-10 text-center shadow-lg ring-1 ring-[#DDD7CA] sm:px-10">

                    <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                        Contacto
                    </span>


                    <h2 class="mt-3 text-3xl font-black text-[#172A3D] sm:text-4xl">
                        ¿Tienes alguna pregunta?
                    </h2>


                    <div class="mx-auto mt-4 h-1 w-12 rounded-full bg-[#D99A24]">
                    </div>


                    <!-- DATOS DE CONTACTO -->

                    <div class="mt-7 flex flex-col items-center justify-center gap-4 sm:flex-row sm:gap-8">

                        <!-- CORREO -->

                        <a href="mailto:info@portaldeseret.com"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-[#526174] transition hover:text-[#D99A24]">

                            <span class="text-lg">
                                ✉
                            </span>

                            <span>
                                info@portaldeseret.com
                            </span>

                        </a>


                        <!-- WHATSAPP -->

                        <a href="https://wa.me/573112819811" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-[#526174] transition hover:text-[#D99A24]">

                            <span class="text-lg">
                                💬
                            </span>

                            <span>
                                WhatsApp: 311 281 9811
                            </span>

                        </a>

                    </div>


                    <!-- CTA -->

                    <div class="mt-7">

                        <Link :href="route('solicitud.ingreso')"
                            class="inline-flex items-center rounded-xl bg-[#D99A24] px-6 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#C58919]">

                            Solicitar acceso →

                        </Link>

                    </div>

                </div>

            </div>

        </section>
        <!-- ========================================================= -->
        <!-- CONOCE PORTAL DESERET -->
        <!-- SOBRE NOSOTROS + CÓMO FUNCIONA -->
        <!-- ========================================================= -->

        <section id="nosotros" class="scroll-mt-10 bg-[#FFFDF8] py-12 lg:py-16">

            <div class="mx-auto max-w-7xl px-6 lg:px-8">

                <div class="grid gap-10 lg:grid-cols-[1fr_1.15fr] lg:items-center">


                    <!-- ================================================= -->
                    <!-- SOBRE NOSOTROS -->
                    <!-- ================================================= -->

                    <div>

                        <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">

                            Portal Deseret

                        </span>


                        <h2 class="mt-3 max-w-xl text-3xl font-black leading-tight text-[#172A3D] sm:text-4xl">

                            Una comunidad que crece
                            <span class="text-[#D99A24]">
                                cuando compartimos.
                            </span>

                        </h2>


                        <div class="mt-4 h-1 w-12 rounded-full bg-[#D99A24]">
                        </div>


                        <p class="mt-5 max-w-xl text-base leading-7 text-[#526174]">

                            Portal Deseret es un espacio creado para conectar
                            personas, conocimientos, oportunidades y proyectos
                            dentro de nuestra comunidad.

                        </p>


                        <p class="mt-4 max-w-xl text-sm leading-6 text-[#718096]">

                            Aquí puedes encontrar servicios, oportunidades laborales,
                            productos, cursos y emprendimientos, mientras descubres
                            nuevas formas de aportar y crecer junto a otros.

                        </p>


                        <!-- CONCEPTOS -->

                        <div class="mt-6 flex flex-wrap gap-3">

                            <span
                                class="rounded-full border border-[#E5DCC9] bg-[#FFF7E5] px-4 py-2 text-xs font-bold text-[#526174]">

                                Comunidad

                            </span>


                            <span
                                class="rounded-full border border-[#E5DCC9] bg-[#FFF7E5] px-4 py-2 text-xs font-bold text-[#526174]">

                                Conexión

                            </span>


                            <span
                                class="rounded-full border border-[#E5DCC9] bg-[#FFF7E5] px-4 py-2 text-xs font-bold text-[#526174]">

                                Crecimiento

                            </span>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- CÓMO FUNCIONA -->
                    <!-- ================================================= -->

                    <div id="como-funciona" class="rounded-[2rem] border border-[#E5DCC9] bg-[#F3F0E8] p-7 sm:p-8">

                        <div>

                            <span class="text-xs font-black uppercase tracking-[0.18em] text-[#9A6A12]">

                                Cómo funciona

                            </span>


                            <h3 class="mt-2 text-2xl font-black text-[#172A3D]">

                                Así comienzas en Portal Deseret.

                            </h3>

                        </div>


                        <!-- ================================================= -->
                        <!-- PROCESO -->
                        <!-- ================================================= -->

                        <div class="mt-7 space-y-5">


                            <!-- PASO 1 -->

                            <div class="flex gap-4">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#172A3D] text-xs font-black text-white">

                                    01

                                </div>


                                <div>

                                    <h4 class="font-bold text-[#172A3D]">

                                        Regístrate y solicita acceso

                                    </h4>


                                    <p class="mt-1 text-sm leading-5 text-[#718096]">

                                        Completa tu registro y envía tu solicitud
                                        para formar parte de Portal Deseret.

                                    </p>

                                </div>

                            </div>


                            <!-- PASO 2 -->

                            <div class="flex gap-4">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#172A3D] text-xs font-black text-white">

                                    02

                                </div>


                                <div>

                                    <h4 class="font-bold text-[#172A3D]">

                                        Recibe la aprobación

                                    </h4>


                                    <p class="mt-1 text-sm leading-5 text-[#718096]">

                                        Revisaremos tu solicitud y, una vez aprobada,
                                        podrás acceder a la comunidad.

                                    </p>

                                </div>

                            </div>


                            <!-- PASO 3 -->

                            <div class="flex gap-4">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#172A3D] text-xs font-black text-white">

                                    03

                                </div>


                                <div>

                                    <h4 class="font-bold text-[#172A3D]">

                                        Explora

                                    </h4>


                                    <p class="mt-1 text-sm leading-5 text-[#718096]">

                                        Descubre servicios, empleos, productos,
                                        cursos y emprendimientos.

                                    </p>

                                </div>

                            </div>


                            <!-- PASO 4 -->

                            <div class="flex gap-4">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#172A3D] text-xs font-black text-white">

                                    04

                                </div>


                                <div>

                                    <h4 class="font-bold text-[#172A3D]">

                                        Conecta

                                    </h4>


                                    <p class="mt-1 text-sm leading-5 text-[#718096]">

                                        Encuentra personas y oportunidades
                                        que puedan ser de tu interés.

                                    </p>

                                </div>

                            </div>


                            <!-- PASO 5 -->

                            <div class="flex gap-4">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#D99A24] text-xs font-black text-white">

                                    05

                                </div>


                                <div>

                                    <h4 class="font-bold text-[#172A3D]">

                                        Publica

                                    </h4>


                                    <p class="mt-1 text-sm leading-5 text-[#718096]">

                                        Publica tus servicios, productos o
                                        emprendimiento y participa activamente
                                        en la comunidad.

                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- CTA -->

                        <div class="mt-7">

                            <Link :href="route('solicitud.ingreso')"
                                class="inline-flex items-center rounded-xl bg-[#172A3D] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-[#243B53]">

                                Solicitar acceso →

                            </Link>

                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- ========================================================= -->
        <!-- FOOTER -->
        <!-- ========================================================= -->

        <footer class="bg-[#101D2B] text-white">

            <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">

                <div class="grid gap-10 md:grid-cols-4">

                    <div class="md:col-span-2">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#D99A24] text-xl">
                                🐝
                            </div>

                            <span class="text-xl font-black">
                                Portal Deseret
                            </span>

                        </div>

                        <p class="mt-5 max-w-md text-sm leading-6 text-white/50">
                            Una plataforma para conectar personas,
                            capacidades y oportunidades.
                        </p>

                    </div>


                    <div>

                        <h3 class="text-sm font-bold text-white">
                            Explorar
                        </h3>

                        <div class="mt-5 space-y-3 text-sm text-white/50">

                            <a href="#servicios-profesionales" class="block transition hover:text-[#F1C96B]">
                                Servicios
                            </a>

                            <a href="#empleos" class="block transition hover:text-[#F1C96B]">
                                Empleos
                            </a>

                            <a href="#marketplace" class="block transition hover:text-[#F1C96B]">
                                Marketplace
                            </a>

                            <a href="#cursos" class="block transition hover:text-[#F1C96B]">
                                Cursos
                            </a>

                            <a href="/comunidad/emprendimientos" class="block transition hover:text-[#F1C96B]">
                                Emprendimientos
                            </a>

                            <a href="#clasificados" class="block transition hover:text-[#F1C96B]">
                                Clasificados
                            </a>
                            <a href="#eventos" class="block transition hover:text-[#F1C96B]">
                                Eventos
                            </a>

                        </div>

                    </div>


                    <div>

                        <h3 class="text-sm font-bold text-white">
                            Portal
                        </h3>

                        <div class="mt-5 space-y-3 text-sm text-white/50">

                            <a href="#nosotros" class="block transition hover:text-[#F1C96B]">
                                Sobre nosotros
                            </a>

                            <a href="#como-funciona" class="block transition hover:text-[#F1C96B]">
                                Cómo funciona
                            </a>

                            <a href="#contacto" class="block transition hover:text-[#F1C96B]">
                                Contacto
                            </a>

                            <a href="/terminos" class="block transition hover:text-[#D99A24]">
                                Términos y condiciones
                            </a>

                            <a href="/privacidad" class="block transition hover:text-[#D99A24]">
                                Política de privacidad
                            </a>

                        </div>

                    </div>

                </div>

                <div class="mt-12 border-t border-white/10 pt-7 text-center text-sm text-white/30">
                    <p>
                        © {{ new Date().getFullYear() }} Portal Deseret.
                        Todos los derechos reservados.
                    </p>
                    <p class="mt-2">
                        Desarrollado por Orsobruno Dev, visítanos en
                        <a href="https://orsobruno.com.co" target="_blank" rel="noopener noreferrer"
                            class="text-[#D99A24]/80 underline transition hover:text-[#D99A24]">
                            https://orsobruno.com.co
                        </a>
                    </p>
                </div>

            </div>

        </footer>

    </div>

</template>
