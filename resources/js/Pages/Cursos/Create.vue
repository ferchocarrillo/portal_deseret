<script setup>
import { computed, ref, reactive, watch } from 'vue'
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import Swal from 'sweetalert2'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'
import MapaSelector from '@/Components/MapaSelector.vue'

const page = usePage()

const props = defineProps({
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
    informacion_recepcion: {
        type: Object,
        default: null,
    }
})

// ==========================================================
// ESTADO DE LA RESIDENCIA / COORDENADAS
// ==========================================================
const informacionRecepcion = ref(props.informacion_recepcion ?? null);

const tieneCoordenadas = computed(() => {
    return !!(informacionRecepcion.value?.latitud && informacionRecepcion.value?.longitud)
})

const inputImagen = ref(null)
const vistaPrevia = ref(null)

const form = useForm({
    titulo: '',
    descripcion: '',
    imagen: null,

    tipo: 'propio',
    url_externa: '',

    es_gratuito: true,
    precio: '',

    modalidad: 'virtual',

    pais: 'Colombia',
    departamento: '',
    ciudad: '',
    direccion: '',
    enlace_virtual: '',

    // GEO
    mostrar_direccion: false,
    radio_alcance_km: 15,

    fecha_inicio: '',
    fecha_fin: '',
    fecha_limite_inscripcion: '',

    frecuencia_clases: '',
    duracion_sesion_minutos: '',
    intensidad_horaria: '',

    publicado_desde: '',
    publicado_hasta: '',

    cupos_ilimitados: true,
    cantidad_cupos: '',
    permite_lista_espera: false,

    estado: 'publicado',
})

// ==========================================================
// CORRECCIÓN MÁGICA CON IA
// ==========================================================
const cargandoIA = ref({
    titulo: false,
    descripcion: false,
})

async function aplicarCorreccionMagica(campo) {
    const textoActual = form[campo]
    if (!textoActual || !textoActual.trim()) return

    cargandoIA.value[campo] = true

    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: `curso_${campo}`,
        })

        if (response.data && response.data.improved_text) {
            form[campo] = response.data.improved_text
        }
    } catch (error) {
        console.error('Error al aplicar la corrección mágica:', error)
        alert('No fue posible conectar con el servicio de IA en este momento.')
    } finally {
        cargandoIA.value[campo] = false
    }
}

// ==========================================================
// MODERACIÓN EN TIEMPO REAL DE PALABRAS PROHIBIDAS
// ==========================================================
const alertaTitulo = ref('')
const alertaDescripcion = ref('')

function verificarPalabrasProhibidas(texto, tipo) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
        return
    }

    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find((palabra) => {
        const palabraClean = palabra.trim().toLowerCase()
        return palabraClean.length > 0 && textoLower.includes(palabraClean)
    })

    if (encontrada) {
        const mensaje = `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de publicar.`
        if (tipo === 'titulo') alertaTitulo.value = mensaje
        if (tipo === 'descripcion') alertaDescripcion.value = mensaje
    } else {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
    }
}

watch(() => form.titulo, (nuevoValor) => {
    verificarPalabrasProhibidas(nuevoValor, 'titulo')
})

watch(() => form.descripcion, (nuevoValor) => {
    verificarPalabrasProhibidas(nuevoValor, 'descripcion')
})

const esExterno = computed(() => form.tipo === 'externo')
const esGratuito = computed(() => form.es_gratuito)
const esVirtual = computed(() => form.modalidad === 'virtual')
const tieneUbicacionPresencial = computed(() =>
    ['presencial', 'hibrido'].includes(form.modalidad),
)

const intensidadCalculada = computed(() => {
    const frecuencia = Number(form.frecuencia_clases)
    const minutos = Number(form.duracion_sesion_minutos)

    if (!form.fecha_inicio || !form.fecha_fin || !frecuencia || !minutos) return ''

    const inicio = new Date(`${form.fecha_inicio}T00:00:00Z`)
    const fin = new Date(`${form.fecha_fin}T00:00:00Z`)
    const dias = Math.floor((fin - inicio) / 86400000) + 1

    if (!Number.isFinite(dias) || dias <= 0) return ''

    const semanas = Math.ceil(dias / 7)
    const horas = (frecuencia * minutos * semanas) / 60

    return Math.ceil(horas)
})

const opcionesModalidad = [
    { value: 'virtual', titulo: 'Virtual', descripcion: 'Los participantes acceden desde internet.' },
    { value: 'presencial', titulo: 'Presencial', descripcion: 'Se realiza en una ubicación física.' },
    { value: 'hibrido', titulo: 'Híbrido', descripcion: 'Combina sesiones virtuales y presenciales.' },
]

const opcionesEstado = [
    { value: 'publicado', titulo: 'Publicado', descripcion: 'El curso estará disponible según sus fechas de publicación.' },
    { value: 'pausado', titulo: 'Pausado', descripcion: 'La publicación permanecerá temporalmente inactiva.' },
    { value: 'cancelado', titulo: 'Cancelado', descripcion: 'El curso se marcará como cancelado.' },
]

watch(() => form.tipo, (tipo) => {
    if (tipo === 'propio') {
        form.url_externa = ''
        form.clearErrors('url_externa')
    }
})

