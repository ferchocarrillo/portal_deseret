<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Swal from 'sweetalert2'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
    clasificado: {
        type: Object,
        required: true,
    },
    categorias: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| Datos iniciales y manejo de categorías
|--------------------------------------------------------------------------
*/

const clasificado = props.clasificado
const categoriaActual = clasificado.categoria

const initialParentId = categoriaActual?.parent_id || (categoriaActual?.hijas?.length ? '' : categoriaActual?.id) || ''
const initialSubId = categoriaActual?.parent_id ? categoriaActual?.id : ''

const categoriaPrincipalId = ref(initialParentId)
const categoriaId = ref(initialSubId)

const categoriaPrincipal = computed(() => {
    return props.categorias.find(
        cat => String(cat.id) === String(categoriaPrincipalId.value)
    )
})

const subcategorias = computed(() => {
    return categoriaPrincipal.value?.hijas || []
})

const esVehiculo = computed(() => {
    const slug = categoriaPrincipal.value?.slug || categoriaActual?.slug
    return slug === 'vehiculos' || categoriaActual?.padre?.slug === 'vehiculos'
})

const esPropiedad = computed(() => {
    const slug = categoriaPrincipal.value?.slug || categoriaActual?.slug
    return slug === 'inmuebles' || slug === 'propiedades' || categoriaActual?.padre?.slug === 'inmuebles' || categoriaActual?.padre?.slug === 'propiedades'
})

/*
|--------------------------------------------------------------------------
| Ubicación y Rango de Cobertura
|--------------------------------------------------------------------------
*/

const ubicacion = ref({
    pais: clasificado.pais?.nombre || 'Colombia',
    departamento:
        clasificado.division_administrativa?.nombre ||
        clasificado.divisionAdministrativa?.nombre ||
        '',
    ciudad: clasificado.ciudad?.nombre || '',
})

const ubicacionError = ref('')

const opcionesCobertura = [
    { valor: 'local', nombre: 'Solo Local', detalle: 'Visible principalmente en tu municipio o ciudad.' },
    { valor: 'departamental', nombre: 'Departamental', detalle: 'Visible para todo tu departamento o región.' },
    { valor: 'nacional', nombre: 'Nacional', detalle: 'Visible en todo el país sin restricción territorial.' },
]

const descripcionCoberturaActual = computed(() => {
    const seleccionada = opcionesCobertura.find(o => o.valor === form.rango_cobertura)
    return seleccionada ? seleccionada.detalle : ''
})

/*
|--------------------------------------------------------------------------
| Imágenes
|--------------------------------------------------------------------------
*/

const imagenesExistentes = ref(
    [...(clasificado.imagenes || [])].sort(
        (a, b) => (a.orden ?? 0) - (b.orden ?? 0)
    )
)

