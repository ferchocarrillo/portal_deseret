<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage, Link } from '@inertiajs/vue3'
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
    destacados: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const user = page.props.auth?.user ?? {
    name: 'Usuario',
}

const categorias = [
    {
        nombre: 'Empleos',
        descripcion: 'Oportunidades laborales',
        icono: '💼',
        imagen: '/images/landing/empleos.png',
        color: '#2F80ED',
        ruta: '/empleos',
    },
    {
        nombre: 'Servicios',
        descripcion: 'Personas que pueden ayudarte',
        icono: '🛠️',
        imagen: '/images/landing/servicios01.png',
        color: '#3B7A70',
        ruta: '/servicios',
    },
    {
        nombre: 'Cursos',
        descripcion: 'Aprende y desarrolla habilidades',
        icono: '📚',
        imagen: '/images/landing/cursos.jpg',
        color: '#496B86',
        ruta: '/cursos',
    },
    {
        nombre: 'Marketplace',
        descripcion: 'Compra y vende',
        icono: '🛍️',
        imagen: '/images/landing/marketplace.jpg',
        color: '#D99A24',
        ruta: '/marketplace',
    },
    {
        nombre: 'Clasificados',
        descripcion: 'Publicaciones de la comunidad',
        icono: '📣',
        imagen: '/images/landing/clasificados.png',
        color: '#C96A32',
        ruta: '/clasificados',
    },
    {
        nombre: 'Emprendimientos',
        descripcion: 'Ideas y negocios que crecen',
        icono: '🚀',
        imagen: '/images/landing/emprendimientos.jpg',
        color: '#A65A45',
        ruta: '/emprendimientos',
    },
    {
        nombre: 'Eventos',
        descripcion: 'Talleres, charlas y encuentros',
        icono: '📅',
        imagen: '/images/landing/eventos.jpg',
        color: '#8B5CF6',
        ruta: '/eventos',
    },
]

const heroSlides = [
    {
        etiqueta: 'Comunidad',
        titulo: 'Todo lo que buscas está más cerca.',
        texto: 'Personas, oportunidades, servicios y nuevas ideas en un solo lugar.',
        imagen: '/images/landing/banner1.png',
        ruta: '/servicios',
        boton: 'Explorar',
    },
    {
        etiqueta: 'Oportunidades',
        titulo: 'Encuentra nuevas posibilidades.',
        texto: 'Descubre empleos, proyectos y oportunidades para seguir creciendo.',
        imagen: '/images/landing/banner2.png',
        ruta: '/empleos',
        boton: 'Ver oportunidades',
    },
    {
        etiqueta: 'Comunidad que crece',
        titulo: 'Cuando nos conectamos, pasan cosas grandes.',
        texto: 'Conoce personas, proyectos y experiencias que hacen crecer nuestra comunidad.',
        imagen: '/images/landing/banner3.png',
        ruta: '/emprendimientos',
        boton: 'Descubrir',
    },
]

const slideActual = ref(0)
let intervaloSlider = null

function siguienteSlide() {
    slideActual.value = (slideActual.value + 1) % heroSlides.length
}

function irSlide(indice) {
    slideActual.value = indice
    reiniciarSlider()
}

function reiniciarSlider() {
    if (intervaloSlider) clearInterval(intervaloSlider)
    intervaloSlider = setInterval(() => {
        siguienteSlide()
    }, 6500)
}

function resolverImagen(item) {
    if (item.imagen) {
        return item.imagen;
    }
    return '/images/landing/banner1.png';
}

const listaDestacados = computed(() => {
    if (!props.destacados || props.destacados.length === 0) {
        return []
    }

    let items = props.destacados.map((item) => ({
        ...item,
        imagen: resolverImagen(item),
    }))

    return items
})

function obtenerRutaDestacado(destacado) {
    if (destacado.ruta) return destacado.ruta

    // Limpiamos el ID extrayendo solo la parte numérica final
    const rawId = String(destacado.id).split('-').pop()

    switch (destacado.tipo) {
        case 'Requerimiento': return `/servicios/requerimientos/${rawId}`
        case 'Servicios': return `/servicios/${rawId}`
        case 'Empleos': return `/empleos/${rawId}`
        case 'Marketplace': return `/marketplace/publicacion/${rawId}`
        case 'Clasificados':
        case 'Clasificado': return `/clasificados/${rawId}` // <-- Agregado para Clasificados
        case 'Cursos': return `/cursos/${rawId}`
        case 'Emprendimientos':
        case 'Emprendimiento': return `/emprendimientos/${rawId}`
        case 'Eventos':
        case 'Evento': return `/eventos/${rawId}`
        default: return '/dashboard'
    }
}

