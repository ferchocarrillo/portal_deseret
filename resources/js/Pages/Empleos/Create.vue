<script setup>
import { computed, onMounted, ref, reactive, watch } from 'vue'
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import Swal from 'sweetalert2'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'
import MapaSelector from '@/Components/MapaSelector.vue'

const props = defineProps({
    empresas: {
        type: Array,
        default: () => [],
    },
    skills: {
        type: Array,
        default: () => [],
    },
    puedePublicar: {
        type: Boolean,
        default: true,
    },
    mensajeInformacion: {
        type: String,
        default: '',
    },
    informacionActualizada: {
        type: Boolean,
        default: true,
    },
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

// ==========================================================
// PUBLICACIÓN Y PREGUNTAS
// ==========================================================
const tipoPublicador = ref('persona')
const preguntas = ref([])
const nuevaPregunta = ref({
    pregunta: '',
    tipo: 'texto',
    requerida: true,
    opciones: ['', ''],
})

const alertaPregunta = ref('')
const cargandoIAPregunta = ref(false)

function verificarPalabrasProhibidasPregunta(texto) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        alertaPregunta.value = ''
        return
    }
    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find(palabra => {
        const palabraClean = palabra.trim().toLowerCase()
        return palabraClean.length > 0 && textoLower.includes(palabraClean)
    })
    if (encontrada) {
        alertaPregunta.value = `⚠️ Término no permitido detectado ("${encontrada}").`
    } else {
        alertaPregunta.value = ''
    }
}

watch(() => nuevaPregunta.value.pregunta, (nuevoValor) => {
    verificarPalabrasProhibidasPregunta(nuevoValor)
})

async function corregirMagicaPregunta() {
    const textoActual = nuevaPregunta.value.pregunta
    if (!textoActual || !textoActual.trim()) return
    cargandoIAPregunta.value = true
    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: 'oferta_empleo_pregunta'
        })
        if (response.data && response.data.improved_text) {
            nuevaPregunta.value.pregunta = response.data.improved_text
        }
    } catch (error) {
        console.error('Error al aplicar la corrección mágica:', error)
        alert('No fue posible conectar con el servicio de IA en este momento.')
    } finally {
        cargandoIAPregunta.value = false
    }
}

// ==========================================================
// FORMULARIO PRINCIPAL DE OFERTA
// ==========================================================
const form = useForm({
    titulo: '',
    descripcion: '',
    tipo_publicador: 'persona',
    empresa_id: null,
    cargo_publicador: '',
    correo_corporativo: '',
    pais: null,
    departamento: null,
    ciudad: null,
    direccion: '',
    mostrar_direccion: false,
    radio_alcance_km: 15,
    modalidad: '',
    tipo_contrato: '',
    nivel_experiencia: '',
    experiencia_minima_meses: 0,
    salario_es_smlv: false,
    salario_monto: '',
    salario_a_convenir: false,
    mostrar_salario: true,
    incluye_subsidio_transporte: false,
    incluye_prestaciones_ley: false,
    incluye_comisiones: false,
    descripcion_comisiones: '',
    incluye_bonificaciones: false,
    descripcion_bonificaciones: '',
    incluye_beneficios_adicionales: false,
    descripcion_beneficios_adicionales: '',
    skills: [],
    preguntas: [],
})

function seleccionarPublicador(tipo) {
    tipoPublicador.value = tipo
    form.tipo_publicador = tipo
    if (tipo === 'persona') {
        form.empresa_id = null
        form.cargo_publicador = ''
        form.correo_corporativo = ''
    }
}

watch(() => form.modalidad, (modalidad) => {
    if (modalidad === 'Remoto') {
        form.direccion = ''
        form.mostrar_direccion = false
        form.radio_alcance_km = null
    } else {
        if (!form.radio_alcance_km) form.radio_alcance_km = 15;
    }
})

// ==========================================================
// SKILLS
// ==========================================================
const categoriaSkillSeleccionada = ref('')
const subcategoriaSkillSeleccionada = ref('')
const mostrarNuevaSkill = ref(false)
const creandoSkill = ref(false)
const cargandoSkills = ref(false)
const errorCargaSkills = ref('')
const catalogoSkills = ref([])
const nuevaSkill = ref({ skill_subcategory_id: '', name: '', description: '' })

