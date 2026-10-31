<script setup>
import { computed, ref, reactive, watch } from 'vue'
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'
import axios from 'axios'
import Swal from 'sweetalert2'
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
    categoria: '',
    modalidad: 'presencial',
    precio_tipo: 'a_convenir',
    precio: '',
    moneda: 'COP',
    imagen_tipo: 'perfil',
    imagen: null,
    pais: 'Colombia',
    departamento: '',
    ciudad: '',
    direccion: '',
    mostrar_direccion: false,
    radio_alcance_km: 15, // Por defecto
})

const categorias = [
    'Tecnología e informática',
    'Diseño y creatividad',
    'Educación y formación',
    'Reparaciones y mantenimiento',
    'Construcción y hogar',
    'Belleza y cuidado personal',
    'Salud y bienestar',
    'Transporte y logística',
    'Eventos y fotografía',
    'Servicios profesionales',
    'Servicios para empresas',
    'Otros servicios',
]

const modalidadesPrecio = [
    { value: 'hora', label: 'Por hora' },
    { value: 'dia', label: 'Por día' },
    { value: 'servicio', label: 'Por servicio' },
    { value: 'proyecto', label: 'Por proyecto' },
    { value: 'fijo', label: 'Precio fijo' },
    { value: 'a_convenir', label: 'A convenir' },
]

const precioEsConvenido = computed(() => form.precio_tipo === 'a_convenir')

watch(() => form.modalidad, (modalidad) => {
    if (modalidad === 'remoto') {
        form.direccion = ''
        form.mostrar_direccion = false
        form.radio_alcance_km = null
    } else {
        if (!form.radio_alcance_km) form.radio_alcance_km = 15;
    }
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
            context_type: `servicio_${campo}`,
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

// ==========================================================
// MANEJO DE IMÁGENES
// ==========================================================
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
    form.imagen_tipo = 'archivo'
    form.imagen = archivo

    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
    }
    vistaPrevia.value = URL.createObjectURL(archivo)
}