watch(() => form.es_gratuito, (gratuito) => {
    if (gratuito) {
        form.precio = ''
        form.clearErrors('precio')
    }
})

watch(() => form.modalidad, (modalidad) => {
    if (modalidad === 'virtual') {
        form.direccion = ''
        form.mostrar_direccion = false
        form.radio_alcance_km = null
        form.enlace_virtual = ''
        form.pais = 'Colombia'
        form.departamento = ''
        form.ciudad = ''
        form.clearErrors(['pais', 'departamento', 'ciudad', 'direccion', 'enlace_virtual'])
    } else {
        form.enlace_virtual = ''
        if (!form.radio_alcance_km) form.radio_alcance_km = 15;
    }
})

watch(() => form.cupos_ilimitados, (ilimitados) => {
    if (ilimitados) {
        form.cantidad_cupos = ''
        form.clearErrors('cantidad_cupos')
    }
})

function seleccionarImagen(event) {
    const archivo = event.target.files?.[0]
    if (!archivo) return

    if (!archivo.type.startsWith('image/')) {
        form.setError('imagen', 'Selecciona un archivo de imagen válido.')
        event.target.value = ''
        return
    }

    if (archivo.size > 5 * 1024 * 1024) {
        form.setError('imagen', 'La imagen no puede superar los 5 MB.')
        event.target.value = ''
        return
    }

    form.clearErrors('imagen')
    form.imagen = archivo

    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
    }

    vistaPrevia.value = URL.createObjectURL(archivo)
}

function quitarImagen() {
    form.imagen = null
    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
        vistaPrevia.value = null
    }
    if (inputImagen.value) {
        inputImagen.value.value = ''
    }
    form.clearErrors('imagen')
}

function formatoTamano(bytes) {
    if (!bytes) return ''
    return `${(bytes / 1024 / 1024).toFixed(2)} MB`
}