const imagenesAEliminar = ref([])
const nuevasImagenes = ref([])
const previews = ref([])

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = useForm({
    categoria_id: clasificado.categoria_id || '',
    intencion: clasificado.intencion || 'ofrecer',
    operacion: clasificado.operacion || 'venta',

    titulo: clasificado.titulo || '',
    descripcion: clasificado.descripcion || '',
    precio: clasificado.precio || '',

    pais_id: clasificado.pais_id || '',
    division_administrativa_id:
        clasificado.division_administrativa_id || '',
    ciudad_id: clasificado.ciudad_id || '',
    rango_cobertura: clasificado.rango_cobertura || 'departamental',

    imagenes: [],
    imagenes_a_eliminar: [],
    imagen_principal_id: imagenesExistentes.value.find(img => img.es_principal)?.id || null,

    // Vehículo
    marca: clasificado.vehiculo?.marca || '',
    modelo: clasificado.vehiculo?.modelo || '',
    anio: clasificado.vehiculo?.anio || '',
    kilometraje: clasificado.vehiculo?.kilometraje || '',
    cilindraje: clasificado.vehiculo?.cilindraje || '',
    combustible: clasificado.vehiculo?.combustible || '',
    transmision: clasificado.vehiculo?.transmision || '',
    color: clasificado.vehiculo?.color || '',
    placa: clasificado.vehiculo?.placa || '',
    unico_propietario: clasificado.vehiculo?.unico_propietario ? true : false,
    documentacion_al_dia: clasificado.vehiculo?.documentacion_al_dia ? true : false,
    vehiculo_observaciones:
        clasificado.vehiculo?.observaciones ||
        clasificado.vehiculo?.vehiculo_observaciones ||
        '',

    // Propiedad - Generales
    area_total: clasificado.propiedad?.area_total || '',
    area_construida: clasificado.propiedad?.area_construida || '',
    habitaciones: clasificado.propiedad?.habitaciones || '',
    banos: clasificado.propiedad?.banos || '',
    garajes: clasificado.propiedad?.garajes || '',
    pisos: clasificado.propiedad?.pisos || '',
    piso: clasificado.propiedad?.piso || '',
    estrato: clasificado.propiedad?.estrato || '',
    antiguedad: clasificado.propiedad?.antiguedad || '',
    remodelada: Boolean(clasificado.propiedad?.remodelada),
    propiedad_observaciones:
        clasificado.propiedad?.observaciones ||
        clasificado.propiedad?.propiedad_observaciones ||
        '',

    // Propiedad - Administración (arriendos)
    incluye_administracion: Boolean(clasificado.propiedad?.incluye_administracion),
    precio_administracion: clasificado.propiedad?.precio_administracion || '',

    // Propiedad - Servicios básicos y documentos
    tiene_servicios_publicos: Boolean(clasificado.propiedad?.tiene_servicios_publicos),
    tiene_agua: Boolean(clasificado.propiedad?.tiene_agua),
    tiene_energia: Boolean(clasificado.propiedad?.tiene_energia),
    tiene_gas: Boolean(clasificado.propiedad?.tiene_gas),
    tiene_alcantarillado: Boolean(clasificado.propiedad?.tiene_alcantarillado),
    tiene_internet: Boolean(clasificado.propiedad?.tiene_internet),
    tiene_tv: Boolean(clasificado.propiedad?.tiene_tv),
    papeles_al_dia: Boolean(clasificado.propiedad?.papeles_al_dia),
    libre_gravamen: Boolean(clasificado.propiedad?.libre_gravamen),

    // Propiedad - Zonas comunes y amenidades
    tiene_ascensor: Boolean(clasificado.propiedad?.tiene_ascensor),
    tiene_parqueadero: Boolean(clasificado.propiedad?.tiene_parqueadero),
    tiene_gimnasio: Boolean(clasificado.propiedad?.tiene_gimnasio),
    tiene_piscina: Boolean(clasificado.propiedad?.tiene_piscina),
    tiene_bbq: Boolean(clasificado.propiedad?.tiene_bbq),
    tiene_salon_comunal: Boolean(clasificado.propiedad?.tiene_salon_comunal),
    tiene_zona_infantil: Boolean(clasificado.propiedad?.tiene_zona_infantil),
    tiene_zonas_verdes: Boolean(clasificado.propiedad?.tiene_zonas_verdes),
    tiene_cancha_multiple: Boolean(clasificado.propiedad?.tiene_cancha_multiple),
    tiene_sauna_turco: Boolean(clasificado.propiedad?.tiene_sauna_turco),
    tiene_coworking: Boolean(clasificado.propiedad?.tiene_coworking),
    tiene_parqueadero_visitantes: Boolean(clasificado.propiedad?.tiene_parqueadero_visitantes),

    // Propiedad - Seguridad, interiores y comodidades
    tiene_porteria_24_7: Boolean(clasificado.propiedad?.tiene_porteria_24_7),
    tiene_circuito_cerrado: Boolean(clasificado.propiedad?.tiene_circuito_cerrado),
    permite_mascotas: Boolean(clasificado.propiedad?.permite_mascotas),
    tiene_balcon: Boolean(clasificado.propiedad?.tiene_balcon),
    tiene_terraza: Boolean(clasificado.propiedad?.tiene_terraza),
    tiene_deposito: Boolean(clasificado.propiedad?.tiene_deposito),
    tiene_calentador: Boolean(clasificado.propiedad?.tiene_calentador),
    tiene_aire_acondicionado: Boolean(clasificado.propiedad?.tiene_aire_acondicionado),
    cocina_integral: Boolean(clasificado.propiedad?.cocina_integral),
    zona_lavanderia: Boolean(clasificado.propiedad?.zona_lavanderia),
})

watch(() => form.incluye_administracion, (incluye) => {
    if (incluye) {
        form.precio_administracion = ''
    }
})

