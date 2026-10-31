<script setup>
import { computed } from 'vue'; // <--- ¡Asegúrate de incluir computed aquí!
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

defineProps({
    ofertas: {
        type: Object,
        required: true,
    },

    estadisticas: {
        type: Object,
        required: true,
    },
})
const page = usePage();

const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user;
    if (!usuario?.bloqueado_hasta) return false;
    return new Date(usuario.bloqueado_hasta) > new Date();
});
const formatoFecha = (fecha) => {
    if (!fecha) return "Sin fecha";

    const fechaLimpia = String(fecha).substring(0, 10);
    const [anio, mes, dia] = fechaLimpia.split("-");

    if (!anio || !mes || !dia) return "Sin fecha";

    return new Date(
        Number(anio),
        Number(mes) - 1,
        Number(dia),
    ).toLocaleDateString("es-CO", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};

const formatoMoneda = (valor) => {
    if (valor === null || valor === undefined || valor === "") {
        return "";
    }

    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        maximumFractionDigits: 0,
    }).format(Number(valor));
};

const formatoSalario = (oferta) => {
    const mostrarSalario =
        oferta.mostrar_salario === true ||
        oferta.mostrar_salario === 1 ||
        oferta.mostrar_salario === "1";

    if (!mostrarSalario) {
        return "Salario no especificado";
    }

    switch (oferta.tipo_salario) {
        case "smlmv":
            return "Salario mínimo legal vigente";

        case "salario_especifico":
            return oferta.salario_monto !== null &&
                oferta.salario_monto !== undefined &&
                oferta.salario_monto !== ""
                ? formatoMoneda(oferta.salario_monto)
                : "Salario no especificado";

        case "a_convenir":
            return "Salario a convenir";

        default:
            return "Salario no especificado";
    }
};

const formatoModalidad = (modalidad) => {
    const modalidades = {
        presencial: "Presencial",
        remoto: "Remoto",
        hibrido: "Híbrido",
    };

    if (!modalidad) return "";

    return modalidades[String(modalidad).toLowerCase()] || modalidad;
};

const formatoPostulaciones = (cantidad) => {
    const total = Number(cantidad ?? 0);

    return `${total} ${total === 1 ? "postulante" : "postulantes"}`;
};

const total = (valor) => {
    return new Intl.NumberFormat("es-CO").format(Number(valor ?? 0));
};

const cerrarOferta = (oferta, estado) => {
    const esVacanteCubierta = estado === "cubierta";

    Swal.fire({
        icon: "warning",

        title: esVacanteCubierta
            ? "¿Vacante cubierta?"
            : "¿Cancelar proceso?",

        text: esVacanteCubierta
            ? `La oferta "${oferta.titulo}" se cerrará porque la vacante fue cubierta.`
            : `La oferta "${oferta.titulo}" se cerrará porque el proceso fue cancelado.`,

        showCancelButton: true,

        confirmButtonText: esVacanteCubierta
            ? "Sí, cerrar oferta"
            : "Sí, cancelar oferta",

        cancelButtonText: "Volver",

        confirmButtonColor: esVacanteCubierta
            ? "#172A3D"
            : "#C24141",

        cancelButtonColor: "#6B7280",

        reverseButtons: true,

        focusCancel: true,
    }).then((resultado) => {
        if (!resultado.isConfirmed) {
            return;
        }

        router.post(
            route("empleos.cerrar", oferta.id),
            { estado },
            {
                preserveScroll: true,

                onSuccess: () => {
                    Swal.fire({
                        icon: "success",

                        title: esVacanteCubierta
                            ? "Vacante cubierta"
                            : "Proceso cancelado",

                        text: esVacanteCubierta
                            ? "La oferta se cerró correctamente."
                            : "La oferta fue cancelada correctamente.",

                        confirmButtonText: "Aceptar",
                        confirmButtonColor: "#172A3D",
                    });
                },

                onError: (errors) => {
                    const primerError = Object.values(errors)[0];

                    Swal.fire({
                        icon: "error",

                        title: "No se pudo cerrar la oferta",

                        text: Array.isArray(primerError)
                            ? primerError[0]
                            : primerError ??
                            "Ocurrió un error al procesar el cierre.",

                        confirmButtonText: "Aceptar",
                        confirmButtonColor: "#172A3D",
                    });
                },
            }
        );
    });
};
</script>

<template>

    <Head title="Mis ofertas de empleo | Empleos" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ======================================================
     ENCABEZADO CON TONO AMARILLO/ÁMBAR TRANSLÚCIDO
