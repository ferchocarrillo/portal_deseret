<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

const props = defineProps({
    denuncia: {
        type: Object,
        required: true,
    },
    publicacionUrl: {
        type: String,
        default: null,
    },
})

const page = usePage()

// Generador dinámico de URL por si el backend no la envía formateada
const urlElementoAfectado = computed(() => {
    if (props.publicacionUrl) {
        return props.publicacionUrl
    }

    if (!props.denuncia?.reportable_id || !props.denuncia?.modulo) {
        return null
    }

    const modulo = props.denuncia.modulo.toLowerCase()
    const id = props.denuncia.reportable_id

    switch (modulo) {
        case 'cursos':
            return route('cursos.show', id)
        case 'marketplace':
            return route('marketplace.show', id)
        case 'clasificados':
            return route('clasificados.show', id)
        case 'emprendimientos':
            return route('emprendimientos.show', id)
        case 'servicios':
            return route('servicios.show', id)
        default:
            return null
    }
})

// Formulario reactivo para actualizar el estado y las notas internas de seguimiento
const form = useForm({
    estado: props.denuncia.estado,
    notas_internas: props.denuncia.notas_internas || '',
})

// Enviar la actualización de seguimiento y cierre
const actualizarSeguimiento = () => {
    form.patch(route('admin.denuncias.seguimiento', props.denuncia.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'El seguimiento de la denuncia ha sido guardado.',
                timer: 2000,
                showConfirmButton: false,
            })
        }
    })
}

/*
|--------------------------------------------------------------------------
| ACCIONES DE MODERACIÓN Y SANCIONES
|--------------------------------------------------------------------------
*/

const bloquearPublicacion = () => {
    Swal.fire({
        title: '¿Bloquear publicación?',
        text: 'La publicación pasará a estado suspendido/bloqueado y dejará de ser visible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, bloquear publicación',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.bloquear-publicacion', props.denuncia.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Bloqueada!', 'La publicación ha sido bloqueada exitosamente.', 'success')
                }
            })
        }
    })
}

const bloquearUsuarioCompleto = () => {
    Swal.fire({
        title: '¿Bloquear usuario y publicaciones?',
        text: 'Esta acción suspenderá la cuenta del usuario infractor y bloqueará todas sus publicaciones activas.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, bloquear usuario',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.bloquear-usuario', props.denuncia.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Usuario bloqueado!', 'El usuario y sus publicaciones han sido suspendidos.', 'success')
                }
            })
        }
    })
}

