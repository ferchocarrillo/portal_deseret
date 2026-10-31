<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    evento: Object,
});

// Función auxiliar para formatear fechas al estándar de inputs datetime-local (YYYY-MM-DDTHH:mm)
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const form = useForm({
    titulo: props.evento.titulo || '',
    descripcion: props.evento.descripcion || '',
    modalidad: props.evento.modalidad || 'presencial',
    ubicacion: props.evento.ubicacion || '',
    enlace_virtual: props.evento.enlace_virtual || '',
    fecha_inicio: formatDateForInput(props.evento.fecha_inicio),
    fecha_fin: formatDateForInput(props.evento.fecha_fin),
    es_gratuito: Boolean(props.evento.es_gratuito),
    precio: props.evento.precio || '',
    precio_preventa: props.evento.precio_preventa || '',
    fecha_limite_preventa: props.evento.fecha_limite_preventa ? props.evento.fecha_limite_preventa.split('T')[0] : '',
    precios_localidades: props.evento.precios_localidades ? [...props.evento.precios_localidades] : [],
    capacidad: props.evento.capacidad || '',
    estado: props.evento.estado || 'activo',
    ciudad_id: props.evento.ciudad_id || '',
    division_administrativa_id: props.evento.division_administrativa_id || '',
    imagen: null,
});

const addLocalidad = () => {
    form.precios_localidades.push({ zona: '', precio: '' });
};

const removeLocalidad = (index) => {
    form.precios_localidades.splice(index, 1);
};

const submit = () => {
    // Usamos post con _method: 'PUT' para permitir la subida de archivos (imagen) en la actualización
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('eventos.update', props.evento.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    ✏️ Editar Evento: {{ evento.titulo }}
                </h2>
                <Link :href="route('eventos.show', evento.id)"
                    class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                    &larr; Volver al detalle
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">

                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Estado del Evento -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Estado del Evento *</label>
                            <select v-model="form.estado"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="activo">Activo</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="suspendido">Suspendido</option>
                            </select>
                            <div v-if="form.errors.estado" class="text-red-600 text-xs mt-1">{{ form.errors.estado }}
                            </div>
                        </div>

                        <!-- Título -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Título del Evento *</label>
                            <input v-model="form.titulo" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                required />
                            <div v-if="form.errors.titulo" class="text-red-600 text-xs mt-1">{{ form.errors.titulo }}
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Descripción Completa *</label>
                            <textarea v-model="form.descripcion" rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                required></textarea>
                            <div v-if="form.errors.descripcion" class="text-red-600 text-xs mt-1">{{
                                form.errors.descripcion }}
                            </div>
                        </div>

                        <!-- Modalidad y Ubicación / Enlace -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Modalidad *</label>
                                <select v-model="form.modalidad"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="presencial">Presencial</option>
                                    <option value="virtual">Virtual</option>
                                    <option value="hibrido">Híbrido</option>
                                </select>
                                <div v-if="form.errors.modalidad" class="text-red-600 text-xs mt-1">{{
                                    form.errors.modalidad }}
                                </div>
                            </div>

                            <div v-if="form.modalidad === 'presencial' || form.modalidad === 'hibrido'">
                                <label class="block font-medium text-sm text-gray-700">Dirección o Ubicación Física
                                    *</label>
                                <input v-model="form.ubicacion" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                <div v-if="form.errors.ubicacion" class="text-red-600 text-xs mt-1">{{
                                    form.errors.ubicacion }}
                                </div>
                            </div>

                            <div v-if="form.modalidad === 'virtual' || form.modalidad === 'hibrido'">
                                <label class="block font-medium text-sm text-gray-700">Enlace Virtual (Transmisión/Zoom)
                                    *</label>
                                <input v-model="form.enlace_virtual" type="url"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                <div v-if="form.errors.enlace_virtual" class="text-red-600 text-xs mt-1">{{
                                    form.errors.enlace_virtual }}</div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Fecha y Hora de Inicio *</label>
                                <input v-model="form.fecha_inicio" type="datetime-local"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    required />
                                <div v-if="form.errors.fecha_inicio" class="text-red-600 text-xs mt-1">{{
                                    form.errors.fecha_inicio }}</div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Fecha y Hora de
                                    Finalización</label>
                                <input v-model="form.fecha_fin" type="datetime-local"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                <div v-if="form.errors.fecha_fin" class="text-red-600 text-xs mt-1">{{
                                    form.errors.fecha_fin }}
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Costos -->
                        <div class="border-t pt-6">
                            <div class="flex items-center mb-4">
                                <input v-model="form.es_gratuito" type="checkbox" id="es_gratuito"
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500 h-4 w-4" />
                                <label for="es_gratuito" class="ml-2 block font-medium text-sm text-gray-900">
                                    Este evento es totalmente gratuito
                                </label>
                            </div>

                            <div v-if="!form.es_gratuito"
                                class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Precio General *</label>
                                    <input v-model="form.precio" type="number" step="0.01" min="0"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                    <div v-if="form.errors.precio" class="text-red-600 text-xs mt-1">{{
                                        form.errors.precio }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Precio Preventa
                                        (Opcional)</label>
                                    <input v-model="form.precio_preventa" type="number" step="0.01" min="0"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Límite de Preventa</label>
                                    <input v-model="form.fecha_limite_preventa" type="date"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Bloque Dinámico: Localidades / Zonas -->
                        <div v-if="!form.es_gratuito" class="border-t pt-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block font-medium text-sm text-gray-700">Precios por Localidades / Zonas
                                    (Opcional)</label>
                                <button type="button" @click="addLocalidad"
                                    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded transition">
                                    + Agregar Zona
                                </button>
                            </div>

                            <div v-for="(loc, index) in form.precios_localidades" :key="index"
                                class="flex gap-4 items-center mb-2">
                                <input v-model="loc.zona" type="text" placeholder="Nombre de la zona (Ej. VIP, General)"
                                    class="w-2/3 border-gray-300 rounded-md shadow-sm text-sm" />
                                <input v-model="loc.precio" type="number" step="0.01" placeholder="Precio"
                                    class="w-1/4 border-gray-300 rounded-md shadow-sm text-sm" />
                                <button type="button" @click="removeLocalidad(index)"
                                    class="text-red-600 hover:text-red-800 font-bold px-2">
                                    &times;
                                </button>
                            </div>
                        </div>

                        <!-- Capacidad e Imagen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Capacidad Máxima de
                                    Asistentes</label>
                                <input v-model="form.capacidad" type="number" min="1"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                <div v-if="form.errors.capacidad" class="text-red-600 text-xs mt-1">{{
                                    form.errors.capacidad }}
                                </div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Actualizar Imagen Promocional
                                    (Banner)</label>
                                <div v-if="evento.imagen" class="mb-2">
                                    <img :src="`/storage/${evento.imagen}`" alt="Imagen actual"
                                        class="h-20 w-auto rounded object-cover" />
                                </div>
                                <input type="file" @input="form.imagen = $event.target.files[0]"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                                <div v-if="form.errors.imagen" class="text-red-600 text-xs mt-1">{{ form.errors.imagen
                                    }}</div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end gap-4 border-t pt-6">
                            <Link :href="route('eventos.show', evento.id)"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition">
                                Cancelar
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md text-sm font-medium transition disabled:opacity-50">
                                Guardar Cambios
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>