/* Vitrina de desplazamiento */
const destacadosContainer = ref(null)
let animacionDestacados = null
let vitrinaPausada = false
let scrollAcumulado = 0
let ultimoTiempo = 0
const velocidadPxPorSegundo = 30

function iniciarVitrina() {
    detenerVitrina()
    vitrinaPausada = false
    ultimoTiempo = 0

    const el = destacadosContainer.value
    if (el) scrollAcumulado = el.scrollLeft

    const animar = (tiempoActual) => {
        const container = destacadosContainer.value
        if (!container) return

        if (!ultimoTiempo) ultimoTiempo = tiempoActual
        const delta = Math.min((tiempoActual - ultimoTiempo) / 1000, 0.05)
        ultimoTiempo = tiempoActual

        if (!vitrinaPausada) {
            scrollAcumulado += velocidadPxPorSegundo * delta
            const mitad = container.scrollWidth / 2
            if (mitad > 0 && scrollAcumulado >= mitad) {
                scrollAcumulado -= mitad
            }
            container.scrollLeft = scrollAcumulado
        }

        animacionDestacados = requestAnimationFrame(animar)
    }

    animacionDestacados = requestAnimationFrame(animar)
}

function detenerVitrina() {
    if (animacionDestacados) {
        cancelAnimationFrame(animacionDestacados)
        animacionDestacados = null
    }
}

function pausarVitrina() {
    vitrinaPausada = true
}

function reanudarVitrina() {
    const container = destacadosContainer.value
    if (container) scrollAcumulado = container.scrollLeft
    ultimoTiempo = performance.now()
    vitrinaPausada = false
}

onMounted(() => {
    reiniciarSlider()
    nextTick(() => {
        iniciarVitrina()
    })
})

onBeforeUnmount(() => {
    if (intervaloSlider) {
        clearInterval(intervaloSlider)
        intervaloSlider = null
    }
    detenerVitrina()
})
</script>

