<script setup>
import { computed, ref, watch, defineComponent, h, onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    publicaciones: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },

    categorias: {
        type: Array,
        default: () => [],
    },

    filtros: {
        type: Object,
        default: () => ({}),
    },
})

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage()
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user
    if (!usuario?.bloqueado_hasta) return false
    return new Date(usuario.bloqueado_hasta) > new Date()
})

/*
|--------------------------------------------------------------------------
| Filtros
|--------------------------------------------------------------------------
*/

const busqueda = ref(props.filtros?.search ?? '')
const categoria = ref(props.filtros?.categoria ?? '')
const subcategoria = ref(props.filtros?.subcategoria ?? '')
const estadoProducto = ref(props.filtros?.estado_producto ?? '')
const pais = ref(props.filtros?.pais ?? '')
const divisionAdministrativa = ref(
    props.filtros?.division_administrativa ?? ''
)
const ciudad = ref(props.filtros?.ciudad ?? '')
const soloGratis = ref(Boolean(props.filtros?.gratis))
const precioMinimo = ref(props.filtros?.precio_min ?? '')
const precioMaximo = ref(props.filtros?.precio_max ?? '')
const orden = ref(props.filtros?.orden ?? 'recientes')

const mostrarFiltros = ref(false)

/*
|--------------------------------------------------------------------------
| Categoría seleccionada
|--------------------------------------------------------------------------
*/

const categoriaSeleccionada = computed(() => {
    if (!categoria.value) {
        return null
    }

    return (
        props.categorias.find(
            item => String(item.id) === String(categoria.value)
        ) ?? null
    )
})

const subcategoriasDisponibles = computed(() => {
    return categoriaSeleccionada.value?.subcategorias ?? []
})

/*
|--------------------------------------------------------------------------
| Cuando cambia la categoría
|--------------------------------------------------------------------------
*/

watch(categoria, () => {
    subcategoria.value = ''
})

/*
|--------------------------------------------------------------------------
| Aplicar filtros
|--------------------------------------------------------------------------
*/