/*
|--------------------------------------------------------------------------
| Categorías y Manejo de Imágenes
|--------------------------------------------------------------------------
*/

const cambiarCategoriaPrincipal = () => {
    categoriaId.value = ''
    form.categoria_id = ''
}

const seleccionarCategoria = id => {
    categoriaId.value = id
    form.categoria_id = id
}

const seleccionarImagenes = event => {
    const archivos = Array.from(event.target.files || [])
    if (!archivos.length) return

    const disponibles = 8 - (imagenesExistentes.value.length - imagenesAEliminar.value.length) - nuevasImagenes.value.length
    if (disponibles <= 0) {
        event.target.value = ''
        return
    }

    const seleccionadas = archivos.slice(0, disponibles)
    seleccionadas.forEach(archivo => {
        nuevasImagenes.value.push(archivo)
        previews.value.push({
            archivo,
            url: URL.createObjectURL(archivo),
        })
    })

    form.imagenes = nuevasImagenes.value
    event.target.value = ''
}

const eliminarNuevaImagen = index => {
    const preview = previews.value[index]
    if (preview?.url) {
        URL.revokeObjectURL(preview.url)
    }
    previews.value.splice(index, 1)
    nuevasImagenes.value.splice(index, 1)
    form.imagenes = nuevasImagenes.value
}

const marcarParaEliminar = id => {
    // Se elimina localmente de la vista previa y se añade al array de pendientes por eliminar
    if (!imagenesAEliminar.value.includes(id)) {
        imagenesAEliminar.value.push(id)
        form.imagenes_a_eliminar = imagenesAEliminar.value
    }
    imagenesExistentes.value = imagenesExistentes.value.filter(img => img.id !== id)

    // Si la imagen borrada era la principal, reasignar otra si existe
    if (form.imagen_principal_id === id) {
        const remaining = imagenesExistentes.value
        form.imagen_principal_id = remaining.length ? remaining[0].id : null
    }
}

const establecerPrincipalExistente = id => {
    form.imagen_principal_id = id
    imagenesExistentes.value = imagenesExistentes.value.map(imagen => ({
        ...imagen,
        es_principal: imagen.id === id,
    }))
}

/*
|--------------------------------------------------------------------------
| Actualizar (Envío Unificado)
|--------------------------------------------------------------------------
*/

const actualizar = () => {
    form.categoria_id = categoriaId.value || categoriaPrincipalId.value

    form
        .transform(data => ({
            ...data,
            _method: 'PUT',
        }))
        .post(
            route('clasificados.update', {
                clasificado: clasificado.id,
            }),
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Cambios guardados!',
                        text: 'Tu clasificado fue actualizado correctamente.',
                        confirmButtonText: 'Continuar',
                        confirmButtonColor: '#172A3D',
                    })
                },
            }
        )
}

const error = campo => form.errors[campo] || ''
</script>