const enviarAdvertencia = () => {
    const rules = page.props.communityRules || {}
    const optionsHtml = Object.entries(rules).map(([id, rule]) => {
        return `<option value="${id}">${rule.code} - ${rule.title}</option>`
    }).join('')

    Swal.fire({
        title: 'Enviar Notificación de Infracción',
        html: `
            <div class="text-left text-xs sm:text-sm space-y-3 pt-2">
                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">
                        Regla infringida <span class="text-red-500">*</span>
                    </label>
                    <select id="swal-rule-id" class="w-full rounded-lg border border-slate-300 p-2 text-xs font-semibold text-slate-800 focus:border-blue-500 focus:outline-none">
                        <option value="" disabled selected>Selecciona la norma violada...</option>
                        ${optionsHtml}
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">
                        Motivo / Detalle para el infractor <span class="text-red-500">*</span>
                    </label>
                    <textarea id="swal-detail" rows="3" class="w-full rounded-lg border border-slate-300 p-2 text-xs text-slate-800 focus:border-blue-500 focus:outline-none" placeholder="Explica detalladamente por qué se sanciona o cita el fragmento reportado..."></textarea>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Registrar y Notificar Infracción',
        cancelButtonText: 'Cancelar',
        focusConfirm: false,
        preConfirm: () => {
            const rule_id = document.getElementById('swal-rule-id').value
            const detail = document.getElementById('swal-detail').value.trim()

            if (!rule_id) {
                Swal.showValidationMessage('Debes seleccionar la norma que se infringió.')
                return false
            }
            if (!detail) {
                Swal.showValidationMessage('Debes indicar el detalle o motivo de la infracción.')
                return false
            }

            return { rule_id, detail }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.enviar-advertencia', props.denuncia.id), {
                rule_id: result.value.rule_id,
                detail: result.value.detail,
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire(
                        '¡Notificación enviada!',
                        'Se registró la infracción y el usuario verá la advertencia obligatoria al navegar.',
                        'success'
                    )
                }
            })
        }
    })
}

const aplicarBloqueoTemporal = () => {
    Swal.fire({
        title: 'Bloqueo temporal de publicaciones',
        text: 'Indica la cantidad de días que el usuario no podrá crear nuevas publicaciones:',
        icon: 'warning',
        input: 'number',
        inputAttributes: {
            min: 1,
            max: 365,
            step: 1
        },
        inputValue: 7,
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Aplicar bloqueo temporal',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Debes ingresar un número válido de días.'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.bloqueo-temporal', props.denuncia.id), {
                dias: result.value
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Aplicado!', `El usuario ha sido suspendido para publicar por ${result.value} días.`, 'success')
                }
            })
        }
    })
}

/*
|--------------------------------------------------------------------------
| ACCIONES DE REVERSIÓN Y LEVANTAMIENTO DE SANCIONES
|--------------------------------------------------------------------------
*/

const reactivarPublicacion = () => {
    Swal.fire({
        title: '¿Reactivar publicación?',
        text: 'La publicación volverá a estar visible para los usuarios.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, reactivar publicación',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.reactivar-publicacion', props.denuncia.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Reactivada!', 'La publicación ha sido restaurada.', 'success')
                }
            })
        }
    })
}

const quitarBloqueoTemporal = () => {
    Swal.fire({
        title: '¿Retirar bloqueo temporal?',
        text: 'El usuario podrá volver a crear publicaciones de inmediato.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, retirar bloqueo',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.quitar-bloqueo-temporal', props.denuncia.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Retirado!', 'El bloqueo temporal ha sido levantado.', 'success')
                }
            })
        }
    })
}

const reactivarUsuario = () => {
    Swal.fire({
        title: '¿Reactivar usuario y todo?',
        text: 'Esta acción devolverá el acceso al usuario y restaurará el estado activo de todas sus publicaciones.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, reactivar todo',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.denuncias.reactivar-usuario', props.denuncia.id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('¡Reactivado!', 'El usuario y sus publicaciones han sido restaurados exitosamente.', 'success')
                }
            })
        }
    })
}

const obtenerColorEstado = (estado) => {
    switch (estado) {
        case 'pendiente':
            return 'bg-amber-100 text-amber-800 border-amber-200'
        case 'en_revision':
            return 'bg-blue-100 text-blue-800 border-blue-200'
        case 'resuelto':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200'
        case 'descartado':
            return 'bg-slate-100 text-slate-700 border-slate-200'
        default:
            return 'bg-slate-100 text-slate-800 border-slate-200'
    }
}
</script>

<template>

    <Head :title="`Denuncia ${denuncia.codigo_unico} | Gestión`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section class="border-b border-slate-200/80 bg-white/60 backdrop-blur-md shadow-sm">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('admin.denuncias.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-red-600">
                        ← Volver al listado de incidencias
                    </Link>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-red-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-red-950">
                                    Seguimiento de Incidencia
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Caso: {{ denuncia.codigo_unico }}
                            </h1>
                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Reportado el {{ new Date(denuncia.created_at).toLocaleString() }}
                            </p>
                        </div>
                        <div>
                            <span
                                :class="['inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider', obtenerColorEstado(denuncia.estado)]">
                                Estado: {{ denuncia.estado.replace('_', ' ') }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- INFORMACIÓN DE LA DENUNCIA -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-xl shadow-slate-500/10 p-6 space-y-5">

                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Detalles del Reporte
                        </h2>

                        <div v-if="urlElementoAfectado">
                            <a :href="urlElementoAfectado" target="_blank"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md transition hover:bg-blue-700">
                                🔗 Ver publicación afectada ↗
                            </a>
                        </div>
                        <div v-else
                            class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
                            ⚠️ Enlace directo no disponible (ID o módulo no vinculado)
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs sm:text-sm">
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider">Módulo Afectado</span>
                            <span class="font-semibold capitalize text-[#172A3D]">{{ denuncia.modulo }}</span>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider">Motivo de
                                Denuncia</span>
                            <span class="font-semibold text-[#172A3D]">{{ denuncia.motivo }}</span>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider">Usuario que
                                Reporta</span>
                            <span class="font-semibold text-[#172A3D]">
                                {{ denuncia.usuario?.name ?? 'Desconocido' }} ({{ denuncia.usuario?.email ?? 'N/A' }})
                            </span>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-500 uppercase tracking-wider">Usuario Reportado
                                (Infractor)</span>
                            <span class="font-semibold text-red-700">
                                {{ denuncia.usuarioReportado?.name ?? 'No disponible / Elemento sin usuario' }}
                                <span v-if="denuncia.usuarioReportado?.email">({{ denuncia.usuarioReportado.email
                                    }})</span>
                            </span>
                        </div>
                        <div v-if="denuncia.fecha_cierre">
                            <span class="block font-bold text-slate-500 uppercase tracking-wider">Fecha y Hora de
                                Cierre</span>
                            <span class="font-semibold text-emerald-700">
                                {{ new Date(denuncia.fecha_cierre).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="block font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Descripción del Incidente
                        </span>
                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                            {{ denuncia.descripcion }}
                        </div>
                    </div>

                    <div v-if="denuncia.evidencia_path">
                        <span class="block font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Evidencia Adjunta
                        </span>
                        <a :href="`/storage/${denuncia.evidencia_path}`" target="_blank"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-slate-50 px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                            📎 Ver archivo de evidencia
                        </a>
                    </div>

                </section>

                <!-- PANEL DE ACCIONES DE MODERACIÓN Y SANCIONES -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-xl shadow-slate-500/10 p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Acciones de Moderación y Sanciones
                        </h2>
                        <p class="text-xs text-slate-500 font-medium">
                            Aplica medidas correctivas sobre la publicación o el usuario infractor según la gravedad.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <button type="button" @click="bloquearPublicacion"
                            class="flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 p-3.5 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 shadow-xs">
                            🚫 Bloquear la publicación
                        </button>

                        <button type="button" @click="bloquearUsuarioCompleto"
                            class="flex items-center justify-center gap-2 rounded-xl border border-red-300 bg-red-600 p-3.5 text-xs sm:text-sm font-black text-white transition hover:bg-red-700 shadow-sm">
                            🔒 Bloquear usuario y sus publicaciones
                        </button>

                        <button type="button" @click="enviarAdvertencia"
                            class="flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 p-3.5 text-xs sm:text-sm font-bold text-blue-700 transition hover:bg-blue-100 shadow-xs">
                            ✉️ Enviar recordatorio de normas
                        </button>

                        <button type="button" @click="aplicarBloqueoTemporal"
                            class="flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3.5 text-xs sm:text-sm font-bold text-amber-900 transition hover:bg-amber-100 shadow-xs">
                            ⏳ Aplicar bloqueo temporal (días)
                        </button>
                    </div>
                </section>

                <!-- PANEL DE REVERSIÓN Y LEVANTAMIENTO DE SANCIONES -->
                <section
                    class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-xl shadow-slate-500/10 p-6 space-y-4">
                    <div class="border-b border-emerald-100 pb-3">
                        <h2 class="text-base sm:text-lg font-bold text-emerald-950">
                            🔄 Reversión y Levantamiento de Sanciones
                        </h2>
                        <p class="text-xs text-emerald-800 font-medium">
                            Revierte medidas aplicadas previamente de forma individual o total según las necesidades.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <button type="button" @click="reactivarPublicacion"
                            class="flex items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-white p-3.5 text-xs sm:text-sm font-bold text-emerald-800 transition hover:bg-emerald-50 shadow-xs">
                            🔓 Reactivar publicación
                        </button>

                        <button type="button" @click="quitarBloqueoTemporal"
                            class="flex items-center justify-center gap-2 rounded-xl border border-amber-300 bg-white p-3.5 text-xs sm:text-sm font-bold text-amber-800 transition hover:bg-amber-50 shadow-xs">
                            ⚡ Quitar bloqueo temporal
                        </button>

                        <button type="button" @click="reactivarUsuario"
                            class="flex items-center justify-center gap-2 rounded-xl border border-emerald-400 bg-emerald-600 p-3.5 text-xs sm:text-sm font-black text-white transition hover:bg-emerald-700 shadow-sm">
                            ✅ Reactivar usuario y todo
                        </button>
                    </div>
                </section>

                <!-- PANEL DE GESTIÓN, SEGUIMIENTO Y CIERRE -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-xl shadow-slate-500/10 p-6">
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Gestión, Seguimiento y Cierre
                        </h2>
                        <p class="text-xs text-slate-500 font-medium">
                            Actualiza el estado del caso y añade notas internas o la resolución final.
                        </p>
                    </div>

                    <form @submit.prevent="actualizarSeguimiento" class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Cambiar Estado de la Incidencia <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.estado"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_revision">En revisión</option>
                                <option value="resuelto">Resuelto (Cierra el caso)</option>
                                <option value="descartado">Descartado (Cierra el caso)</option>
                            </select>
                            <p v-if="form.errors.estado" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.estado }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Notas Internas / Resolución del Caso
                            </label>
                            <textarea v-model="form.notas_internas" rows="4"
                                placeholder="Escribe aquí las acciones tomadas, comentarios de seguimiento o el motivo del cierre..."
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100"></textarea>
                            <p v-if="form.errors.notas_internas" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.notas_internas }}
                            </p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-red-500/20 transition hover:bg-red-700 disabled:opacity-50">
                                {{ form.processing ? 'Guardando cambios...' : 'Actualizar y Guardar Seguimiento' }}
                            </button>
                        </div>
                    </form>
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