function publicarCurso() {
    form.clearErrors('general')
    form.intensidad_horaria = intensidadCalculada.value === '' ? '' : intensidadCalculada.value

    form.post(route('cursos.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}

const fechaInicioVisual = ref('')
const fechaFinVisual = ref('')
const fechaLimiteInscripcionVisual = ref('')
const publicadoDesdeVisual = ref('')
const publicadoHastaVisual = ref('')

function formatearFechaVisual(valor) {
    const limpio = String(valor ?? '').replace(/\D/g, '').slice(0, 8)
    if (!limpio) return ''
    let mostrado = limpio
    if (limpio.length > 2) mostrado = `${limpio.slice(0, 2)}/${limpio.slice(2)}`
    if (limpio.length > 4) mostrado = `${limpio.slice(0, 2)}/${limpio.slice(2, 4)}/${limpio.slice(4)}`
    return mostrado
}

function fechaParaBackend(valor) {
    const limpio = String(valor ?? '').replace(/\D/g, '').slice(0, 8)
    if (limpio.length !== 8) return ''
    return `${limpio.slice(4, 8)}-${limpio.slice(2, 4)}-${limpio.slice(0, 2)}`
}

function formatearFechaHoraVisual(valor) {
    const limpio = String(valor ?? '').replace(/\D/g, '').slice(0, 12)
    if (!limpio) return ''
    let mostrado = formatearFechaVisual(limpio.slice(0, 8))
    if (limpio.length > 8) mostrado += ` ${limpio.slice(8, 10)}`
    if (limpio.length > 10) mostrado += `:${limpio.slice(10, 12)}`
    return mostrado
}

function fechaHoraParaBackend(valor) {
    const limpio = String(valor ?? '').replace(/\D/g, '').slice(0, 12)
    if (limpio.length !== 12) return ''
    const fecha = fechaParaBackend(limpio.slice(0, 8))
    return `${fecha}T${limpio.slice(8, 10)}:${limpio.slice(10, 12)}`
}

function actualizarFecha(campo, valor) {
    const mostrado = formatearFechaVisual(valor)
    if (campo === 'fecha_inicio') {
        fechaInicioVisual.value = mostrado
        form.fecha_inicio = fechaParaBackend(mostrado)
    }
    if (campo === 'fecha_fin') {
        fechaFinVisual.value = mostrado
        form.fecha_fin = fechaParaBackend(mostrado)
    }
}

function actualizarFechaHora(campo, valor) {
    const mostrado = formatearFechaHoraVisual(valor)
    if (campo === 'fecha_limite_inscripcion') {
        fechaLimiteInscripcionVisual.value = mostrado
        form.fecha_limite_inscripcion = fechaHoraParaBackend(mostrado)
    }
    if (campo === 'publicado_desde') {
        publicadoDesdeVisual.value = mostrado
        form.publicado_desde = fechaHoraParaBackend(mostrado)
    }
    if (campo === 'publicado_hasta') {
        publicadoHastaVisual.value = mostrado
        form.publicado_hasta = fechaHoraParaBackend(mostrado)
    }
}

// ==========================================================
// MODAL ANCLA TERRITORIAL (UBICACIÓN Y RESIDENCIA)
// ==========================================================
const mostrarModalRecepcion = ref(false);
const guardandoRecepcion = ref(false);
const obteniendoGps = ref(false);
const cargandoModal = ref(false);

const dropdownTipoViaAbierto = ref(false);
const dropdownSectorAbierto = ref(false);

const tiposVia = [
    'Calle', 'Carrera', 'Avenida', 'Avenida Calle', 'Avenida Carrera',
    'Diagonal', 'Transversal', 'Circular', 'Autopista', 'Kilómetro', 'Vereda', 'Otro'
];

const sufijosSector = ['Ninguno', 'Sur', 'Norte', 'Este', 'Oeste'];

const direccionEstandar = reactive({
    tipo: 'Calle',
    viaPrincipal: '',
    viaGeneradora: '',
    placa: '',
    sector: 'Ninguno',
});

const formRecepcion = reactive({
    pais: informacionRecepcion.value?.pais || 'Colombia',
    departamento: informacionRecepcion.value?.departamento ?? '',
    ciudad: informacionRecepcion.value?.ciudad ?? '',
    codigo_postal: informacionRecepcion.value?.codigo_postal ?? '',
    barrio: informacionRecepcion.value?.barrio ?? '',
    tipo_via: informacionRecepcion.value?.tipo_via ?? 'Calle',
    numero_via: informacionRecepcion.value?.numero_via ?? '',
    complemento: informacionRecepcion.value?.complemento ?? '',
    complemento_2: informacionRecepcion.value?.complemento_2 ?? '',
    referencia_direccion: informacionRecepcion.value?.referencia_direccion ?? '',
    latitud: informacionRecepcion.value?.latitud ?? null,
    longitud: informacionRecepcion.value?.longitud ?? null,
    telefono_contacto: informacionRecepcion.value?.telefono_contacto ?? '',
    whatsapp: informacionRecepcion.value?.whatsapp ?? '',
    correo_contacto: informacionRecepcion.value?.correo_contacto ?? '',
    medio_contacto_preferido: informacionRecepcion.value?.medio_contacto_preferido ?? '',
    instrucciones_recepcion: informacionRecepcion.value?.instrucciones_recepcion ?? '',
    permite_contacto: informacionRecepcion.value?.permite_contacto ?? true,
    permite_compartir_direccion: informacionRecepcion.value?.permite_compartir_direccion ?? false,
});

const seleccionarTipoVia = (via) => {
    direccionEstandar.tipo = via;
    formRecepcion.tipo_via = via;
    dropdownTipoViaAbierto.value = false;
};

const seleccionarSector = (sec) => {
    direccionEstandar.sector = sec;
    dropdownSectorAbierto.value = false;
};

const direccionCompletaGenerada = computed(() => {
    if (!direccionEstandar.viaPrincipal) return '';
    let dir = `${direccionEstandar.tipo} ${direccionEstandar.viaPrincipal.trim()}`;
    if (direccionEstandar.viaGeneradora) dir += ` # ${direccionEstandar.viaGeneradora.trim()}`;
    if (direccionEstandar.placa) dir += ` - ${direccionEstandar.placa.trim()}`;
    if (direccionEstandar.sector && direccionEstandar.sector !== 'Ninguno') dir += ` ${direccionEstandar.sector}`;
    return dir.trim();
});

watch(direccionCompletaGenerada, (nuevaDireccion) => {
    if (!cargandoModal.value && nuevaDireccion) {
        formRecepcion.numero_via = nuevaDireccion;
        formRecepcion.tipo_via = direccionEstandar.tipo;
    }
});

watch(
    () => [formRecepcion.ciudad, formRecepcion.departamento, direccionCompletaGenerada.value],
    () => {
        if (!cargandoModal.value && mostrarModalRecepcion.value) {
            formRecepcion.latitud = null;
            formRecepcion.longitud = null;
        }
    }
);

function parsearDireccionExistente(direccionTexto, tipoViaGuardado) {
    if (tipoViaGuardado && tiposVia.includes(tipoViaGuardado)) {
        direccionEstandar.tipo = tipoViaGuardado;
    } else {
        direccionEstandar.tipo = 'Calle';
    }
    direccionEstandar.viaPrincipal = '';
    direccionEstandar.viaGeneradora = '';
    direccionEstandar.placa = '';
    direccionEstandar.sector = 'Ninguno';

    if (!direccionTexto) return;

    const texto = direccionTexto.trim();
    for (const sec of ['Sur', 'Norte', 'Este', 'Oeste']) {
        const regexSec = new RegExp(`\\b${sec}\\b`, 'i');
        if (regexSec.test(texto)) {
            direccionEstandar.sector = sec;
            break;
        }
    }
    for (const via of tiposVia) {
        const regexVia = new RegExp(`^${via}\\b`, 'i');
        if (regexVia.test(texto)) {
            direccionEstandar.tipo = via;
            break;
        }
    }

    const regexCompleta = /(?:[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+)?\s*([0-9]+[A-Za-z\s]*)\s*#\s*([0-9]+[A-Za-z\s]*)\s*-\s*([0-9]+)/i;
    const match = texto.match(regexCompleta);

    if (match) {
        direccionEstandar.viaPrincipal = match[1]?.trim() ?? '';
        direccionEstandar.viaGeneradora = match[2]?.trim() ?? '';
        direccionEstandar.placa = match[3]?.trim() ?? '';
    } else {
        const limpia = texto.replace(new RegExp(`^${direccionEstandar.tipo}\\s*`, 'i'), '');
        direccionEstandar.viaPrincipal = limpia.replace(/\b(Sur|Norte|Este|Oeste)\b/gi, '').trim();
    }
}

function abrirModalRecepcion() {
    cargandoModal.value = true;
    dropdownTipoViaAbierto.value = false;
    dropdownSectorAbierto.value = false;

    const info = informacionRecepcion.value;
    Object.assign(formRecepcion, {
        pais: info?.pais || 'Colombia',
        departamento: info?.departamento ?? '',
        ciudad: info?.ciudad ?? '',
        codigo_postal: info?.codigo_postal ?? '',
        barrio: info?.barrio ?? '',
        tipo_via: info?.tipo_via ?? 'Calle',
        numero_via: info?.numero_via ?? '',
        complemento: info?.complemento ?? '',
        complemento_2: info?.complemento_2 ?? '',
        referencia_direccion: info?.referencia_direccion ?? '',
        latitud: info?.latitud ?? null,
        longitud: info?.longitud ?? null,
        telefono_contacto: info?.telefono_contacto ?? '',
        whatsapp: info?.whatsapp ?? '',
        correo_contacto: info?.correo_contacto ?? '',
        medio_contacto_preferido: info?.medio_contacto_preferido ?? '',
        instrucciones_recepcion: info?.instrucciones_recepcion ?? '',
        permite_contacto: info?.permite_contacto ?? true,
        permite_compartir_direccion: info?.permite_compartir_direccion ?? false,
    });

    parsearDireccionExistente(info?.numero_via, info?.tipo_via);
    mostrarModalRecepcion.value = true;
    setTimeout(() => { cargandoModal.value = false; }, 150);
}

function cerrarModalRecepcion() {
    if (guardandoRecepcion.value) return;
    dropdownTipoViaAbierto.value = false;
    dropdownSectorAbierto.value = false;
    mostrarModalRecepcion.value = false;
}

const limpiarCoordenadas = () => {
    formRecepcion.latitud = null;
    formRecepcion.longitud = null;
    Swal.fire({
        icon: 'info',
        title: 'Coordenadas reiniciadas',
        text: 'Al guardar la residencia, el sistema recalculará la ubicación exacta a partir de tu dirección estructurada.',
        timer: 2500,
        showConfirmButton: false,
    });
};

const obtenerUbicacionGPS = () => {
    if (!navigator.geolocation) {
        Swal.fire({
            icon: 'warning',
            title: 'GPS no compatible',
            text: 'Tu navegador no permite detección de ubicación satelital.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    obteniendoGps.value = true;
    navigator.geolocation.getCurrentPosition(
        (position) => {
            formRecepcion.latitud = Number(position.coords.latitude.toFixed(7));
            formRecepcion.longitud = Number(position.coords.longitude.toFixed(7));
            obteniendoGps.value = false;
            Swal.fire({
                icon: 'success',
                title: 'Coordenadas capturadas',
                text: 'Se detectó tu ubicación geográfica exitosamente.',
                timer: 2000,
                showConfirmButton: false,
            });
        },
        (error) => {
            obteniendoGps.value = false;
            let msg = 'No se pudo obtener tu ubicación actual.';
            if (error.code === error.PERMISSION_DENIED) msg = 'Permiso denegado por el navegador para acceder al GPS.';
            Swal.fire({
                icon: 'info',
                title: 'Atención',
                text: `${msg} No te preocupes, el sistema geocodificará tu dirección automáticamente al guardar.`,
                confirmButtonColor: '#172A3D',
            });
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
};

async function guardarRecepcion() {
    if (direccionCompletaGenerada.value) {
        formRecepcion.numero_via = direccionCompletaGenerada.value;
        formRecepcion.tipo_via = direccionEstandar.tipo;
    }

    if (!formRecepcion.numero_via || formRecepcion.numero_via.trim() === '') {
        Swal.fire({ icon: 'warning', title: 'Dirección requerida', text: 'Debes completar los datos de tu dirección física.', confirmButtonColor: '#172A3D' });
        return;
    }

    if (!formRecepcion.latitud || !formRecepcion.longitud) {
        Swal.fire({ icon: 'warning', title: 'Ubicación en el mapa requerida', text: 'Por favor, haz clic o arrastra el marcador en el mapa hasta tu ubicación exacta.', confirmButtonColor: '#172A3D' });
        return;
    }

    guardandoRecepcion.value = true;
    try {
        const response = await axios.post(route('perfil.informacion-recepcion.store'), formRecepcion);
        informacionRecepcion.value = response.data.informacion;
        mostrarModalRecepcion.value = false;

        router.reload({ only: ['informacion_recepcion'] });

        Swal.fire({
            icon: 'success',
            title: 'Residencia registrada',
            text: 'La dirección y su ubicación exacta en el mapa han sido guardadas.',
            confirmButtonColor: '#172A3D',
        });
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error al guardar', text: error.response?.data?.message ?? 'Ocurrió un error al guardar la información.', confirmButtonColor: '#172A3D' });
    } finally {
        guardandoRecepcion.value = false;
    }
}
</script>

<template>

    <Head title="Crear curso | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('cursos.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Volver a los cursos
                    </Link>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">Portal
                                    Deseret</span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">Crear un curso
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium max-w-2xl">
                                Comparte tus conocimientos, crea una experiencia de aprendizaje o recomienda un curso
                                disponible en otra plataforma.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-orange-50 rounded-xl p-3 mt-2">
                <form class="space-y-6" @submit.prevent="publicarCurso">
                    <!-- 1. INFORMACIÓN GENERAL -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">1. Información general</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Describe el curso y define
                                cómo se ofrecerá.</p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="titulo"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Nombre del curso <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                        :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <input id="titulo" v-model="form.titulo" type="text" maxlength="180" required
                                    placeholder="Ej. Introducción al desarrollo web con Laravel"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />
                                <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">{{ alertaTitulo
                                    }}</p>
                                <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">{{
                                    form.errors.titulo }}</p>
                            </div>

                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3">
                                    <label for="descripcion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                            :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition disabled:opacity-50">
                                            <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                            <span v-else>✨</span>
                                            <span>Corrección Mágica</span>
                                        </button>
                                        <span class="text-xs text-slate-400 font-medium">{{ form.descripcion.length
                                            }}/5000</span>
                                    </div>
                                </div>
                                <textarea id="descripcion" v-model="form.descripcion" rows="6" maxlength="5000" required
                                    placeholder="Explica qué aprenderán los participantes, qué temas se tratarán y a quién está dirigido..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>
                                <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">{{
                                    alertaDescripcion }}</p>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">{{
                                    form.errors.descripcion }}</p>
                            </div>

                            <div>
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">Tipo
                                    de curso <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.tipo === 'propio' ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.tipo" type="radio" value="propio"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Curso
                                                propio</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Una formación
                                                que organizas o impartes.</span>
                                        </span>
                                    </label>
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.tipo === 'externo' ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.tipo" type="radio" value="externo"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Curso
                                                externo</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Una formación
                                                disponible en otra plataforma.</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="esExterno">
                                <label for="url_externa"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Enlace
                                    del curso <span class="text-red-500">*</span></label>
                                <input id="url_externa" v-model="form.url_externa" type="url" maxlength="2048" required
                                    placeholder="https://..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.url_externa }" />
                                <p class="mt-1 text-xs text-slate-500 font-medium">Ingresa la dirección donde las
                                    personas pueden consultar o realizar el curso.</p>
                            </div>
                        </div>
                    </section>

                    <!-- 2. IMAGEN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">2. Imagen del curso</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Utiliza una imagen
                                representativa de la formación.</p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div v-if="vistaPrevia"
                                class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                <img :src="vistaPrevia" alt="Vista previa de la imagen del curso"
                                    class="max-h-96 w-full object-contain" />
                            </div>

                            <div>
                                <label for="imagen"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Seleccionar
                                    imagen <span class="text-red-500">*</span></label>
                                <input id="imagen" ref="inputImagen" type="file" accept="image/*" required
                                    class="block w-full rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-600 file:mr-4 file:border-0 file:bg-orange-50 file:px-4 file:py-3 file:font-bold file:text-orange-900 hover:file:bg-orange-100"
                                    @change="seleccionarImagen" />
                                <p class="mt-2 text-xs text-slate-500 font-medium">Formatos de imagen habituales. Tamaño
                                    máximo: 5 MB.</p>
                                <button v-if="vistaPrevia" type="button"
                                    class="mt-3 text-xs font-bold text-red-600 hover:text-red-800 transition"
                                    @click="quitarImagen">Quitar imagen seleccionada</button>
                            </div>
                        </div>
                    </section>

                    <!-- 3. MODALIDAD Y UBICACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">3. Modalidad y ubicación</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Define dónde y cómo se
                                desarrollará el curso.</p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div>
                                <label
                                    class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">Modalidad
                                    <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <label v-for="opcion in opcionesModalidad" :key="opcion.value"
                                        class="flex cursor-pointer gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.modalidad === opcion.value ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.modalidad" type="radio" :value="opcion.value"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{
                                                opcion.titulo }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">{{
                                                opcion.descripcion }}</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="esVirtual">
                                <label for="enlace_virtual"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Enlace
                                    de acceso (opcional)</label>
                                <input id="enlace_virtual" v-model="form.enlace_virtual" type="url" maxlength="2048"
                                    placeholder="https://..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                                <p class="mt-1 text-xs text-slate-500 font-medium">Puedes agregar el enlace de la
                                    plataforma donde se realizará el curso.</p>
                            </div>

                            <!-- UBICACIÓN FÍSICA Y ALCANCE -->
                            <div v-if="tieneUbicacionPresencial" class="space-y-6">
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-[#172A3D]">Ubicación del curso</h3>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Selecciona el país, la
                                        división administrativa y la ciudad.</p>
                                </div>

                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                    <UbicacionSelector v-model:pais="form.pais" v-model:departamento="form.departamento"
                                        v-model:ciudad="form.ciudad" />
                                </div>

                                <div>
                                    <label for="direccion"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Dirección
                                        o lugar <span class="font-normal text-slate-400">(opcional)</span></label>
                                    <input id="direccion" v-model="form.direccion" type="text" maxlength="255"
                                        placeholder="Ej. Salón comunal, auditorio o dirección"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />

                                    <label
                                        class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <input v-model="form.mostrar_direccion" type="checkbox"
                                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-slate-700">Mostrar la
                                                dirección exacta públicamente</span>
                                        </span>
                                    </label>

                                    <!-- ALCANCE GEOGRÁFICO Y ANCLA TERRITORIAL -->
                                    <div class="mt-6 border-t border-slate-200 pt-6">
                                        <div v-if="!tieneCoordenadas"
                                            class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm">
                                            <div class="flex gap-4">
                                                <div
                                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 font-bold text-lg">
                                                    !</div>
                                                <div>
                                                    <h3 class="font-bold text-red-900">Ubicación exacta requerida</h3>
                                                    <p
                                                        class="mt-1 text-xs sm:text-sm text-red-700 font-medium leading-relaxed">
                                                        Para establecer el rango de este curso presencial o híbrido,
                                                        necesitamos conocer tu ubicación exacta como Ancla Territorial.
                                                    </p>
                                                    <button type="button" @click="abrirModalRecepcion"
                                                        class="mt-3 inline-block text-xs sm:text-sm font-bold text-red-800 underline underline-offset-4 transition hover:text-red-600">
                                                        Registrar ubicación en el mapa →
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else class="rounded-xl border border-orange-200 bg-orange-50/60 p-5">
                                            <div class="flex items-center justify-between">
                                                <label
                                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                    Radio de alcance del curso: <span
                                                        class="text-orange-600 text-sm font-black">{{
                                                        form.radio_alcance_km }} km</span>
                                                </label>
                                                <button type="button" @click="abrirModalRecepcion"
                                                    class="text-xs font-bold text-orange-600 hover:underline">
                                                    Modificar ubicación base
                                                </button>
                                            </div>
                                            <p class="mt-1 mb-4 text-xs leading-5 text-slate-600 font-medium">Solo los
                                                estudiantes en este rango podrán ver y acceder a la inscripción del
                                                curso.</p>
                                            <div class="flex items-center gap-4">
                                                <span class="text-xs font-bold text-slate-500">1 km</span>
                                                <input type="range" v-model="form.radio_alcance_km" min="1" max="100"
                                                    step="1"
                                                    class="w-full h-2 bg-slate-300 rounded-lg appearance-none cursor-pointer accent-orange-600" />
                                                <span class="text-xs font-bold text-slate-500">100 km</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 4. PRECIO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">4. Precio e inscripción</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Indica si el curso es gratuito
                                y si tiene cupos limitados.</p>
                        </div>
                        <div class="space-y-5 p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4 rounded-xl border p-4 shadow-xs"
                                :class="form.es_gratuito ? 'border-orange-300 bg-orange-50/50' : 'border-slate-300 bg-white'">
                                <div>
                                    <p class="font-bold text-[#172A3D]">Curso gratuito</p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Activa esta opción si
                                        no tiene costo de inscripción.</p>
                                </div>
                                <input v-model="form.es_gratuito" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>

                            <div v-if="!esGratuito">
                                <label for="precio"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Precio
                                    en pesos colombianos (COP) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">$</span>
                                    <input id="precio" v-model="form.precio" type="number" min="0" step="0.01" required
                                        placeholder="0"
                                        class="w-full rounded-xl border border-slate-300 py-3 pl-9 pr-4 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4 rounded-xl border p-4 shadow-xs"
                                :class="form.cupos_ilimitados ? 'border-orange-300 bg-orange-50/50' : 'border-slate-300 bg-white'">
                                <div>
                                    <p class="font-bold text-[#172A3D]">Cupos ilimitados</p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Desactiva esta opción
                                        si existe un límite de participantes.</p>
                                </div>
                                <input v-model="form.cupos_ilimitados" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>

                            <div v-if="!form.cupos_ilimitados">
                                <label for="cantidad_cupos"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Cantidad
                                    de cupos <span class="text-red-500">*</span></label>
                                <input id="cantidad_cupos" v-model="form.cantidad_cupos" type="number" min="1" step="1"
                                    required placeholder="Ej. 30"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 rounded-xl border border-slate-300 bg-white p-4 shadow-xs">
                                <div>
                                    <p class="font-bold text-[#172A3D]">Permitir lista de espera</p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Permite registrar
                                        interesados cuando se completen los cupos.</p>
                                </div>
                                <input v-model="form.permite_lista_espera" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>
                        </div>
                    </section>

                    <!-- 5. DURACIÓN E INTENSIDAD HORARIA -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">5. Duración e intensidad horaria</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Indica la frecuencia y
                                duración de las clases. El total de horas se estima automáticamente.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3 sm:p-6">
                            <div>
                                <label for="frecuencia_clases"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Clases
                                    por semana</label>
                                <input id="frecuencia_clases" v-model="form.frecuencia_clases" type="number" min="1"
                                    max="255" step="1" placeholder="Ej. 2"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div>
                                <label for="duracion_sesion_minutos"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Minutos
                                    por clase</label>
                                <input id="duracion_sesion_minutos" v-model="form.duracion_sesion_minutos" type="number"
                                    min="1" max="1440" step="1" placeholder="Ej. 90"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div>
                                <label for="intensidad_horaria"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Total
                                    de horas</label>
                                <input id="intensidad_horaria" :value="intensidadCalculada" type="number" readonly
                                    placeholder="Calculado automático"
                                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none" />
                                <p class="mt-1 text-xs text-slate-500 font-medium">Estimación basada en semanas
                                    calendario.</p>
                            </div>
                        </div>
                    </section>

                    <!-- 6. FECHAS -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">6. Fechas del curso</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Define cuándo comienza, cuándo
                                termina y hasta cuándo pueden inscribirse.</p>
                        </div>
                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <div>
                                <label for="fecha_inicio"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Fecha
                                    de inicio</label>
                                <input id="fecha_inicio" :value="fechaInicioVisual" type="text" inputmode="numeric"
                                    maxlength="10" placeholder="dd/mm/yyyy" autocomplete="off"
                                    @input="actualizarFecha('fecha_inicio', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div>
                                <label for="fecha_fin"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Fecha
                                    de finalización</label>
                                <input id="fecha_fin" :value="fechaFinVisual" type="text" inputmode="numeric"
                                    maxlength="10" placeholder="dd/mm/yyyy" autocomplete="off"
                                    @input="actualizarFecha('fecha_fin', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div class="sm:col-span-2">
                                <label for="fecha_limite_inscripcion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Fecha
                                    límite de inscripción</label>
                                <input id="fecha_limite_inscripcion" :value="fechaLimiteInscripcionVisual" type="text"
                                    inputmode="numeric" maxlength="16" placeholder="dd/mm/yyyy hh:mm" autocomplete="off"
                                    @input="actualizarFechaHora('fecha_limite_inscripcion', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>
                        </div>
                    </section>

                    <!-- 7. VIGENCIA DE LA PUBLICACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">7. Vigencia de la publicación</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Estas fechas controlan cuándo
                                se muestra la publicación. No reemplazan las fechas del curso.</p>
                        </div>
                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <div>
                                <label for="publicado_desde"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Mostrar
                                    desde</label>
                                <input id="publicado_desde" :value="publicadoDesdeVisual" type="text"
                                    inputmode="numeric" maxlength="16" placeholder="dd/mm/yyyy hh:mm" autocomplete="off"
                                    @input="actualizarFechaHora('publicado_desde', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>

                            <div>
                                <label for="publicado_hasta"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Mostrar
                                    hasta</label>
                                <input id="publicado_hasta" :value="publicadoHastaVisual" type="text"
                                    inputmode="numeric" maxlength="16" placeholder="dd/mm/yyyy hh:mm" autocomplete="off"
                                    @input="actualizarFechaHora('publicado_hasta', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>
                        </div>
                    </section>

                    <!-- 8. ESTADO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">8. Estado del curso</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Selecciona cómo quedará el
                                curso al guardarlo.</p>
                        </div>
                        <div class="space-y-3 p-5 sm:p-6">
                            <label v-for="opcion in opcionesEstado" :key="opcion.value"
                                class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                :class="form.estado === opcion.value ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                <input v-model="form.estado" type="radio" :value="opcion.value"
                                    class="mt-1 h-4 w-4 accent-orange-600" />
                                <span>
                                    <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{ opcion.titulo
                                        }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500 font-medium">{{ opcion.descripcion
                                        }}</span>
                                </span>
                            </label>

                            <div
                                class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-xs sm:text-sm leading-6 text-orange-950 font-medium shadow-xs">
                                <strong>Ten en cuenta:</strong> el estado «Finalizado» lo asignará el sistema cuando
                                corresponda, de acuerdo con la fecha de finalización del curso.
                            </div>
                        </div>
                    </section>

                    <div v-if="form.hasErrors"
                        class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-2xl shadow-red-500/15">
                        <p class="font-bold text-red-900">Revisa la información del formulario.</p>
                        <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">Hay campos que necesitan corrección
                            antes de guardar.</p>
                    </div>

                    <!-- ACCIONES -->
                    <div class="flex flex-col-reverse gap-3 pb-12 sm:flex-row sm:justify-end">
                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Cancelar
                        </Link>
                        <button type="submit"
                            :disabled="form.processing || Boolean(alertaTitulo) || Boolean(alertaDescripcion) || (!tieneCoordenadas && (form.modalidad === 'presencial' || form.modalidad === 'hibrido'))"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg v-if="form.processing" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" />
                            </svg>
                            <span>{{ form.processing ? 'Creando curso...' : 'Crear curso' }}</span>
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </AuthenticatedLayout>

    <!-- ============================================================= -->
    <!-- MODAL RESIDENCIA Y GEORREFERENCIACIÓN -->
    <!-- ============================================================= -->
    <Teleport to="body">
        <div v-if="mostrarModalRecepcion"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#172A3D]/60 px-4 py-6 backdrop-blur-sm">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-[1.5rem] bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-start justify-between border-b border-[#E9EDF0] pb-4">
                    <div>
                        <span class="block text-[8px] font-black uppercase tracking-[0.18em] text-[#9A6A12]">Ancla
                            Territorial</span>
                        <h2 class="text-xl font-black text-[#172A3D]">Información de residencia</h2>
                    </div>
                    <button type="button"
                        class="small-button shrink-0 border border-slate-200 bg-white px-3 py-1 text-slate-700"
                        @click="cerrarModalRecepcion">×</button>
                </div>

                <form class="space-y-4" @submit.prevent="guardarRecepcion">
                    <UbicacionSelector v-model:pais="formRecepcion.pais"
                        v-model:departamento="formRecepcion.departamento" v-model:ciudad="formRecepcion.ciudad" />

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">Dirección estructurada *</label>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nomenclatura
                                oficial</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-6">
                            <div class="relative col-span-2 sm:col-span-2">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Tipo de
                                    vía</label>
                                <button type="button"
                                    @click="dropdownTipoViaAbierto = !dropdownTipoViaAbierto; dropdownSectorAbierto = false"
                                    class="mt-1 flex w-full items-center justify-between rounded-xl bg-[#172A3D] px-4 py-3 text-left text-sm font-bold text-white shadow-sm border border-[#172A3D] transition hover:bg-[#233C55]">
                                    <span>{{ direccionEstandar.tipo || 'Selecciona' }}</span>
                                    <span class="text-xs text-white">▼</span>
                                </button>
                                <div v-if="dropdownTipoViaAbierto"
                                    class="absolute left-0 top-full z-[120] mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-[#172A3D] bg-[#172A3D] p-1.5 shadow-2xl">
                                    <button v-for="via in tiposVia" :key="via" type="button"
                                        @click="seleccionarTipoVia(via)"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition hover:bg-[#233C55] text-slate-200 hover:text-white"
                                        :class="direccionEstandar.tipo === via ? 'bg-[#D99A24] text-white' : ''">
                                        <span>{{ via }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Nº /
                                    Vía</label>
                                <input v-model="direccionEstandar.viaPrincipal" type="text" placeholder="Ej. 85G"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">#
                                    Cruce</label>
                                <input v-model="direccionEstandar.viaGeneradora" type="text" placeholder="Ej. 24C"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">-
                                    Placa</label>
                                <input v-model="direccionEstandar.placa" type="text" placeholder="Ej. 50"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-900 border border-slate-300"
                                    required />
                            </div>
                            <div class="relative col-span-2 sm:col-span-1">
                                <label
                                    class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Sector</label>
                                <button type="button"
                                    @click="dropdownSectorAbierto = !dropdownSectorAbierto; dropdownTipoViaAbierto = false"
                                    class="mt-1 flex w-full items-center justify-between rounded-xl bg-[#172A3D] px-4 py-3 text-left text-sm font-bold text-white shadow-sm border border-[#172A3D] transition hover:bg-[#233C55]">
                                    <span>{{ direccionEstandar.sector || 'Ninguno' }}</span>
                                    <span class="text-xs text-white">▼</span>
                                </button>
                                <div v-if="dropdownSectorAbierto"
                                    class="absolute left-0 top-full z-[120] mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-[#172A3D] bg-[#172A3D] p-1.5 shadow-2xl">
                                    <button v-for="sec in sufijosSector" :key="sec" type="button"
                                        @click="seleccionarSector(sec)"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition hover:bg-[#233C55] text-slate-200 hover:text-white"
                                        :class="direccionEstandar.sector === sec ? 'bg-[#D99A24] text-white' : ''">
                                        <span>{{ sec }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="direccionCompletaGenerada"
                            class="flex items-center justify-between rounded-xl border border-orange-200 bg-[#FFFDF8] px-3 py-2 text-xs">
                            <span class="font-medium text-slate-600">Dirección canónica a guardar:</span>
                            <strong class="font-black text-slate-950">{{ direccionCompletaGenerada }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Barrio</label>
                            <input v-model="formRecepcion.barrio" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D]"
                                placeholder="Ej. Modelia" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Complemento (opcional)</label>
                            <input v-model="formRecepcion.complemento" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D]"
                                placeholder="Ej. Torre 2 Apto 401" />
                        </div>
                    </div>

                    <div class="rounded-2xl border border-[#E8DDBF] bg-[#FFFDF8] p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-[#9A6A12]">📍
                                    Georreferenciación Exacta</h4>
                                <p class="mt-1 text-xs text-[#718096]">Verifica tu ubicación en el mapa interactivo o
                                    captúrala con tu GPS satelital.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button v-if="formRecepcion.latitud || formRecepcion.longitud" type="button"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-[#846000]"
                                    @click="limpiarCoordenadas" title="Borrar coordenadas actuales para recalcular">
                                    🔄 Recalcular
                                </button>
                                <button type="button"
                                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700"
                                    :disabled="obteniendoGps" @click="obtenerUbicacionGPS">
                                    🛰️ {{ obteniendoGps ? 'Detectando GPS...' : 'Usar mi GPS actual' }}
                                </button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <MapaSelector v-model:latitud="formRecepcion.latitud"
                                v-model:longitud="formRecepcion.longitud" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-[#E9EDF0] pt-4">
                        <button type="button"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700"
                            @click="cerrarModalRecepcion">Cancelar</button>
                        <button type="submit" class="rounded-xl bg-[#D99A24] px-5 py-2.5 text-xs font-black text-white"
                            :disabled="guardandoRecepcion">
                            {{ guardandoRecepcion ? 'Guardando...' : 'Guardar residencia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>