function usarFotoPerfil() {
    form.imagen_tipo = 'perfil'
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

function publicarServicio() {
    form.post(route('servicios.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}

function formatoTamano(bytes) {
    if (!bytes) return ''
    return `${(bytes / 1024 / 1024).toFixed(2)} MB`
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

// Generador de dirección canónica
const direccionCompletaGenerada = computed(() => {
    if (!direccionEstandar.viaPrincipal) return '';
    let dir = `${direccionEstandar.tipo} ${direccionEstandar.viaPrincipal.trim()}`;
    if (direccionEstandar.viaGeneradora) {
        dir += ` # ${direccionEstandar.viaGeneradora.trim()}`;
    }
    if (direccionEstandar.placa) {
        dir += ` - ${direccionEstandar.placa.trim()}`;
    }
    if (direccionEstandar.sector && direccionEstandar.sector !== 'Ninguno') {
        dir += ` ${direccionEstandar.sector}`;
    }
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
        Swal.fire({ icon: 'warning', title: 'Ubicación en el mapa requerida', text: 'Por favor, haz clic o arrastra el marcador en el mapa hasta tu ubicación exacta, o usa el botón "Usar mi GPS actual".', confirmButtonColor: '#172A3D' });
        return;
    }

    guardandoRecepcion.value = true;
    try {
        const response = await axios.post(route('perfil.informacion-recepcion.store'), formRecepcion);
        informacionRecepcion.value = response.data.informacion;
        mostrarModalRecepcion.value = false;

        // Recargar los props de Inertia silenciosamente para actualizar validaciones globales
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

    <Head title="Publicar un servicio | Servicios" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">
            <!-- =====================================================
                 ENCABEZADO CON TONO VERDE / ESMERALDA TRANSLÚCIDO
            ====================================================== -->
            <section
                class="border-b border-emerald-200/80 bg-emerald-100/25 backdrop-blur-md shadow-lg shadow-emerald-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('servicios.index')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Volver al catálogo
                    </Link>

                    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-900">
                                    Portal Deseret
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Publica tu servicio
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium max-w-2xl">
                                Comparte lo que sabes hacer con la comunidad. Los miembros interesados podrán
                                contactarte directamente a través del chat de la plataforma.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 CONTENIDO PRINCIPAL / FORMULARIO
            ====================================================== -->
            <div class="py-8">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 bg-emerald-50 rounded-xl p-3">
                    <form class="space-y-6" @submit.prevent="publicarServicio">
                        <!-- 1. INFORMACIÓN GENERAL -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15 mt-4">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">1. Información del servicio</h2>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Describe claramente lo que
                                    ofreces.</p>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label for="titulo"
                                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Nombre del servicio <span class="text-red-500">*</span>
                                        </label>
                                        <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                            :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition disabled:opacity-50">
                                            <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                            <span v-else>✨</span>
                                            <span>Corrección Mágica</span>
                                        </button>
                                    </div>
                                    <input id="titulo" v-model="form.titulo" type="text" maxlength="150" required
                                        placeholder="Ej. Instalación y reparación de redes eléctricas"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                                        :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />

                                    <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">{{
                                        alertaTitulo }}</p>
                                    <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">{{
                                        form.errors.titulo }}</p>
                                </div>

                                <div>
                                    <label for="categoria"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>
                                    <select id="categoria" v-model="form.categoria" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                                        :class="{ 'border-red-500': form.errors.categoria }">
                                        <option value="" disabled>Selecciona una categoría</option>
                                        <option v-for="categoria in categorias" :key="categoria" :value="categoria">{{
                                            categoria }}</option>
                                    </select>
                                    <p v-if="form.errors.categoria" class="mt-1 text-xs text-red-600 font-medium">{{
                                        form.errors.categoria }}</p>
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
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition disabled:opacity-50">
                                                <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                                <span v-else>✨</span>
                                                <span>Corrección Mágica</span>
                                            </button>
                                            <span class="text-xs text-slate-400 font-medium">{{ form.descripcion.length
                                                }}/5000</span>
                                        </div>
                                    </div>
                                    <textarea id="descripcion" v-model="form.descripcion" rows="6" maxlength="5000"
                                        required
                                        placeholder="Explica qué servicio ofreces, qué incluye, tu experiencia y cualquier detalle importante..."
                                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                                        :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>
                                    <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">{{
                                        alertaDescripcion }}</p>
                                    <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">{{
                                        form.errors.descripcion }}</p>
                                </div>

                                <!-- MODALIDAD -->
                                <div>
                                    <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Modalidad del servicio <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <label v-for="opcion in [
                                            { value: 'presencial', titulo: 'Presencial', descripcion: 'Atención en una ubicación física' },
                                            { value: 'remoto', titulo: 'Remoto', descripcion: 'Atención a distancia' },
                                            { value: 'hibrido', titulo: 'Híbrido', descripcion: 'Presencial y remoto' },
                                        ]" :key="opcion.value"
                                            class="flex cursor-pointer gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                            :class="form.modalidad === opcion.value
                                                ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md'
                                                : 'border-slate-300 hover:border-emerald-300'">
                                            <input v-model="form.modalidad" type="radio" :value="opcion.value"
                                                class="mt-1 h-4 w-4 accent-emerald-700" />
                                            <span>
                                                <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{
                                                    opcion.titulo }}</span>
                                                <span
                                                    class="mt-0.5 block text-xs leading-relaxed text-slate-500 font-medium">{{
                                                    opcion.descripcion }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <p v-if="form.errors.modalidad" class="mt-1 text-xs text-red-600 font-medium">{{
                                        form.errors.modalidad }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- 2. PRECIO -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">2. Precio del servicio</h2>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Indica cómo cobras por tu
                                    trabajo.</p>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div>
                                    <label for="precio_tipo"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Modalidad de cobro <span class="text-red-500">*</span>
                                    </label>
                                    <select id="precio_tipo" v-model="form.precio_tipo" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        <option v-for="opcion in modalidadesPrecio" :key="opcion.value"
                                            :value="opcion.value">
                                            {{ opcion.label }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="!precioEsConvenido" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="precio"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Valor <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                            <input id="precio" v-model="form.precio" type="number" min="0" step="0.01"
                                                required placeholder="0"
                                                class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-9 pr-4 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                                                :class="{ 'border-red-500': form.errors.precio }" />
                                        </div>
                                        <p v-if="form.errors.precio" class="mt-1 text-xs text-red-600 font-medium">{{
                                            form.errors.precio }}</p>
                                    </div>

                                    <div>
                                        <label for="moneda"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Moneda</label>
                                        <select id="moneda" v-model="form.moneda"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <option value="COP">COP — Peso colombiano</option>
                                            <option value="USD">USD — Dólar estadounidense</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-else class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-xs">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-0.5 text-base font-bold text-amber-700">i</span>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-amber-900">Precio a convenir</p>
                                            <p class="mt-1 text-xs leading-5 text-amber-800 font-medium">Las personas
                                                interesadas acordarán el valor directamente contigo a través del chat.
                                                No se mostrará una cifra en el catálogo.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 3. IMAGEN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">3. Imagen del servicio</h2>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Puedes utilizar tu foto de
                                    perfil o subir una imagen promocional.</p>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.imagen_tipo === 'perfil' ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md' : 'border-slate-300 hover:border-emerald-300'">
                                        <input v-model="form.imagen_tipo" type="radio" value="perfil"
                                            class="mt-1 h-4 w-4 accent-emerald-700" @change="usarFotoPerfil" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Usar foto de
                                                perfil</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Se utilizará
                                                la foto de tu cuenta.</span>
                                        </span>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.imagen_tipo === 'archivo' ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md' : 'border-slate-300 hover:border-emerald-300'">
                                        <input v-model="form.imagen_tipo" type="radio" value="archivo"
                                            class="mt-1 h-4 w-4 accent-emerald-700" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Subir
                                                imagen</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Logo, tarjeta
                                                o imagen promocional.</span>
                                        </span>
                                    </label>
                                </div>

                                <div v-if="form.imagen_tipo === 'archivo'">
                                    <label for="imagen"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Selecciona
                                        una imagen <span class="text-red-500">*</span></label>
                                    <input id="imagen" ref="inputImagen" type="file" accept="image/*"
                                        class="block w-full rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-600 file:mr-4 file:border-0 file:bg-emerald-50 file:px-4 file:py-3 file:font-bold file:text-emerald-900 hover:file:bg-emerald-100"
                                        @change="seleccionarImagen" />
                                    <p class="mt-2 text-xs text-slate-500 font-medium">Formatos de imagen admitidos.
                                        Tamaño máximo: 5 MB.</p>
                                    <p v-if="form.errors.imagen" class="mt-1 text-xs text-red-600 font-medium">{{
                                        form.errors.imagen }}</p>

                                    <div v-if="vistaPrevia"
                                        class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                                        <img :src="vistaPrevia" alt="Vista previa de la imagen"
                                            class="max-h-80 w-full bg-slate-50 object-contain" />
                                        <div
                                            class="flex items-center justify-between gap-3 border-t border-slate-100 px-4 py-3 bg-white">
                                            <div class="min-w-0">
                                                <p class="truncate text-xs sm:text-sm font-bold text-slate-700">{{
                                                    form.imagen?.name }}</p>
                                                <p class="text-[11px] text-slate-500 font-medium">{{
                                                    formatoTamano(form.imagen?.size) }}</p>
                                            </div>
                                            <button type="button"
                                                class="text-xs font-bold text-red-600 hover:text-red-800 transition"
                                                @click="usarFotoPerfil">Quitar</button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else
                                    class="rounded-xl bg-slate-50 p-4 text-xs sm:text-sm text-slate-600 font-medium border border-slate-200">
                                    La foto de perfil se tomará de la información de tu usuario. No necesitas subirla
                                    nuevamente.
                                </div>
                            </div>
                        </section>

                        <!-- 4. UBICACIÓN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">4. Ubicación de la oferta</h2>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Indica dónde prestas el
                                    servicio, si aplica.</p>
                            </div>

                            <div class="p-5 sm:p-6 space-y-6">
                                <div v-if="form.modalidad">
                                    <UbicacionSelector v-model:pais="form.pais" v-model:departamento="form.departamento"
                                        v-model:ciudad="form.ciudad" />
                                </div>
                                <div v-if="form.modalidad === 'presencial' || form.modalidad === 'hibrido'">
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Dirección
                                        del servicio <span class="font-normal text-slate-400">(opcional)</span></label>
                                    <input v-model="form.direccion" type="text"
                                        placeholder="Ej. Carrera 10 # 20-30, Madrid"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />

                                    <label
                                        class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <input v-model="form.mostrar_direccion" type="checkbox"
                                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700" />
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
                                                        Para limitar el alcance de esta publicación, necesitamos conocer
                                                        tu ubicación exacta como Ancla Territorial.
                                                    </p>
                                                    <button type="button" @click="abrirModalRecepcion"
                                                        class="mt-3 inline-block text-xs sm:text-sm font-bold text-red-800 underline underline-offset-4 transition hover:text-red-600">
                                                        Registrar ubicación en el mapa →
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-5">
                                            <div class="flex items-center justify-between">
                                                <label
                                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                    Radio de alcance del servicio: <span
                                                        class="text-emerald-700 text-sm font-black">{{
                                                        form.radio_alcance_km }} km</span>
                                                </label>
                                                <button type="button" @click="abrirModalRecepcion"
                                                    class="text-xs font-bold text-emerald-700 hover:underline">
                                                    Modificar ubicación base
                                                </button>
                                            </div>
                                            <p class="mt-1 mb-4 text-xs leading-5 text-slate-600 font-medium">Los
                                                usuarios podrán ver si prestas servicios en su zona en base a esta
                                                distancia.</p>
                                            <div class="flex items-center gap-4">
                                                <span class="text-xs font-bold text-slate-500">1 km</span>
                                                <input type="range" v-model="form.radio_alcance_km" min="1" max="100"
                                                    step="1"
                                                    class="w-full h-2 bg-slate-300 rounded-lg appearance-none cursor-pointer accent-emerald-700" />
                                                <span class="text-xs font-bold text-slate-500">100 km</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- ERROR GENERAL -->
                        <div v-if="form.hasErrors"
                            class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-lg shadow-red-500/10">
                            <p class="font-bold text-red-900">Revisa la información del formulario.</p>
                            <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">Hay campos que necesitan
                                corrección antes de publicar el servicio.</p>
                        </div>

                        <!-- ACCIONES -->
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-12">
                            <Link :href="route('servicios.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Cancelar
                            </Link>

                            <button type="submit"
                                :disabled="form.processing || Boolean(alertaTitulo) || Boolean(alertaDescripcion) || (!tieneCoordenadas && (form.modalidad === 'presencial' || form.modalidad === 'hibrido'))"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-emerald-500/20 transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg v-if="form.processing" class="h-5 w-5 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" />
                                </svg>
                                {{ form.processing ? 'Publicando...' : 'Publicar servicio' }}
                            </button>
                        </div>
                        <p
                            class="bg-white border border-red-200 rounded-xl text-center text-xs leading-relaxed text-slate-400 font-medium pb-6">
                            Al publicar, confirmas que la información es correcta y que los miembros interesados podrán
                            iniciar una conversación contigo a través del chat interno de Portal Deseret.
                        </p>
                    </form>
                </div>
            </div>
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
                        <span class="block text-[8px] font-black uppercase tracking-[0.18em] text-emerald-700">Ancla
                            Territorial</span>
                        <h2 class="text-xl font-black text-[#172A3D]">Información de residencia</h2>
                    </div>
                    <button type="button"
                        class="small-button shrink-0 border border-slate-200 bg-white px-3 py-1 text-slate-700"
                        @click="cerrarModalRecepcion">×</button>
                </div>

                <form class="space-y-4" @submit.prevent="guardarRecepcion">
                    <!-- SELECTOR DE UBICACIÓN -->
                    <UbicacionSelector v-model:pais="formRecepcion.pais"
                        v-model:departamento="formRecepcion.departamento" v-model:ciudad="formRecepcion.ciudad" />

                    <!-- DIRECCIÓN GUIADA Y ESTANDARIZADA -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">Dirección estructurada *</label>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nomenclatura
                                oficial</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-6">
                            <!-- 1. TIPO DE VÍA (ESTILO OSCURO) -->
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
                                        :class="direccionEstandar.tipo === via ? 'bg-emerald-600 text-white' : ''">
                                        <span>{{ via }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. VÍA PRINCIPAL -->
                            <div class="col-span-2 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Nº /
                                    Vía</label>
                                <input v-model="direccionEstandar.viaPrincipal" type="text" placeholder="Ej. 85G"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 3. VÍA GENERADORA (# CRUCE) -->
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">#
                                    Cruce</label>
                                <input v-model="direccionEstandar.viaGeneradora" type="text" placeholder="Ej. 24C"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 4. PLACA / PUERTA -->
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">-
                                    Placa</label>
                                <input v-model="direccionEstandar.placa" type="text" placeholder="Ej. 50"
                                    class="mt-1 w-full rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 5. SECTOR (ESTILO OSCURO) -->
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
                                        :class="direccionEstandar.sector === sec ? 'bg-emerald-600 text-white' : ''">
                                        <span>{{ sec }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA PREVIA DE LA DIRECCIÓN FORMATEADA -->
                        <div v-if="direccionCompletaGenerada"
                            class="flex items-center justify-between rounded-xl border border-emerald-200 bg-[#EEFDF7] px-3 py-2 text-xs">
                            <span class="font-medium text-emerald-800">Dirección canónica a guardar:</span>
                            <strong class="font-black text-emerald-950">{{ direccionCompletaGenerada }}</strong>
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

                    <!-- MAPA INTERACTIVO Y COORDENADAS -->
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-emerald-800">📍
                                    Georreferenciación Exacta</h4>
                                <p class="mt-1 text-xs text-emerald-700/80">Verifica tu ubicación en el mapa interactivo
                                    o captúrala con tu GPS satelital.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button v-if="formRecepcion.latitud || formRecepcion.longitud" type="button"
                                    class="rounded-xl border border-emerald-300 bg-white px-3 py-2 text-xs font-bold text-emerald-700"
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

                        <!-- MINI MAPA LEAFLET -->
                        <div class="mt-3">
                            <MapaSelector v-model:latitud="formRecepcion.latitud"
                                v-model:longitud="formRecepcion.longitud" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-[#E9EDF0] pt-4">
                        <button type="button"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700"
                            @click="cerrarModalRecepcion">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-700 px-5 py-2.5 text-xs font-black text-white"
                            :disabled="guardandoRecepcion">
                            {{ guardandoRecepcion ? 'Guardando...' : 'Guardar residencia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.fondo-carboncillo {
    background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-mobile.jpg');
}

@media (min-width: 768px) {
    .fondo-carboncillo {
        background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-desktop.jpg');
    }
}
</style>