function normalizarCategoriasSkills(fuente) {
    if (!Array.isArray(fuente)) return []
    if (fuente.every(item => Array.isArray(item?.subcategories))) return fuente
    const categorias = new Map()
    fuente.forEach(skill => {
        const subcategoria = skill?.subcategory || skill?.sub_category || skill?.skill_subcategory
        const categoria = subcategoria?.category || subcategoria?.skill_category || skill?.category
        if (!subcategoria || !categoria) return
        if (!categorias.has(categoria.id)) {
            categorias.set(categoria.id, { ...categoria, subcategories: [] })
        }
        const categoriaActual = categorias.get(categoria.id)
        let subcategoriaActual = categoriaActual.subcategories.find(item => Number(item.id) === Number(subcategoria.id))
        if (!subcategoriaActual) {
            subcategoriaActual = { ...subcategoria, skills: [] }
            categoriaActual.subcategories.push(subcategoriaActual)
        }
        if (!subcategoriaActual.skills.some(item => Number(item.id) === Number(skill.id))) {
            subcategoriaActual.skills.push(skill)
        }
    })
    return Array.from(categorias.values())
}

const categoriasSkills = computed(() => catalogoSkills.value)

async function cargarCatalogoSkills() {
    const skillsIniciales = normalizarCategoriasSkills(props.skills)
    if (skillsIniciales.length) {
        catalogoSkills.value = skillsIniciales
        return
    }
    cargandoSkills.value = true
    errorCargaSkills.value = ''
    try {
        const response = await axios.get(route('perfil.skills.index'))
        catalogoSkills.value = normalizarCategoriasSkills(response.data?.categories)
        if (!catalogoSkills.value.length) {
            errorCargaSkills.value = 'No hay categorías de skills disponibles.'
        }
    } catch (error) {
        console.error('Error cargando catálogo de skills:', error)
        errorCargaSkills.value = 'No fue posible cargar las categorías de skills.'
    } finally {
        cargandoSkills.value = false
    }
}

onMounted(() => {
    cargarCatalogoSkills()
})

const categoriaActual = computed(() => categoriasSkills.value.find(c => String(c.id) === String(categoriaSkillSeleccionada.value)) || null)
const subcategoriasSkills = computed(() => categoriaActual.value?.subcategories || [])
const subcategoriaActual = computed(() => subcategoriasSkills.value.find(s => String(s.id) === String(subcategoriaSkillSeleccionada.value)) || null)
const skillsDisponibles = computed(() => subcategoriaActual.value?.skills || [])

function seleccionarCategoriaSkill() {
    subcategoriaSkillSeleccionada.value = ''
    nuevaSkill.value.skill_subcategory_id = ''
}
function seleccionarSubcategoriaSkill() {
    nuevaSkill.value.skill_subcategory_id = subcategoriaSkillSeleccionada.value
}
function skillSeleccionada(skillId) {
    return form.skills.some(skill => Number(skill.id) === Number(skillId))
}
function obtenerSkill(skillId) {
    if (!catalogoSkills.value.length) return null
    for (const categoria of catalogoSkills.value) {
        if (!Array.isArray(categoria.subcategories)) continue
        for (const subcategoria of categoria.subcategories) {
            if (!Array.isArray(subcategoria.skills)) continue
            const skillEncontrada = subcategoria.skills.find(item => Number(item.id) === Number(skillId))
            if (skillEncontrada) {
                return { ...skillEncontrada, subcategoriaNombre: subcategoria.name, categoriaNombre: categoria.name }
            }
        }
    }
    return null
}
function toggleSkill(skill) {
    const index = form.skills.findIndex(item => Number(item.id) === Number(skill.id))
    if (index === -1) {
        form.skills.push({ id: skill.id, nivel_requerido: 'Intermedio', es_obligatorio: true })
    } else {
        form.skills.splice(index, 1)
    }
}
function abrirNuevaSkill() {
    nuevaSkill.value = { skill_subcategory_id: subcategoriaSkillSeleccionada.value, name: '', description: '' }
    mostrarNuevaSkill.value = true
}
async function crearNuevaSkill() {
    if (!nuevaSkill.value.skill_subcategory_id || !nuevaSkill.value.name.trim()) return
    creandoSkill.value = true
    try {
        const response = await axios.post('/skills', {
            skill_subcategory_id: nuevaSkill.value.skill_subcategory_id,
            name: nuevaSkill.value.name.trim(),
            description: nuevaSkill.value.description.trim() || null,
        })
        const skill = response.data.skill
        if (skill) {
            const subcategoria = subcategoriasSkills.value.find(item => Number(item.id) === Number(nuevaSkill.value.skill_subcategory_id))
            if (subcategoria) {
                if (!Array.isArray(subcategoria.skills)) subcategoria.skills = []
                if (!subcategoria.skills.some(item => Number(item.id) === Number(skill.id))) {
                    subcategoria.skills.push(skill)
                }
            }
            form.skills.push({ id: skill.id, nivel_requerido: 'Intermedio', es_obligatorio: true })
        }
        mostrarNuevaSkill.value = false
        nuevaSkill.value = { skill_subcategory_id: subcategoriaSkillSeleccionada.value, name: '', description: '' }
    } catch (error) {
        alert(error.response?.data?.message || 'No fue posible crear la skill.')
    } finally {
        creandoSkill.value = false
    }
}
function eliminarSkill(skillId) {
    const index = form.skills.findIndex(item => Number(item.id) === Number(skillId))
    if (index !== -1) form.skills.splice(index, 1)
}