<template>

    <Head title="Descubrir | Portal Deseret" />

    <AuthenticatedLayout>
        <div class="deseret-home min-h-screen overflow-x-hidden bg-[#FFF9F0] text-[#172A3D]">
            <!-- FONDO DECORATIVO -->
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="ambient ambient-one"></div>
                <div class="ambient ambient-two"></div>
                <div class="ambient ambient-three"></div>
            </div>

            <main class="mx-auto max-w-[1580px] px-4 py-4 sm:px-6 lg:px-8 lg:py-5 bg-[#F0FDFA]/50 rounded-xl p-3 mt-5">
                <!-- CABECERA -->
                <header class="mb-5 flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#172A3D] text-lg text-white shadow-lg shadow-[#172A3D]/10">
                            🐝
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[10px] font-black uppercase tracking-[0.22em] text-[#D99A24]">
                                Portal Deseret
                            </p>
                            <p class="truncate text-xs font-semibold text-[#526174]">
                                Conecta · Descubre · Crece
                            </p>
                        </div>
                    </div>

                    <Link href="/perfil"
                        class="flex shrink-0 items-center gap-2 rounded-full border border-white/80 bg-white/90 px-3 py-2 text-xs font-bold text-[#526174] shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-md">
                        <span class="hidden sm:inline">Hola, {{ user.name }}</span>
                        <span class="sm:hidden">Mi perfil</span>
                        <span class="text-[#D99A24]">→</span>
                    </Link>
                </header>

                <!-- 1. CATEGORÍAS -->
                <section id="descubrir" class="mb-7">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.22em] text-[#D99A24]">La colmena</p>
                            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-[#172A3D]">Explora Portal
                                Deseret</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-7">
                        <Link v-for="categoria in categorias" :key="categoria.nombre" :href="categoria.ruta"
                            class="category-card group relative overflow-hidden">
                            <img :src="categoria.imagen" :alt="categoria.nombre"
                                class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-[#172A3D]/95 via-[#172A3D]/45 to-[#172A3D]/10">
                            </div>

                            <div
                                class="absolute left-3 top-3 flex h-8 w-8 items-center justify-center rounded-xl bg-white/90 text-sm shadow-lg backdrop-blur">
                                {{ categoria.icono }}
                            </div>

                            <div class="relative flex min-h-[135px] flex-col justify-end p-3.5">
                                <h3 class="text-sm sm:text-base font-black leading-tight text-white">{{ categoria.nombre
                                    }}</h3>
                                <p
                                    class="mt-0.5 text-[9px] font-bold uppercase tracking-[0.08em] text-white/75 leading-tight">
                                    {{ categoria.descripcion }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </section>

                <!-- 2. DESTACADOS DE LA COMUNIDAD (DISEÑO HORIZONTAL MITAD Y MITAD) -->
                <section v-if="listaDestacados.length > 0" class="mb-7">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#D99A24]/15 text-base">
                                ✨
                            </span>
                            <div>
                                <h2 class="text-lg sm:text-xl font-black tracking-tight text-[#172A3D]">
                                    Destacados de la comunidad
                                </h2>
                                <p class="text-xs text-[#718096]">
                                    Solicitudes urgentes, servicios y novedades destacadas
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="pointer-events-none absolute left-0 top-0 z-10 h-full w-12 bg-gradient-to-r from-[#FFF9F0] to-transparent">
                        </div>
                        <div
                            class="pointer-events-none absolute right-0 top-0 z-10 h-full w-12 bg-gradient-to-l from-[#FFF9F0] to-transparent">
                        </div>

                        <div ref="destacadosContainer" class="destacados-vitrina" @mouseenter="pausarVitrina"
                            @mouseleave="reanudarVitrina" @touchstart="pausarVitrina" @touchend="reanudarVitrina">
                            <div class="destacados-track">
                                <!-- PRIMERA TANDA -->
                                <Link v-for="destacado in listaDestacados" :key="`t1-${destacado.id}`"
                                    :href="obtenerRutaDestacado(destacado)" class="destacado-card">
                                    <!-- Imagen a la izquierda (50%) -->
                                    <div class="destacado-parte-imagen">
                                        <img :src="destacado.imagen" :alt="destacado.titulo"
                                            class="h-full w-full object-cover" />
                                        <div class="absolute left-2 top-2 rounded-full px-2 py-0.5 text-[8px] font-black text-white shadow-md backdrop-blur-sm"
                                            :style="{ backgroundColor: destacado.color || '#3B7A70' }">
                                            {{ destacado.icono || '📌' }} {{ destacado.tipo }}
                                        </div>
                                    </div>

                                    <!-- Contenido a la derecha (50%) con fondo claro y limpio -->
                                    <div class="destacado-parte-info">
                                        <div class="min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <span
                                                    class="text-[8px] font-black uppercase tracking-wider text-[#D99A24] bg-[#D99A24]/10 px-1.5 py-0.5 rounded">
                                                    Destacado
                                                </span>
                                            </div>
                                            <h3
                                                class="line-clamp-2 text-xs sm:text-sm font-black leading-snug text-[#172A3D] hover:text-[#D99A24] transition-colors">
                                                {{ destacado.titulo }}
                                            </h3>
                                            <p class="mt-1 line-clamp-2 text-[10px] leading-relaxed text-[#526174]">
                                                {{ destacado.descripcion }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex items-center justify-between border-t border-slate-100 pt-1.5 text-[9px] font-black uppercase tracking-wider text-[#3B7A70]">
                                            <span>Conocer más</span>
                                            <span class="text-sm">→</span>
                                        </div>
                                    </div>
                                </Link>

                                <!-- SEGUNDA TANDA (BUCLE PARA CONTINUIDAD DE LA VITRINA) -->
                                <Link v-for="destacado in listaDestacados" :key="`t2-${destacado.id}`"
                                    :href="obtenerRutaDestacado(destacado)" class="destacado-card" aria-hidden="true"
                                    tabindex="-1">
                                    <div class="destacado-parte-imagen">
                                        <img :src="destacado.imagen" :alt="destacado.titulo"
                                            class="h-full w-full object-cover" />
                                        <div class="absolute left-2 top-2 rounded-full px-2 py-0.5 text-[8px] font-black text-white shadow-md backdrop-blur-sm"
                                            :style="{ backgroundColor: destacado.color || '#3B7A70' }">
                                            {{ destacado.icono || '📌' }} {{ destacado.tipo }}
                                        </div>
                                    </div>

                                    <div class="destacado-parte-info">
                                        <div class="min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <span
                                                    class="text-[8px] font-black uppercase tracking-wider text-[#D99A24] bg-[#D99A24]/10 px-1.5 py-0.5 rounded">
                                                    Destacado
                                                </span>
                                            </div>
                                            <h3
                                                class="line-clamp-2 text-xs sm:text-sm font-black leading-snug text-[#172A3D] hover:text-[#D99A24] transition-colors">
                                                {{ destacado.titulo }}
                                            </h3>
                                            <p class="mt-1 line-clamp-2 text-[10px] leading-relaxed text-[#526174]">
                                                {{ destacado.descripcion }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex items-center justify-between border-t border-slate-100 pt-1.5 text-[9px] font-black uppercase tracking-wider text-[#3B7A70]">
                                            <span>Conocer más</span>
                                            <span class="text-sm">→</span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. HERO / BANNER DE NOVEDADES -->
                <section class="hero-banner relative mb-6 overflow-hidden rounded-2xl bg-[#172A3D] shadow-md">
                    <div class="absolute inset-0">
                        <TransitionGroup name="hero-image">
                            <div :key="slideActual" class="absolute inset-0">
                                <img :src="heroSlides[slideActual].imagen" alt=""
                                    class="absolute inset-0 h-full w-full object-cover" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-[#172A3D]/95 via-[#172A3D]/70 to-[#172A3D]/30">
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <div class="relative flex h-full items-center justify-between px-5 py-4 sm:px-8">
                        <Transition name="hero-copy" mode="out-in">
                            <div :key="slideActual" class="max-w-xl">
                                <div
                                    class="mb-1.5 inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-2.5 py-0.5 text-[8px] font-black uppercase tracking-[0.16em] text-white backdrop-blur">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#F4C542]"></span>
                                    {{ heroSlides[slideActual].etiqueta }}
                                </div>

                                <h2
                                    class="text-base font-black leading-tight tracking-tight text-white sm:text-lg lg:text-xl">
                                    {{ heroSlides[slideActual].titulo }}
                                </h2>

                                <p class="mt-1 max-w-lg text-[11px] sm:text-xs text-white/80 line-clamp-1">
                                    {{ heroSlides[slideActual].texto }}
                                </p>

                                <div class="mt-3 flex items-center gap-2">
                                    <Link :href="heroSlides[slideActual].ruta"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-[#F4C542] px-3.5 py-1.5 text-[9px] font-black text-[#172A3D] shadow transition hover:bg-white">
                                        {{ heroSlides[slideActual].boton }}
                                        <span>→</span>
                                    </Link>
                                </div>
                            </div>
                        </Transition>

                        <div class="flex items-center gap-1 self-end sm:self-center">
                            <button v-for="(_, indice) in heroSlides" :key="indice" type="button"
                                @click="irSlide(indice)" class="h-1.5 rounded-full transition-all duration-300"
                                :class="indice === slideActual ? 'w-6 bg-[#F4C542]' : 'w-2 bg-white/40'"></button>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hero-banner {
    height: 165px;
    min-height: 165px;
}

.category-card {
    min-height: 135px;
    border-radius: 1.15rem;
    background: #172A3D;
    box-shadow: 0 8px 22px rgba(23, 42, 61, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(23, 42, 61, 0.16);
}

.destacados-vitrina {
    position: relative;
    width: 100%;
    overflow-x: scroll;
    overflow-y: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
    touch-action: pan-x pan-y;
    padding-top: 0.2rem;
    padding-bottom: 0.5rem;
}

.destacados-vitrina::-webkit-scrollbar {
    display: none;
}

.destacados-track {
    display: flex;
    width: max-content;
    gap: 0;
    flex-wrap: nowrap;
}

/* Rediseño de la Card: Horizontal (Mitad y Mitad) y fondo claro/limpio */
.destacado-card {
    position: relative;
    flex: 0 0 380px;
    width: 380px;
    height: 155px;
    overflow: hidden;
    display: flex;
    flex-direction: row;
    /* Disposición horizontal: Imagen izquierda, contenido derecha */
    border-radius: 1.15rem;
    background: #FFFFFF;
    /* Fondo blanco limpio para eliminar el aspecto lúgubre */
    border: 1px solid rgba(23, 42, 61, 0.08);
    box-shadow: 0 6px 18px rgba(23, 42, 61, 0.06);
    margin-right: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.destacado-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(23, 42, 61, 0.12);
    border-color: rgba(217, 154, 36, 0.4);
}

.destacado-parte-imagen {
    position: relative;
    width: 42%;
    /* Mitad izquierda para la imagen */
    height: 100%;
    overflow: hidden;
    background: #f1f5f9;
}

.destacado-parte-imagen img {
    height: 100%;
    width: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.destacado-card:hover .destacado-parte-imagen img {
    transform: scale(1.08);
}

.destacado-parte-info {
    width: 58%;
    /* Mitad derecha para el contenido */
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0.75rem 0.85rem;
    background: #FFFFFF;
    /* Fondo claro */
}

.ambient {
    position: absolute;
    border-radius: 9999px;
    filter: blur(75px);
    opacity: 0.22;
}

.ambient-one {
    width: 320px;
    height: 320px;
    left: -100px;
    top: 90px;
    background: #F4C542;
}

.ambient-two {
    width: 340px;
    height: 340px;
    right: -120px;
    top: 380px;
    background: #2F526D;
}

.ambient-three {
    width: 260px;
    height: 260px;
    left: 40%;
    bottom: 60px;
    background: #D99A24;
}
</style>