const aplicarFiltros = () => {
    router.get(
        route('marketplace.index'),
        {
            search: busqueda.value || undefined,
            categoria: categoria.value || undefined,
            subcategoria: subcategoria.value || undefined,
            estado_producto: estadoProducto.value || undefined,
            pais: pais.value || undefined,
            division_administrativa:
                divisionAdministrativa.value || undefined,
            ciudad: ciudad.value || undefined,
            gratis: soloGratis.value ? 1 : undefined,
            precio_min: precioMinimo.value || undefined,
            precio_max: precioMaximo.value || undefined,
            orden: orden.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Limpiar filtros
|--------------------------------------------------------------------------
*/

const limpiarFiltros = () => {
    busqueda.value = ''
    categoria.value = ''
    subcategoria.value = ''
    estadoProducto.value = ''
    pais.value = ''
    divisionAdministrativa.value = ''
    ciudad.value = ''
    soloGratis.value = false
    precioMinimo.value = ''
    precioMaximo.value = ''
    orden.value = 'recientes'

    aplicarFiltros()
}

/*
|--------------------------------------------------------------------------
| Formato de precio
|--------------------------------------------------------------------------
*/

const formatearPrecio = precio => {
    if (precio === null || precio === undefined || precio === '') {
        return ''
    }

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(precio))
}

/*
|--------------------------------------------------------------------------
| Estado del producto
|--------------------------------------------------------------------------
*/

const nombreEstadoProducto = estado => {
    const estados = {
        nuevo: 'Nuevo',
        como_nuevo: 'Como nuevo',
        buen_estado: 'Buen estado',
        usado: 'Usado',
        para_reparar: 'Para reparar',
    }

    return estados[estado] ?? estado
}

/*
|--------------------------------------------------------------------------
| Favoritos Unificados y Optimistas
|--------------------------------------------------------------------------
*/

const alternarFavorito = publicacion => {
    // Invertir de forma optimista el estado actual
    const nuevoEstado = !publicacion.es_favorito
    publicacion.es_favorito = nuevoEstado

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: publicacion.id,
            modulo: 'marketplace',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.flash?.es_favorito !== undefined) {
                    publicacion.es_favorito = page.props.flash.es_favorito
                }
            },
            onError: () => {
                publicacion.es_favorito = !nuevoEstado
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| Navegación
|--------------------------------------------------------------------------
*/

const abrirPublicacion = publicacion => {
    router.get(
        route(
            'marketplace.show',
            publicacion.id
        )
    )
}

/*
|--------------------------------------------------------------------------
| Imagen o Video principal para la tarjeta
|--------------------------------------------------------------------------
*/

const imagenPrincipal = publicacion => {
    if (publicacion.imagen_principal?.ruta) {
        return `/storage/${publicacion.imagen_principal.ruta}`
    }

    if (
        Array.isArray(publicacion.imagenes) &&
        publicacion.imagenes.length
    ) {
        return `/storage/${publicacion.imagenes[0].ruta}`
    }

    return null
}

const videoPrincipal = publicacion => {
    if (publicacion.video?.ruta) {
        return `/storage/${publicacion.video.ruta}`
    }

    if (typeof publicacion.video === 'string' && publicacion.video.trim() !== '') {
        return `/storage/${publicacion.video}`
    }

    return null
}

// Componente ligero en línea para generar la miniatura estática del video por canvas
const VideoThumbnail = defineComponent({
    props: {
        src: { type: String, required: true },
        alt: { type: String, default: '' },
        class: { type: String, default: '' }
    },
    setup(props) {
        const thumbnailSrc = ref(null)

        onMounted(() => {
            const video = document.createElement('video')
            video.src = props.src + '#t=0.5'
            video.crossOrigin = 'anonymous'
            video.muted = true
            video.playsInline = true
            video.preload = 'metadata'

            video.onloadeddata = () => {
                video.currentTime = 0.5
            }

            video.onseeked = () => {
                try {
                    const canvas = document.createElement('canvas')
                    canvas.width = video.videoWidth || 640
                    canvas.height = video.videoHeight || 480
                    const ctx = canvas.getContext('2d')
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
                    thumbnailSrc.value = canvas.toDataURL('image/jpeg', 0.85)
                } catch (e) {
                    // Fallback si hay restricciones de CORS
                    thumbnailSrc.value = null
                }
            }
        })

        return () => {
            if (thumbnailSrc.value) {
                return h('img', {
                    src: thumbnailSrc.value,
                    alt: props.alt,
                    class: props.class
                })
            }
            // Mientras carga o si falla el canvas, mostramos el video nativo pausado en el frame
            return h('video', {
                src: props.src + '#t=0.5',
                preload: 'metadata',
                muted: true,
                playsInline: true,
                class: props.class
            })
        }
    }
})
</script>

<template>

    <Head title="Marketplace | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="false">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo">

            <!-- =========================================================
                ENCABEZADO DE SECCIÓN CON TONO AZUL TRANSLÚCIDO
            ========================================================== -->
            <section class="border-b border-blue-200/80 bg-blue-100/25 backdrop-blur-md shadow-lg shadow-blue-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-blue-950">
                                    Comercio Comunitario
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Marketplace
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Compra, vende y encuentra oportunidades dentro de la comunidad.
                            </p>
                        </div>

                        <!-- ACCIONES DE CABECERA (Protegidas con estatus de bloqueo) -->
                        <div class="flex flex-wrap gap-3">
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Publicación restringida
                                </span>
                            </template>
                            <template v-else>
                                <a :href="route('marketplace.publicar')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-blue-500/25 transition hover:bg-blue-700">
                                    <span class="text-base">＋</span>
                                    Publicar producto
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main
                class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-blue-50 rounded-xl p-3 mt-2 shadow-blue-500/25">

                <!-- BUSCADOR CON SOMBRA AZUL -->
                <section class="rounded-2xl border border-slate-300 bg-white p-4 sm:p-5 shadow-2xl shadow-blue-500/15">
                    <div class="flex flex-col gap-3 md:flex-row">
                        <div class="relative flex-1">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                🔎
                            </span>
                            <input v-model="busqueda" type="text" placeholder="¿Qué estás buscando?"
                                class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                @keyup.enter="aplicarFiltros" />
                        </div>

                        <button type="button"
                            class="h-11 rounded-xl bg-blue-600 px-6 text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 shadow-md shadow-blue-500/20"
                            @click="aplicarFiltros">
                            Buscar
                        </button>

                        <button type="button"
                            class="h-11 rounded-xl border border-slate-300 bg-white px-5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs"
                            :class="{ '!border-blue-600 !bg-blue-50 text-blue-700': mostrarFiltros }"
                            @click="mostrarFiltros = !mostrarFiltros">
                            ⚙️ Filtros
                        </button>
                    </div>

                    <!-- PANEL DESPLEGABLE DE FILTROS -->
                    <div v-if="mostrarFiltros" class="mt-5 border-t border-slate-200 pt-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <!-- Categoría -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Categoría
                                </label>
                                <select v-model="categoria"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-blue-600 outline-none">
                                    <option value="">Todas las categorías</option>
                                    <option v-for="item in categorias" :key="item.id" :value="item.id">
                                        {{ item.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Subcategoría -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Subcategoría
                                </label>
                                <select v-model="subcategoria" :disabled="!subcategoriasDisponibles.length"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] disabled:bg-slate-100 disabled:text-slate-400 focus:border-blue-600 outline-none">
                                    <option value="">Todas</option>
                                    <option v-for="item in subcategoriasDisponibles" :key="item.id" :value="item.id">
                                        {{ item.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Estado del producto
                                </label>
                                <select v-model="estadoProducto"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-blue-600 outline-none">
                                    <option value="">Cualquier estado</option>
                                    <option value="nuevo">Nuevo</option>
                                    <option value="como_nuevo">Como nuevo</option>
                                    <option value="buen_estado">Buen estado</option>
                                    <option value="usado">Usado</option>
                                    <option value="para_reparar">Para reparar</option>
                                </select>
                            </div>

                            <!-- Orden -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Ordenar por
                                </label>
                                <select v-model="orden"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-blue-600 outline-none">
                                    <option value="recientes">Más recientes</option>
                                    <option value="precio_menor">Precio menor</option>
                                    <option value="precio_mayor">Precio mayor</option>
                                </select>
                            </div>

                            <!-- Precio mínimo -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Precio mínimo
                                </label>
                                <input v-model="precioMinimo" type="number" min="0" placeholder="$ 0"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm text-[#172A3D] focus:border-blue-600 outline-none" />
                            </div>

                            <!-- Precio máximo -->
                            <div>
                                <label class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Precio máximo
                                </label>
                                <input v-model="precioMaximo" type="number" min="0" placeholder="Sin límite"
                                    class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm text-[#172A3D] focus:border-blue-600 outline-none" />
                            </div>

                            <!-- Gratis -->
                            <div class="flex items-end">
                                <label
                                    class="flex h-10 w-full cursor-pointer items-center gap-3 rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 shadow-xs">
                                    <input v-model="soloGratis" type="checkbox"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                    <span>Solo publicaciones gratis</span>
                                </label>
                            </div>

                            <!-- Acciones -->
                            <div class="flex items-end gap-2">
                                <button type="button"
                                    class="h-10 flex-1 rounded-xl bg-blue-600 px-4 text-xs font-black text-white transition hover:bg-blue-700 shadow-xs"
                                    @click="aplicarFiltros">
                                    Aplicar
                                </button>
                                <button type="button"
                                    class="h-10 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                                    @click="limpiarFiltros">
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PUBLICACIONES -->
                <section class="mt-8">
                    <!-- ENCABEZADO BLANCO DE PUBLICACIONES CON SOMBRA AZUL -->
                    <div
                        class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between px-1 bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-blue-500/15">
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-blue-600 uppercase tracking-wide p-2">
                                Catálogo comunitario
                            </p>
                            <h2 class="mt-1 text-2xl font-black tracking-tight text-[#172A3D] p-2">
                                Publicaciones
                            </h2>
                        </div>

                        <span v-if="publicaciones?.total !== undefined"
                            class="text-xs sm:text-sm font-semibold text-slate-600">
                            {{ publicaciones.total }} resultado{{ publicaciones.total === 1 ? '' : 's' }}
                        </span>
                    </div>

                    <!-- ESTADO VACÍO -->
                    <div v-if="!publicaciones?.data?.length"
                        class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-blue-500/15 ">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-3xl text-blue-600 border border-blue-200">
                            🛍️
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-[#172A3D]">
                            No encontramos publicaciones
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                            Intenta cambiar los filtros o realizar una búsqueda diferente.
                        </p>
                        <button type="button"
                            class="mt-6 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs"
                            @click="limpiarFiltros">
                            Limpiar filtros
                        </button>
                    </div>

                    <!-- CUADRÍCULA DE PUBLICACIONES -->
                    <div v-else
                        class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-blue-500/15">
                        <article v-for="publicacion in publicaciones.data" :key="publicacion.id"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15 transition duration-300 hover:-translate-y-1 hover:border-blue-400">

                            <!-- Imagen o Video -->
                            <div class="relative aspect-[4/3] cursor-pointer overflow-hidden bg-slate-100"
                                @click="abrirPublicacion(publicacion)">

                                <!-- 1. Si hay imagen principal -->
                                <img v-if="imagenPrincipal(publicacion)" :src="imagenPrincipal(publicacion)"
                                    :alt="publicacion.titulo"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                                <!-- 2. Si no hay imagen pero hay video: usamos un componente de miniatura por canvas o video estilizado con poster dinámico -->
                                <VideoThumbnail v-else-if="videoPrincipal(publicacion)"
                                    :src="videoPrincipal(publicacion)" :alt="publicacion.titulo"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                                <!-- 3. Icono por defecto si no hay imagen ni video -->
                                <div v-else
                                    class="flex h-full w-full items-center justify-center text-4xl text-slate-300">
                                    📦
                                </div>

                                <!-- Indicador visual de video -->
                                <div v-if="videoPrincipal(publicacion) && !imagenPrincipal(publicacion)"
                                    class="absolute bottom-3 left-3 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-bold text-white backdrop-blur-sm flex items-center gap-1">
                                    <span>▶</span> Video
                                </div>

                                <!-- Botón de Favorito Unificado -->
                                <button type="button"
                                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-base shadow-md backdrop-blur transition hover:scale-110"
                                    :class="publicacion.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : ''"
                                    :title="publicacion.es_favorito ? 'Quitar de favoritos' : 'Agregar a favoritos'"
                                    @click.stop="alternarFavorito(publicacion)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-5 w-5 transition-colors"
                                        :class="publicacion.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>

                                <!-- Reservado -->
                                <div v-if="publicacion.estado_publicacion === 'reservada'"
                                    class="absolute bottom-3 left-3 rounded-full bg-amber-500 px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs">
                                    Reservado
                                </div>
                            </div>

                            <!-- Información -->
                            <div class="flex flex-1 flex-col p-4">
                                <div class="mb-1.5 flex items-center justify-between gap-2">
                                    <span v-if="publicacion.categoria?.nombre"
                                        class="text-[11px] font-bold text-blue-700">
                                        {{ publicacion.categoria.nombre }}
                                    </span>
                                    <span v-if="publicacion.estado_producto"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-[10px] font-bold text-slate-700">
                                        {{ nombreEstadoProducto(publicacion.estado_producto) }}
                                    </span>
                                </div>

                                <h3 class="cursor-pointer line-clamp-2 text-sm font-black text-[#172A3D] transition group-hover:text-blue-600"
                                    @click="abrirPublicacion(publicacion)">
                                    {{ publicacion.titulo }}
                                </h3>

                                <div class="mt-2.5">
                                    <span v-if="publicacion.es_gratis || Number(publicacion.precio) === 0"
                                        class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                        Gratis
                                    </span>
                                    <span v-else class="text-base font-black text-[#172A3D]">
                                        {{ formatearPrecio(publicacion.precio) }}
                                    </span>
                                </div>

                                <div v-if="publicacion.ciudad || publicacion.division_administrativa"
                                    class="mt-3 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                    <span>📍</span>
                                    <span class="truncate">
                                        {{ publicacion.ciudad?.nombre ?? publicacion.division_administrativa?.nombre ??
                                            '' }}
                                    </span>
                                </div>

                                <div v-if="publicacion.user"
                                    class="mt-auto border-t border-slate-100 pt-3 text-[11px] text-slate-400 font-medium">
                                    Publicado por
                                    <span class="font-bold text-slate-700">
                                        {{ publicacion.user.nombres ?? publicacion.user.name ?? 'Miembro' }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- PAGINACIÓN -->
                    <nav v-if="publicaciones?.links && publicaciones.links.length > 3"
                        class="mt-8 flex flex-wrap justify-center gap-2" aria-label="Paginación">
                        <template v-for="(link, index) in publicaciones.links" :key="index">
                            <button v-if="link.url" type="button"
                                class="rounded-lg border px-3.5 py-2 text-xs font-bold transition shadow-xs"
                                :class="link.active
                                    ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                    : 'border-slate-300 bg-white text-slate-700 hover:border-blue-500 hover:bg-blue-50'"
                                @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                                v-html="link.label"></button>
                            <span v-else
                                class="rounded-lg border border-slate-100 bg-slate-50 px-3.5 py-2 text-xs text-slate-300 font-medium"
                                v-html="link.label"></span>
                        </template>
                    </nav>
                </section>

            </main>
        </div>
    </AuthenticatedLayout>
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