// ==========================================================
// PREGUNTAS (AGREGAR)
// ==========================================================
function agregarPregunta() {
    if (!nuevaPregunta.value.pregunta.trim()) return
    const pregunta = {
        pregunta: nuevaPregunta.value.pregunta.trim(),
        tipo: nuevaPregunta.value.tipo,
        requerida: nuevaPregunta.value.requerida,
        opciones: nuevaPregunta.value.tipo === 'opcion_multiple' ? nuevaPregunta.value.opciones.filter(o => o.trim() !== '') : [],
    }
    preguntas.value.push(pregunta)
    nuevaPregunta.value = { pregunta: '', tipo: 'texto', requerida: true, opciones: ['', ''] }
}
function eliminarPregunta(index) {
    preguntas.value.splice(index, 1)
}
function agregarOpcion() {
    nuevaPregunta.value.opciones.push('')
}
function eliminarOpcion(index) {
    if (nuevaPregunta.value.opciones.length <= 2) return
    nuevaPregunta.value.opciones.splice(index, 1)
}
const preguntasFormateadas = computed(() => preguntas.value.map((p, index) => ({ ...p, orden: index + 1 })))

// ==========================================================
// PUBLICAR
// ==========================================================
function publicarOferta() {
    form.tipo_publicador = tipoPublicador.value
    form.preguntas = preguntasFormateadas.value
    form.post(route('empleos.store'))
}