======================================================= -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-[#D99A24]"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">
                                    Panel de administración
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis ofertas de empleo
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium">
                                Administra tus publicaciones y consulta el rendimiento de tus ofertas.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('empleos.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm hover:bg-slate-50 transition">
                                Ver bolsa de empleos
                            </Link>

                            <!-- Si el usuario está bloqueado temporalmente, mostramos aviso restrictivo -->
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-4 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Publicación restringida
                                </span>
                            </template>
                            <template v-else>
                                <Link :href="route('empleos.create')"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#D99A24] px-4 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/20 transition hover:bg-amber-600">
                                    <span class="mr-1.5 text-base">＋</span>
                                    Nueva oferta
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================================
                 CONTENIDO PRINCIPAL
            ======================================================= -->
            <div class="py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8 bg-[#D99A24]/20 rounded-xl p-3 mt-2">

                    <!-- Indicadores -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <!-- Total de ofertas -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-[#D99A24] font-bold">
                                    📁
                                </div>
                                <span
                                    class="rounded-full bg-amber-50 border border-amber-200 px-2.5 py-1 text-[10px] font-bold text-amber-900 uppercase">
                                    Publicaciones
                                </span>
                            </div>

                            <p class="mt-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Mis ofertas
                            </p>
                            <p class="mt-1 text-3xl font-black text-[#172A3D]">
                                {{ total(estadisticas.totalOfertas) }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 font-medium">
                                Ofertas que has publicado
                            </p>
                        </div>

                        <!-- Total de postulaciones -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold">
                                    👥
                                </div>
                                <span
                                    class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[10px] font-bold text-emerald-900 uppercase">
                                    Candidaturas
                                </span>
                            </div>

                            <p class="mt-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Postulaciones recibidas
                            </p>
                            <p class="mt-1 text-3xl font-black text-[#172A3D]">
                                {{ total(estadisticas.totalPostulaciones) }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 font-medium">
                                En todas tus ofertas
                            </p>
                        </div>

                        <!-- Visualizaciones -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 font-bold">
                                    👁️
                                </div>
                                <span
                                    class="rounded-full bg-slate-100 border border-slate-200 px-2.5 py-1 text-[10px] font-bold text-slate-600 uppercase">
                                    Estadística
                                </span>
                            </div>

                            <p class="mt-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Visualizaciones
                            </p>
                            <p class="mt-1 text-3xl font-black text-[#172A3D]">
                                {{ total(estadisticas.totalVisualizaciones) }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 font-medium">
                                Visitas acumuladas en tus ofertas
                            </p>
                        </div>

                        <!-- Estado de la gestión -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 font-bold">
                                    📊
                                </div>
                                <span
                                    class="rounded-full bg-blue-50 border border-blue-200 px-2.5 py-1 text-[10px] font-bold text-blue-900 uppercase">
                                    Seguimiento
                                </span>
                            </div>

                            <p class="mt-4 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Ofertas con postulantes
                            </p>
                            <p class="mt-1 text-3xl font-black text-[#172A3D]">
                                {{ total(estadisticas.totalOfertasConPostulaciones) }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 font-medium">
                                Ofertas que ya recibieron candidaturas
                            </p>
                        </div>
                    </div>

                    <!-- Encabezado del listado -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-1">
                        <div>
                            <h2 class="text-xl font-black text-[#172A3D]">
                                Mis publicaciones
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 font-medium">
                                Consulta y administra cada oferta.
                            </p>
                        </div>

                        <span class="text-xs sm:text-sm font-semibold text-slate-600">
                            {{ ofertas.total ?? ofertas.data?.length ?? 0 }} ofertas en el listado
                        </span>
                    </div>

                    <!-- Sin ofertas -->
                    <div v-if="!ofertas.data?.length"
                        class="rounded-3xl border border-slate-300 bg-white p-12 text-center shadow-2xl shadow-amber-500/15">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-3xl text-[#D99A24]">
                            📭
                        </div>
                        <h2 class="text-lg font-bold text-[#172A3D]">
                            Aún no has publicado ofertas
                        </h2>
                        <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                            Cuando publiques una oferta, aparecerá aquí.
                        </p>
                        <Link :href="route('empleos.create')"
                            class="mt-6 inline-flex items-center rounded-xl bg-[#D99A24] px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-amber-600">
                            Publicar una oferta
                        </Link>
                    </div>

                    <!-- Listado de ofertas -->
                    <div v-else class="space-y-4">
                        <article v-for="oferta in ofertas.data" :key="oferta.id"
                            class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15 transition duration-300 hover:border-amber-400 sm:p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                        {{ oferta.titulo }}
                                    </h3>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span v-if="oferta.modalidad"
                                            class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-900">
                                            {{ formatoModalidad(oferta.modalidad) }}
                                        </span>

                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ formatoSalario(oferta) }}
                                        </span>
                                    </div>

                                    <!-- Indicadores individuales de la oferta -->
                                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        <!-- Postulaciones -->
                                        <Link :href="route('empleos.postulantes', oferta.id)"
                                            class="flex items-center gap-3 rounded-xl border border-slate-300 bg-white p-4 transition hover:border-emerald-400 hover:bg-emerald-50/50 shadow-xs">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 font-bold">
                                                👥
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                    Postulaciones
                                                </p>
                                                <p class="text-lg font-black text-[#172A3D]">
                                                    {{ total(oferta.postulaciones_count) }}
                                                </p>
                                                <p class="text-[11px] font-bold text-emerald-700">
                                                    Ver candidatos →
                                                </p>
                                            </div>
                                        </Link>

                                        <!-- Gestionadas -->
                                        <div
                                            class="flex items-center gap-3 rounded-xl border border-slate-300 bg-white p-4 shadow-xs">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-700 font-bold">
                                                ✅
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                    Gestionadas
                                                </p>
                                                <p class="text-lg font-black text-[#172A3D]">
                                                    {{ total(oferta.postulaciones_gestionadas_count) }}
                                                </p>
                                                <p class="text-[11px] text-slate-500 font-medium">
                                                    Aceptadas o rechazadas
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Visualizaciones -->
                                        <div
                                            class="flex items-center gap-3 rounded-xl border border-slate-300 bg-white p-4 shadow-xs">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-purple-200 bg-purple-50 text-purple-700 font-bold">
                                                👁️
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                    Visualizaciones
                                                </p>
                                                <p class="text-lg font-black text-[#172A3D]">
                                                    {{ total(oferta.visualizaciones) }}
                                                </p>
                                                <p class="text-[11px] text-slate-500 font-medium">
                                                    Visitas a esta oferta
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-5 flex flex-col gap-2 text-xs sm:text-sm text-slate-500 font-medium sm:flex-row sm:flex-wrap sm:gap-x-6">
                                        <p>
                                            <span class="font-bold text-slate-700">Publicada:</span>
                                            {{ formatoFecha(oferta.fecha_publicacion) }}
                                        </p>
                                        <p>
                                            <span class="font-bold text-slate-700">Postulaciones:</span>
                                            {{ formatoPostulaciones(oferta.postulaciones_count) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex flex-col gap-2.5 sm:flex-row lg:shrink-0 pt-2 lg:pt-0">
                                    <Link :href="route('empleos.show', oferta.id)"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                        Ver oferta
                                    </Link>

                                    <Link :href="route('empleos.edit', oferta.id)"
                                        class="inline-flex items-center justify-center rounded-xl bg-[#172A3D] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#D99A24] hover:text-[#172A3D] shadow-xs">
                                        Editar
                                    </Link>

                                    <button v-if="oferta.estado === 'publicada'" type="button"
                                        @click="cerrarOferta(oferta, 'cubierta')"
                                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-black text-white transition hover:bg-emerald-700 shadow-xs">
                                        Vacante cubierta
                                    </button>

                                    <button v-if="oferta.estado === 'publicada'" type="button"
                                        @click="cerrarOferta(oferta, 'cancelada')"
                                        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-xs font-black text-white transition hover:bg-red-700 shadow-xs">
                                        Cancelar proceso
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Paginación -->
                    <div v-if="ofertas.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                        <template v-for="(link, index) in ofertas.links" :key="index">
                            <Link v-if="link.url" :href="link.url" preserve-scroll
                                class="rounded-lg border px-3 py-1.5 text-xs font-bold transition shadow-xs"
                                :class="link.active
                                    ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                    : 'border-slate-300 bg-white text-slate-700 hover:border-amber-400 hover:bg-amber-50'" v-html="link.label" />
                            <span v-else
                                class="rounded-lg border border-slate-100 bg-slate-50/80 px-3 py-1.5 text-xs text-slate-300"
                                v-html="link.label" />
                        </template>
                    </div>

                </div>
            </div>

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