<template>

    <Head title="Editar clasificado | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-950">
                                    Modificación de aviso
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Editar clasificado
                            </h1>
                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Modifica la información, características o comodidades de tu publicación.
                            </p>
                        </div>

                        <Link :href="route('clasificados.show', clasificado.id)"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-amber-600 transition">
                            ← Volver al detalle
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-amber-50 rounded-xl p-3 mt-2">

                <!-- INFORMACIÓN DE OPERACIÓN -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Información de la publicación</h2>
                    </div>

                    <div class="p-5 sm:p-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">¿Qué deseas hacer?</label>
                            <select v-model="form.intencion" class="form-input">
                                <option value="ofrecer">Ofrecer</option>
                                <option value="buscar">Buscar</option>
                            </select>
                            <p v-if="error('intencion')" class="form-error">{{ error('intencion') }}</p>
                        </div>

                        <div>
                            <label class="form-label">Operación</label>
                            <select v-model="form.operacion" class="form-input">
                                <option value="venta">Venta</option>
                                <option value="arriendo">Arriendo</option>
                            </select>
                            <p v-if="error('operacion')" class="form-error">{{ error('operacion') }}</p>
                        </div>
                    </div>
                </section>

                <!-- CATEGORÍA -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Categoría</h2>
                    </div>

                    <div class="p-5 sm:p-6 grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="form-label">Categoría principal</label>
                            <select v-model="categoriaPrincipalId" @change="cambiarCategoriaPrincipal"
                                class="form-input">
                                <option value="">Selecciona una categoría</option>
                                <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                    {{ categoria.nombre }}
                                </option>
                            </select>
                        </div>

                        <div v-if="subcategorias.length">
                            <label class="form-label">Subcategoría</label>
                            <select :value="categoriaId" @change="seleccionarCategoria(Number($event.target.value))"
                                class="form-input">
                                <option value="">Selecciona una subcategoría</option>
                                <option v-for="categoria in subcategorias" :key="categoria.id" :value="categoria.id">
                                    {{ categoria.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="px-5 pb-5 sm:px-6">
                        <p v-if="error('categoria_id')" class="form-error">{{ error('categoria_id') }}</p>
                    </div>
                </section>

                <!-- TÍTULO / DESCRIPCIÓN / PRECIO -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Información principal</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div>
                            <label class="form-label">Título <span class="text-red-500">*</span></label>
                            <input v-model="form.titulo" type="text" maxlength="255" class="form-input" />
                            <p v-if="error('titulo')" class="form-error">{{ error('titulo') }}</p>
                        </div>

                        <div>
                            <label class="form-label">Descripción <span class="text-red-500">*</span></label>
                            <textarea v-model="form.descripcion" rows="6" class="form-input"></textarea>
                            <p v-if="error('descripcion')" class="form-error">{{ error('descripcion') }}</p>
                        </div>

                        <div class="max-w-md">
                            <label class="form-label">
                                {{ form.intencion === 'buscar' ? 'Presupuesto máximo' : form.operacion === 'arriendo' ?
                                    'Canon de arriendo' : 'Precio' }}
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                <input v-model="form.precio" type="number" min="0" step="0.01"
                                    class="form-input pl-9 font-semibold" placeholder="0" />
                            </div>
                            <p v-if="error('precio')" class="form-error">{{ error('precio') }}</p>
                        </div>
                    </div>
                </section>

                <!-- VEHÍCULO -->
                <section v-if="esVehiculo"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Características del vehículo</h2>
                    </div>
                    <div class="p-5 sm:p-6 grid gap-5 md:grid-cols-2">
                        <div><label class="form-label">Marca</label><input v-model="form.marca" type="text"
                                class="form-input" /></div>
                        <div><label class="form-label">Modelo</label><input v-model="form.modelo" type="text"
                                class="form-input" /></div>
                        <div><label class="form-label">Año</label><input v-model="form.anio" type="number"
                                class="form-input" /></div>
                        <div><label class="form-label">Kilometraje</label><input v-model="form.kilometraje"
                                type="number" min="0" class="form-input" /></div>
                        <div><label class="form-label">Cilindraje</label><input v-model="form.cilindraje" type="number"
                                min="0" class="form-input" /></div>
                        <div>
                            <label class="form-label">Combustible</label>
                            <select v-model="form.combustible" class="form-input">
                                <option value="">Selecciona</option>
                                <option value="gasolina">Gasolina</option>
                                <option value="diesel">Diésel</option>
                                <option value="hibrido">Híbrido</option>
                                <option value="electrico">Eléctrico</option>
                                <option value="gas">Gas</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Transmisión</label>
                            <select v-model="form.transmision" class="form-input">
                                <option value="">Selecciona</option>
                                <option value="manual">Manual</option>
                                <option value="automatica">Automática</option>
                                <option value="cvt">CVT</option>
                            </select>
                        </div>
                        <div><label class="form-label">Color</label><input v-model="form.color" type="text"
                                class="form-input" /></div>
                        <div><label class="form-label">Placa</label><input v-model="form.placa" type="text"
                                class="form-input uppercase" /></div>
                        <div class="md:col-span-2"><label class="form-label">Observaciones</label><textarea
                                v-model="form.vehiculo_observaciones" rows="4" class="form-input"></textarea></div>
                    </div>

                    <div class="px-5 pb-6 sm:px-6 space-y-3">
                        <label class="form-check"><input v-model="form.unico_propietario" type="checkbox"
                                :true-value="true" :false-value="false" /><span>Único propietario</span></label>
                        <label class="form-check"><input v-model="form.documentacion_al_dia" type="checkbox"
                                :true-value="true" :false-value="false" /><span>Documentación al día</span></label>
                    </div>
                </section>

                <!-- PROPIEDAD -->
                <section v-if="esPropiedad"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Características de la propiedad</h2>
                    </div>

                    <div class="p-5 sm:p-6 grid gap-5 md:grid-cols-2">
                        <div><label class="form-label">Área total (m²)</label><input v-model="form.area_total"
                                type="number" min="0" step="0.01" class="form-input" /></div>
                        <div><label class="form-label">Área construida (m²)</label><input v-model="form.area_construida"
                                type="number" min="0" step="0.01" class="form-input" /></div>
                        <div><label class="form-label">Habitaciones</label><input v-model="form.habitaciones"
                                type="number" min="0" class="form-input" /></div>
                        <div><label class="form-label">Baños</label><input v-model="form.banos" type="number" min="0"
                                class="form-input" /></div>
                        <div><label class="form-label">Garajes</label><input v-model="form.garajes" type="number"
                                min="0" class="form-input" /></div>
                        <div>
                            <label class="form-label">Estrato</label>
                            <select v-model="form.estrato" class="form-input">
                                <option value="">Seleccionar</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="rural">Rural</option>
                            </select>
                        </div>
                        <div><label class="form-label">Pisos de la propiedad</label><input v-model="form.pisos"
                                type="number" min="0" class="form-input" /></div>
                        <div><label class="form-label">Piso</label><input v-model="form.piso" type="number" min="0"
                                class="form-input" /></div>
                        <div><label class="form-label">Antigüedad</label><input v-model="form.antiguedad" type="text"
                                class="form-input" placeholder="Ej. 5 años" /></div>
                        <div class="flex items-center pt-6">
                            <label class="form-check">
                                <input v-model="form.remodelada" type="checkbox" :true-value="true"
                                    :false-value="false" />
                                <span>Propiedad remodelada</span>
                            </label>
                        </div>

                        <!-- CONDICIONES DE ADMINISTRACIÓN (SOLO ARRIENDOS) -->
                        <div v-if="form.operacion === 'arriendo'"
                            class="md:col-span-2 rounded-2xl border border-amber-200/80 bg-amber-50/30 p-4 space-y-4">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Condiciones
                                de administración</label>
                            <div class="flex items-center gap-6">
                                <label class="form-check">
                                    <input v-model="form.incluye_administracion" type="checkbox" :true-value="true"
                                        :false-value="false" />
                                    <span>El canon incluye la administración</span>
                                </label>
                            </div>
                            <div v-if="!form.incluye_administracion" class="max-w-xs pt-1">
                                <label class="form-label">Valor mensual de administración</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                    <input v-model="form.precio_administracion" type="number" min="0" step="0.01"
                                        placeholder="Ej. 180000" class="form-input pl-9 font-semibold" />
                                </div>
                            </div>
                        </div>

                        <!-- SERVICIOS BÁSICOS Y DOCUMENTACIÓN -->
                        <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-700">💡 Servicios básicos y
                                documentación</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                <label class="form-check"><input v-model="form.tiene_servicios_publicos" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Servicios
                                        públicos</span></label>
                                <label class="form-check"><input v-model="form.tiene_agua" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Agua</span></label>
                                <label class="form-check"><input v-model="form.tiene_energia" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Energía</span></label>
                                <label class="form-check"><input v-model="form.tiene_gas" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Gas natural</span></label>
                                <label class="form-check"><input v-model="form.tiene_alcantarillado" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Alcantarillado</span></label>
                                <label class="form-check"><input v-model="form.tiene_internet" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Internet / Fibra</span></label>
                                <label class="form-check"><input v-model="form.tiene_tv" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Televisión</span></label>
                                <label class="form-check"><input v-model="form.papeles_al_dia" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Papeles al día</span></label>
                                <label class="form-check"><input v-model="form.libre_gravamen" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Libre de gravamen</span></label>
                            </div>
                        </div>

                        <!-- ZONAS COMUNES Y AMENIDADES -->
                        <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-700">🏊‍♂️ Zonas comunes y
                                amenidades</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                <label class="form-check"><input v-model="form.tiene_ascensor" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Ascensor</span></label>
                                <label class="form-check"><input v-model="form.tiene_parqueadero" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Parqueadero
                                        propio</span></label>
                                <label class="form-check"><input v-model="form.tiene_gimnasio" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Gimnasio</span></label>
                                <label class="form-check"><input v-model="form.tiene_piscina" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Piscina</span></label>
                                <label class="form-check"><input v-model="form.tiene_bbq" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Zona BBQ</span></label>
                                <label class="form-check"><input v-model="form.tiene_salon_comunal" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Salón comunal</span></label>
                                <label class="form-check"><input v-model="form.tiene_zona_infantil" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Parque infantil</span></label>
                                <label class="form-check"><input v-model="form.tiene_zonas_verdes" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Zonas verdes</span></label>
                                <label class="form-check"><input v-model="form.tiene_cancha_multiple" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Cancha múltiple</span></label>
                                <label class="form-check"><input v-model="form.tiene_sauna_turco" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Sauna / Turco</span></label>
                                <label class="form-check"><input v-model="form.tiene_coworking" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Zona Coworking</span></label>
                                <label class="form-check"><input v-model="form.tiene_parqueadero_visitantes"
                                        type="checkbox" :true-value="true" :false-value="false" /><span>Parqueadero
                                        visitantes</span></label>
                            </div>
                        </div>

                        <!-- COMODIDADES INTERIORES, SEGURIDAD Y POLÍTICAS -->
                        <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-700">🛋️ Interiores,
                                seguridad y políticas</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                <label class="form-check"><input v-model="form.tiene_porteria_24_7" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Vigilancia 24/7</span></label>
                                <label class="form-check"><input v-model="form.tiene_circuito_cerrado" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Cámaras / CCTV</span></label>
                                <label class="form-check"><input v-model="form.permite_mascotas" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Permite mascotas</span></label>
                                <label class="form-check"><input v-model="form.tiene_balcon" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Balcón</span></label>
                                <label class="form-check"><input v-model="form.tiene_terraza" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Terraza</span></label>
                                <label class="form-check"><input v-model="form.tiene_deposito" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Depósito / Bodega</span></label>
                                <label class="form-check"><input v-model="form.cocina_integral" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Cocina integral</span></label>
                                <label class="form-check"><input v-model="form.zona_lavanderia" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Zona de ropas</span></label>
                                <label class="form-check"><input v-model="form.tiene_calentador" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Calentador</span></label>
                                <label class="form-check"><input v-model="form.tiene_aire_acondicionado" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Aire
                                        acondicionado</span></label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Observaciones</label>
                            <textarea v-model="form.propiedad_observaciones" rows="4" class="form-input"></textarea>
                        </div>
                    </div>
                </section>

                <!-- UBICACIÓN Y COBERTURA TERRITORIAL -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Ubicación y cobertura geográfica</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">
                        <div class="space-y-2">
                            <label class="form-label mb-1">Punto central de referencia</label>
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <UbicacionSelector v-model:pais="ubicacion.pais"
                                    v-model:departamento="ubicacion.departamento" v-model:ciudad="ubicacion.ciudad" />
                            </div>
                            <p v-if="form.errors.pais_id" class="form-error">{{ form.errors.pais_id }}</p>
                            <p v-if="form.errors.division_administrativa_id" class="form-error">{{
                                form.errors.division_administrativa_id }}</p>
                            <p v-if="form.errors.ciudad_id" class="form-error">{{ form.errors.ciudad_id }}</p>
                            <p v-if="ubicacionError" class="form-error">{{ ubicacionError }}</p>
                        </div>

                        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-4 sm:p-5">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-[#172A3D]">
                                        📍 Cobertura geográfica
                                    </label>
                                    <p class="text-xs text-slate-500 font-medium">Define qué usuarios verán esta
                                        publicación según su ubicación.</p>
                                </div>
                                <span
                                    class="rounded-lg bg-amber-600 px-2.5 py-1 text-xs font-black text-white shadow-xs uppercase tracking-wide">
                                    {{ form.rango_cobertura }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <button v-for="opcion in opcionesCobertura" :key="opcion.valor" type="button"
                                    @click="form.rango_cobertura = opcion.valor"
                                    class="flex flex-col items-center justify-center rounded-xl border p-3 text-center transition"
                                    :class="form.rango_cobertura === opcion.valor
                                        ? 'border-amber-600 bg-white ring-2 ring-amber-400/50 shadow-sm'
                                        : 'border-slate-200 bg-white/70 hover:border-amber-300 hover:bg-white'">
                                    <span class="text-sm font-bold text-[#172A3D]">{{ opcion.nombre }}</span>
                                    <span class="mt-0.5 text-[11px] text-slate-500 font-medium">{{ opcion.detalle
                                    }}</span>
                                </button>
                            </div>

                            <p class="mt-3 text-xs font-medium text-amber-950/80">
                                ℹ️ <strong class="font-bold">Efecto:</strong> {{ descripcionCoberturaActual }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- IMÁGENES -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Imágenes</h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Puedes agregar hasta 8 imágenes en total.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div v-if="imagenesExistentes.length"
                            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            <div v-for="imagen in imagenesExistentes" :key="imagen.id"
                                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-sm">
                                <img :src="`/storage/${imagen.ruta}`" class="aspect-square w-full object-cover"
                                    alt="Imagen del clasificado" />
                                <div v-if="imagen.es_principal"
                                    class="absolute left-2 top-2 rounded-full bg-amber-600 px-2.5 py-1 text-[10px] font-black text-white shadow-sm">
                                    Principal
                                </div>
                                <div
                                    class="absolute inset-x-0 bottom-0 flex gap-2 bg-gradient-to-t from-black/80 to-transparent p-2 pt-8">
                                    <button v-if="!imagen.es_principal" type="button"
                                        @click="establecerPrincipalExistente(imagen.id)"
                                        class="flex-1 rounded-lg bg-white px-2 py-1.5 text-xs font-bold text-slate-800 hover:bg-slate-100 shadow-sm">
                                        Principal
                                    </button>
                                    <button type="button" @click="marcarParaEliminar(imagen.id)"
                                        class="rounded-lg bg-red-600 px-2 py-1.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="previews.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            <div v-for="(preview, index) in previews" :key="preview.url"
                                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-sm">
                                <img :src="preview.url" class="aspect-square w-full object-cover" alt="Nueva imagen" />
                                <div
                                    class="absolute left-2 top-2 rounded-full bg-amber-600 px-2.5 py-1 text-[10px] font-black text-white shadow-sm">
                                    Nueva
                                </div>
                                <button type="button" @click="eliminarNuevaImagen(index)"
                                    class="absolute right-2 top-2 rounded-full bg-red-600 px-2 py-1.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm">
                                    ×
                                </button>
                            </div>
                        </div>

                        <div
                            class="rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center transition hover:border-amber-400 hover:bg-amber-50/30 bg-slate-50">
                            <input id="edit-imagenes" type="file" accept="image/*" multiple class="hidden"
                                @change="seleccionarImagenes" />
                            <label for="edit-imagenes" class="cursor-pointer">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 border border-amber-200 text-2xl">
                                    📷
                                </div>
                                <p class="mt-4 font-bold text-[#172A3D]">Agregar más imágenes</p>
                                <p class="mt-1 text-xs text-slate-500 font-medium">JPG, JPEG, PNG o WEBP</p>
                            </label>
                        </div>
                        <p v-if="form.errors.imagenes" class="form-error">{{ form.errors.imagenes }}</p>
                    </div>
                </section>

                <!-- ACCIONES -->
                <section class="rounded-3xl border border-amber-200 bg-amber-50/80 p-6 shadow-xl backdrop-blur-md">
                    <div v-if="form.errors.general"
                        class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-xs sm:text-sm font-medium text-red-700">
                        {{ form.errors.general }}
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-bold text-amber-950">¿Listo para guardar?</h2>
                            <p class="mt-1 text-xs sm:text-sm text-amber-900 font-medium">
                                Los cambios se aplicarán de forma inmediata en la plataforma.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link :href="route('clasificados.show', { clasificado: clasificado.id })"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm">
                                Cancelar
                            </Link>

                            <button type="button" :disabled="form.processing" @click="actualizar"
                                class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                </svg>
                                {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                            </button>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-label {
    @apply mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider;
}

.form-input {
    @apply w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-sm outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100;
}

.form-check {
    @apply flex cursor-pointer items-center gap-2.5 text-xs sm:text-sm font-bold text-slate-700;
}

.form-check input {
    @apply h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 accent-amber-600;
}

.form-error {
    @apply mt-1 text-xs text-red-600 font-medium;
}

.fondo-carboncillo {
    background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-mobile.jpg');
}

@media (min-width: 768px) {
    .fondo-carboncillo {
        background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-desktop.jpg');
    }
}
</style>