// ==========================================================
// CORRECCIÓN MÁGICA CON IA
// ==========================================================
const cargandoIA = ref({ titulo: false, descripcion: false })
async function aplicarCorreccionMagica(campo) {
    const textoActual = form[campo]
    if (!textoActual || !textoActual.trim()) return
    cargandoIA.value[campo] = true
    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: `oferta_empleo_${campo}`
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
// MODERACIÓN EN TIEMPO REAL
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
    const encontrada = props.palabrasProhibidas.find(palabra => {
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
watch(() => form.titulo, (nuevoValor) => verificarPalabrasProhibidas(nuevoValor, 'titulo'))
watch(() => form.descripcion, (nuevoValor) => verificarPalabrasProhibidas(nuevoValor, 'descripcion'))


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

    <Head title="Publicar oferta de empleo | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="mb-3 flex items-center gap-2 text-xs sm:text-sm text-slate-700">
                                <Link :href="route('empleos.index')" class="transition hover:text-[#D99A24] font-bold">
                                    Empleos</Link>
                                <span>/</span>
                                <span class="text-slate-500 font-semibold">Publicar oferta</span>
                            </div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-[#D99A24]"></span>
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">
                                    Oportunidades laborales</p>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#172A3D]">Publicar una
                                oferta de empleo</h1>
                            <p class="mt-2 max-w-2xl text-xs sm:text-sm leading-6 text-slate-700 font-medium">
                                Crea una oportunidad laboral y encuentra personas que coincidan con el perfil que
                                necesitas.
                            </p>
                        </div>
                        <Link :href="route('empleos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-slate-50">
                            ← Volver a empleos
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-6 bg-[#D99A24]/20 rounded-xl p-3 mt-2">

                <!-- ALERTA INFORMACIÓN FALTANTE -->
                <div v-if="!puedePublicar || !informacionActualizada"
                    class="rounded-2xl border border-amber-300 bg-amber-50/90 p-5 shadow-2xl shadow-amber-500/20 backdrop-blur-md">
                    <div class="flex gap-4">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 font-bold">
                            !</div>
                        <div class="min-w-0">
                            <h2 class="font-bold text-amber-900">Información requerida</h2>
                            <p v-if="mensajeInformacion"
                                class="mt-1 text-xs sm:text-sm leading-6 text-amber-800 font-medium">{{
                                mensajeInformacion }}</p>
                            <p v-else class="mt-1 text-xs sm:text-sm leading-6 text-amber-800 font-medium">Debes
                                mantener actualizada la información de tu perfil para poder publicar o postularte a
                                ofertas de empleo.</p>
                            <Link :href="route('perfil.show')"
                                class="mt-3 inline-flex text-xs sm:text-sm font-bold text-amber-900 underline underline-offset-4 transition hover:text-amber-700">
                                Actualizar mi información
                            </Link>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="publicarOferta" class="space-y-6">

                    <!-- INFORMACIÓN DE LA OFERTA -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Información de la
                                oferta</p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">Describe la oportunidad</h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">Proporciona la información
                                principal del cargo.</p>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Título
                                        de la oferta <span class="text-red-500">*</span></label>
                                    <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                        :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-[#D99A24] bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span><span
                                            v-else>✨</span><span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <input v-model="form.titulo" type="text" placeholder="Ej. Desarrollador Web Full Stack"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                                <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">{{ alertaTitulo
                                    }}</p>
                                <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">{{
                                    form.errors.titulo }}</p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Descripción
                                        <span class="text-red-500">*</span></label>
                                    <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                        :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-[#D99A24] bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span><span
                                            v-else>✨</span><span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea v-model="form.descripcion" rows="7"
                                    placeholder="Describe el cargo, responsabilidades, objetivos y cualquier información importante..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100"></textarea>
                                <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">{{
                                    alertaDescripcion }}</p>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">{{
                                    form.errors.descripcion }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- QUIÉN PUBLICA -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Publicador</p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">¿Quién publica esta oferta?</h2>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <button type="button" @click="seleccionarPublicador('persona')"
                                :class="['rounded-2xl border p-5 text-left transition', tipoPublicador === 'persona' ? 'border-[#D99A24] bg-amber-50/50 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 bg-white hover:border-amber-300']">
                                <div class="flex items-start gap-4">
                                    <div
                                        :class="['flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-bold', tipoPublicador === 'persona' ? 'bg-[#D99A24] text-white shadow-sm' : 'bg-slate-100 text-slate-600']">
                                        👤</div>
                                    <div>
                                        <h3 class="font-bold text-[#172A3D]">Publico como persona</h3>
                                        <p class="mt-1 text-xs sm:text-sm leading-5 text-slate-500 font-medium">
                                            Utilizaré mis datos registrados en Portal Deseret.</p>
                                    </div>
                                </div>
                            </button>
                            <button type="button" @click="seleccionarPublicador('empresa')"
                                :class="['rounded-2xl border p-5 text-left transition', tipoPublicador === 'empresa' ? 'border-[#D99A24] bg-amber-50/50 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 bg-white hover:border-amber-300']">
                                <div class="flex items-start gap-4">
                                    <div
                                        :class="['flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-lg font-bold', tipoPublicador === 'empresa' ? 'bg-[#D99A24] text-white shadow-sm' : 'bg-slate-100 text-slate-600']">
                                        🏢</div>
                                    <div>
                                        <h3 class="font-bold text-[#172A3D]">Represento una empresa</h3>
                                        <p class="mt-1 text-xs sm:text-sm leading-5 text-slate-500 font-medium">
                                            Publicaré en nombre de una empresa registrada.</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                        <div v-if="tipoPublicador === 'empresa'"
                            class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/60 p-5">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Nombre
                                        de la empresa</label>
                                    <input v-model="form.empresa_nombre" type="text" placeholder="Nombre de la empresa"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Cargo
                                        dentro de la empresa</label>
                                    <input v-model="form.cargo_publicador" type="text"
                                        placeholder="Ej. Gerente, Administrador, Reclutador..."
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Correo
                                        corporativo</label>
                                    <input v-model="form.correo_corporativo" type="email"
                                        placeholder="nombre@empresa.com"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- UBICACIÓN Y MODALIDAD -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Ubicación y modalidad
                            </p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">¿Dónde y cómo se realizará el trabajo?
                            </h2>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Modalidad
                                    de trabajo</label>
                                <select v-model="form.modalidad"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                    <option value="">Selecciona la modalidad</option>
                                    <option value="Presencial">Presencial</option>
                                    <option value="Híbrido">Híbrido</option>
                                    <option value="Remoto">Remoto</option>
                                </select>
                            </div>

                            <div v-if="form.modalidad">
                                <div class="mb-3">
                                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ubicación del
                                        empleo</p>
                                </div>
                                <UbicacionSelector v-model:pais="form.pais" v-model:departamento="form.departamento"
                                    v-model:ciudad="form.ciudad" />
                            </div>

                            <div v-if="form.modalidad === 'Presencial' || form.modalidad === 'Híbrido'">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Dirección
                                    del lugar de trabajo <span
                                        class="font-normal text-slate-400">(opcional)</span></label>
                                <input v-model="form.direccion" type="text" placeholder="Ej. Carrera 10 # 20-30, Madrid"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />

                                <label
                                    class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <input v-model="form.mostrar_direccion" type="checkbox"
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
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
                                                    Para limitar el alcance de esta oferta presencial o híbrida,
                                                    necesitamos conocer tu ubicación exacta como Ancla Territorial.
                                                </p>
                                                <button type="button" @click="abrirModalRecepcion"
                                                    class="mt-3 inline-block text-xs sm:text-sm font-bold text-red-800 underline underline-offset-4 transition hover:text-red-600">
                                                    Registrar ubicación en el mapa →
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="rounded-xl border border-amber-200 bg-amber-50/60 p-5">
                                        <div class="flex items-center justify-between">
                                            <label
                                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                Radio de alcance de la oferta: <span
                                                    class="text-[#D99A24] text-xl font-black">{{ form.radio_alcance_km
                                                    }} km</span>
                                            </label>
                                            <button type="button" @click="abrirModalRecepcion"
                                                class="text-xs font-bold text-[#D99A24] hover:underline">
                                                Modificar ubicación origen
                                            </button>
                                        </div>
                                        <p class="mt-1 mb-4 text-xs leading-5 text-slate-600 font-medium">Solo
                                            candidatos que residan dentro de este rango podrán ver y aplicar a esta
                                            oferta.</p>
                                        <div class="flex items-center gap-4">
                                            <span class="text-xl font-bold text-slate-500">1 km</span>
                                            <input type="range" v-model="form.radio_alcance_km" min="1" max="100"
                                                step="1"
                                                class="w-full h-2 bg-slate-300 rounded-lg appearance-none cursor-pointer accent-[#D99A24]" />
                                            <span class="text-xl font-bold text-slate-500">100 km</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.modalidad === 'Remoto'"
                                class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-[#D99A24] shadow-xs">
                                        🌐</div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-[#172A3D]">Trabajo remoto</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 font-medium">No se solicitará
                                            ubicación física. La ubicación seleccionada es solo para referencia.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Tipo
                                        de contrato</label>
                                    <select v-model="form.tipo_contrato"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                        <option value="">Selecciona</option>
                                        <option value="Término indefinido">Término indefinido</option>
                                        <option value="Término fijo">Término fijo</option>
                                        <option value="Prestación de servicios">Prestación de servicios</option>
                                        <option value="Obra o labor">Obra o labor</option>
                                        <option value="Aprendizaje">Aprendizaje</option>
                                        <option value="Temporal">Temporal</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Nivel
                                        de experiencia</label>
                                    <select v-model="form.nivel_experiencia"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                        <option value="">Selecciona</option>
                                        <option value="Sin experiencia">Sin experiencia</option>
                                        <option value="Practicante">Practicante</option>
                                        <option value="Junior">Junior</option>
                                        <option value="Intermedio">Intermedio</option>
                                        <option value="Senior">Senior</option>
                                        <option value="Especialista">Especialista</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Experiencia
                                    mínima requerida (meses)</label>
                                <select v-model.number="form.experiencia_minima_meses"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                    <option :value="0">Sin experiencia</option>
                                    <option :value="1">De 1 a 6 meses</option>
                                    <option :value="6">De 6 meses a 1 año</option>
                                    <option :value="12">De 1 a 2 años</option>
                                    <option :value="24">De 2 a 3 años</option>
                                    <option :value="36">De 3 a 5 años</option>
                                    <option :value="60">Más de 5 años</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- COMPENSACIÓN -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Compensación</p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">Información salarial</h2>
                        </div>
                        <div class="space-y-5">
                            <div class="grid gap-4 md:grid-cols-2">
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <input v-model="form.salario_es_smlv" type="checkbox"
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span>
                                        <span class="block text-xs sm:text-sm font-bold text-slate-700">Salario mínimo
                                            legal vigente (SMLV)</span>
                                    </span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <input v-model="form.salario_a_convenir" type="checkbox"
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span>
                                        <span class="block text-xs sm:text-sm font-bold text-slate-700">Salario a
                                            convenir</span>
                                    </span>
                                </label>
                            </div>

                            <div v-if="!form.salario_es_smlv && !form.salario_a_convenir">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Salario
                                    ofrecido</label>
                                <input v-model="form.salario_monto" type="number" min="0" step="1000"
                                    placeholder="Ej. 2500000"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
                                    <input v-model="form.incluye_subsidio_transporte" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span class="text-xs sm:text-sm font-bold text-slate-700">Incluye subsidio de
                                        transporte</span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
                                    <input v-model="form.incluye_prestaciones_ley" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span class="text-xs sm:text-sm font-bold text-slate-700">Incluye prestaciones de
                                        ley</span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
                                    <input v-model="form.incluye_comisiones" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span class="text-xs sm:text-sm font-bold text-slate-700">Incluye comisiones</span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
                                    <input v-model="form.incluye_bonificaciones" type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                    <span class="text-xs sm:text-sm font-bold text-slate-700">Incluye
                                        bonificaciones</span>
                                </label>
                            </div>

                            <div v-if="form.incluye_comisiones">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Información
                                    sobre las comisiones</label>
                                <textarea v-model="form.descripcion_comisiones" rows="3"
                                    placeholder="Explica cómo funcionan las comisiones, porcentajes o condiciones..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100"></textarea>
                            </div>

                            <div v-if="form.incluye_bonificaciones">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Información
                                    sobre las bonificaciones</label>
                                <textarea v-model="form.descripcion_bonificaciones" rows="3"
                                    placeholder="Describe las bonificaciones ofrecidas..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100"></textarea>
                            </div>

                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 bg-white">
                                <input v-model="form.incluye_beneficios_adicionales" type="checkbox"
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                <span>
                                    <span class="block text-xs sm:text-sm font-bold text-slate-700">Beneficios
                                        adicionales</span>
                                    <span class="mt-1 block text-xs text-slate-500 font-medium">Por ejemplo:
                                        alimentación, bonos, horarios flexibles, seguros u otros beneficios.</span>
                                </span>
                            </label>

                            <div v-if="form.incluye_beneficios_adicionales">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Describe
                                    los beneficios adicionales</label>
                                <textarea v-model="form.descripcion_beneficios_adicionales" rows="3"
                                    placeholder="Describe los beneficios adicionales de la oferta..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100"></textarea>
                            </div>

                            <label class="flex cursor-pointer items-center gap-3">
                                <input v-model="form.mostrar_salario" type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                                <span class="text-xs sm:text-sm font-bold text-slate-700">Mostrar la información
                                    salarial públicamente en la oferta</span>
                            </label>
                        </div>
                    </section>

                    <!-- SKILLS -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Perfil requerido</p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">Skills requeridas</h2>
                        </div>
                        <div v-if="categoriasSkills.length" class="space-y-5">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Categoría</label>
                                    <select v-model="categoriaSkillSeleccionada" @change="seleccionarCategoriaSkill"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                        <option value="">Selecciona una categoría</option>
                                        <option v-for="categoria in categoriasSkills" :key="categoria.id"
                                            :value="categoria.id">{{ categoria.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Subcategoría</label>
                                    <select v-model="subcategoriaSkillSeleccionada"
                                        @change="seleccionarSubcategoriaSkill" :disabled="!categoriaSkillSeleccionada"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none disabled:cursor-not-allowed focus:border-[#D99A24]">
                                        <option value="">Selecciona una subcategoría</option>
                                        <option v-for="subcategoria in subcategoriasSkills" :key="subcategoria.id"
                                            :value="subcategoria.id">{{ subcategoria.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="subcategoriaSkillSeleccionada"
                                class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <h3 class="font-bold text-[#172A3D]">Habilidades disponibles</h3>
                                    <button type="button" @click="abrirNuevaSkill"
                                        class="inline-flex items-center justify-center rounded-xl border border-[#D99A24] bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#D99A24] transition hover:bg-amber-50">+
                                        Crear nueva skill</button>
                                </div>
                                <div v-if="skillsDisponibles.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    <button v-for="skill in skillsDisponibles" :key="skill.id" type="button"
                                        @click="toggleSkill(skill)"
                                        :class="['rounded-xl border px-4 py-3 text-left text-sm transition font-semibold', skillSeleccionada(skill.id) ? 'border-[#D99A24] bg-amber-50 text-[#172A3D] shadow-xs' : 'border-slate-300 bg-white text-slate-700']">
                                        <div class="flex items-center justify-between gap-3">
                                            <span>{{ skill.name }}</span>
                                            <span v-if="skillSeleccionada(skill.id)"
                                                class="text-[#D99A24] font-bold">✓</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div v-if="form.skills.length > 0" class="mt-6">
                                <h3 class="text-xs sm:text-sm font-bold text-slate-700 uppercase tracking-wider mb-3">
                                    Skills seleccionadas ({{ form.skills.length }})</h3>
                                <div class="space-y-3">
                                    <div v-for="seleccionada in form.skills" :key="seleccionada.id"
                                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="font-bold text-[#172A3D] text-sm sm:text-base">{{
                                            obtenerSkill(seleccionada.id)?.name ?? 'Habilidad #' + seleccionada.id }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4">
                                            <select v-model="seleccionada.nivel_requerido"
                                                class="rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-[#172A3D]">
                                                <option value="Básico">Básico</option>
                                                <option value="Intermedio">Intermedio</option>
                                                <option value="Avanzado">Avanzado</option>
                                            </select>
                                            <label
                                                class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 cursor-pointer">
                                                <input v-model="seleccionada.es_obligatorio" type="checkbox"
                                                    class="h-4 w-4 rounded border-slate-300 text-[#D99A24]" />
                                                <span>Obligatorio</span>
                                            </label>
                                            <button type="button" @click="eliminarSkill(seleccionada.id)"
                                                class="rounded-xl p-2 text-red-500 hover:bg-red-50">🗑️</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Crear nueva skill modal/panel -->
                        <div v-if="mostrarNuevaSkill"
                            class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/60 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-[#172A3D]">Crear nueva skill</h3>
                                </div>
                                <button type="button" @click="mostrarNuevaSkill = false"
                                    class="text-slate-500 font-bold text-lg">×</button>
                            </div>
                            <div class="mt-5 space-y-4">
                                <input v-model="nuevaSkill.name" type="text" placeholder="Ej. FlutterFlow"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-[#D99A24]" />
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="mostrarNuevaSkill = false"
                                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold">Cancelar</button>
                                    <button type="button" @click="crearNuevaSkill"
                                        :disabled="creandoSkill || !nuevaSkill.name.trim()"
                                        class="rounded-xl bg-[#D99A24] px-5 py-2.5 text-xs font-black text-white">Crear
                                        skill</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- PREGUNTAS -->
                    <section
                        class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 md:p-8">
                        <div class="mb-6">
                            <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Filtro de candidatos
                            </p>
                            <h2 class="mt-1 text-xl font-black text-[#172A3D]">Preguntas para los postulantes</h2>
                        </div>
                        <div v-if="preguntas.length" class="mb-8 space-y-4">
                            <div v-for="(pregunta, index) in preguntas" :key="index"
                                class="rounded-xl border border-slate-200 bg-amber-50/40 p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <span
                                            class="text-[10px] font-black uppercase tracking-wider text-[#D99A24]">Pregunta
                                            {{ index + 1 }}</span>
                                        <p class="mt-1 font-bold text-[#172A3D]">{{ pregunta.pregunta }}</p>
                                    </div>
                                    <button type="button" @click="eliminarPregunta(index)"
                                        class="text-xs font-bold text-red-600">Eliminar</button>
                                </div>
                                <div v-if="pregunta.tipo === 'opcion_multiple'" class="mt-4 space-y-2">
                                    <div v-for="opcion in pregunta.opciones" :key="opcion"
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700">
                                        {{ opcion }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5">
                            <h3 class="font-bold text-[#172A3D]">Agregar una pregunta</h3>
                            <div class="mt-4 space-y-5">
                                <div>
                                    <textarea v-model="nuevaPregunta.pregunta" rows="3"
                                        placeholder="Ej. ¿Qué tipo de código manejas?"
                                        class="w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-[#D99A24]"></textarea>
                                </div>
                                <div class="grid gap-5 md:grid-cols-2">
                                    <select v-model="nuevaPregunta.tipo"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold">
                                        <option value="texto">Respuesta abierta</option>
                                        <option value="opcion_multiple">Selección de opciones</option>
                                    </select>
                                    <label class="flex items-center gap-3">
                                        <input v-model="nuevaPregunta.requerida" type="checkbox"
                                            class="h-4 w-4 rounded text-[#D99A24]" />
                                        <span class="text-xs sm:text-sm font-bold text-slate-700">Pregunta
                                            obligatoria</span>
                                    </label>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" @click="agregarPregunta"
                                        class="rounded-xl bg-[#D99A24] px-5 py-3 text-xs sm:text-sm font-black text-white"
                                        :disabled="!nuevaPregunta.pregunta.trim() || alertaPregunta">+ Agregar
                                        pregunta</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div v-if="form.hasErrors"
                        class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-2xl shadow-red-500/15">
                        <h3 class="font-bold text-red-900">Revisa la información</h3>
                        <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">Hay campos que requieren corrección
                            antes de publicar la oferta.</p>
                    </div>

                    <!-- ACCIONES -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end pb-12">
                        <Link :href="route('empleos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Cancelar</Link>
                        <button type="submit"
                            :disabled="form.processing || !puedePublicar || !informacionActualizada || alertaTitulo || alertaDescripcion || (!tieneCoordenadas && (form.modalidad === 'Presencial' || form.modalidad === 'Híbrido'))"
                            class="inline-flex items-center justify-center rounded-xl bg-[#D99A24] px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-amber-600 disabled:opacity-50">
                            {{ form.processing ? 'Publicando...' : 'Publicar oferta' }}
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
                            <!-- 1. TIPO DE VÍA (ESTILO OSCURO APLICADO) -->
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
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition"
                                        :class="direccionEstandar.tipo === via ? 'bg-[#D99A24] text-white' : 'text-slate-300 hover:bg-[#233C55] hover:text-white'">
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

                            <!-- 5. SECTOR (ESTILO OSCURO APLICADO) -->
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
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition"
                                        :class="direccionEstandar.sector === sec ? 'bg-[#D99A24] text-white' : 'text-slate-300 hover:bg-[#233C55] hover:text-white'">
                                        <span>{{ sec }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA PREVIA DE LA DIRECCIÓN FORMATEADA -->
                        <div v-if="direccionCompletaGenerada"
                            class="flex items-center justify-between rounded-xl border border-amber-200 bg-[#FFFDF8] px-3 py-2 text-xs">
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

                    <!-- MAPA INTERACTIVO Y COORDENADAS -->
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
                                    🔄 Recalcular por dirección
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

