<script setup>

import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { countries } from '@/Data/countries';
import Swal from 'sweetalert2';


const form = useForm({

    // ==========================================================
    // DECISIÓN INICIAL
    // ==========================================================

    es_miembro: null,


    // ==========================================================
    // DATOS PERSONALES
    // ==========================================================

    nombres: '',
    apellidos: '',
    tipo_documento: '',
    documento: '',


    // ==========================================================
    // UBICACIÓN
    // ==========================================================

    pais: 'CO',
    region: '',
    ciudad: '',


    // ==========================================================
    // CONTACTO
    // ==========================================================

    telefono: '',
    email: '',


    // ==========================================================
    // INFORMACIÓN COMO MIEMBRO
    // ==========================================================

    estaca: '',
    barrio: '',
    nombre_obispo: '',


    // ==========================================================
    // INFORMACIÓN DE NO MIEMBRO
    // ==========================================================

    motivo_ingreso: '',


    // ==========================================================
    // AVAL
    // ==========================================================

    tiene_aval: null,

    aval_nombres: '',
    aval_apellidos: '',
    aval_email: '',
    aval_telefono: '',
    aval_barrio: '',


    // ==========================================================
    // CONSENTIMIENTOS
    // ==========================================================

    acepta_terminos: false,
    ha_leido_privacidad: false,
    autoriza_tratamiento_datos: false,


    // ==========================================================
    // INFORMACIÓN ADICIONAL
    // ==========================================================

    observaciones: '',
});


// ==========================================================
// ESTADOS COMPUTADOS
// ==========================================================

const esMiembro = computed(() => {
    return form.es_miembro === '1';
});


const noEsMiembro = computed(() => {
    return form.es_miembro === '0';
});


const tieneAval = computed(() => {
    return form.tiene_aval === '1';
});


// ==========================================================
// EL USUARIO PUEDE CONTINUAR
// ==========================================================
// Miembro:
//      puede continuar inmediatamente.
//
// No miembro:
//      solamente puede continuar si tiene aval.
//
// ==========================================================

const puedeContinuar = computed(() => {
    return esMiembro.value || (
        noEsMiembro.value &&
        tieneAval.value
    );
});


// ==========================================================
// PAÍS SELECCIONADO
// ==========================================================

const paisSeleccionado = computed(() => {

    return countries.find(
        country => country.code === form.pais
    );

});


// ==========================================================
// INDICATIVO TELEFÓNICO
// ==========================================================

const codigoPais = computed(() => {

    return paisSeleccionado.value?.dial_code ?? '';

});


// ==========================================================
// ENVÍO DEL FORMULARIO
// ==========================================================

const submit = () => {

    // Seguridad adicional del lado de la interfaz.
    // El backend también debe validar esta condición.
    if (!puedeContinuar.value) {
        return;
    }


    form.post(route('solicitud.ingreso.store'), {

        preserveScroll: true,

        onSuccess: () => {

            Swal.fire({

                icon: 'success',

                title: '¡Solicitud recibida!',

                text: 'Hemos recibido tu solicitud. Agradecemos que la hayas enviado. Vamos a validar la información y te notificaremos el resultado. ¡Hasta pronto!',

                confirmButtonText: 'Entendido',

                confirmButtonColor: '#D99A24',

            });


            form.reset();


            // Mantener Colombia como país predeterminado.
            form.pais = 'CO';

        },

    });

};

</script>


<template>

  <GuestLayout minWidth="sm:min-w-6xl" maxWidth="sm:max-w-7xl" backgroundImage="/images/landing/solicitud-bg.png">

        <Head title="Solicitud de ingreso" />

        <div class="mx-auto w-full">



            <!-- ===================================================== -->
            <!-- ENCABEZADO -->
            <!-- ===================================================== -->

            <div class="mb-8 text-center">

                <h1 class="text-2xl font-bold text-gray-900">
                    Solicitud de ingreso
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    Completa la información para solicitar tu admisión a la
                    plataforma.
                </p>

            </div>


            <form @submit.prevent="submit" class="space-y-8">


                <!-- ===================================================== -->
                <!-- 1. TIPO DE SOLICITANTE -->
                <!-- ===================================================== -->

                <section class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Antes de comenzar
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Esta información determinará el proceso que debes
                            seguir para solicitar tu ingreso.
                        </p>

                    </div>


                    <InputLabel value="¿Eres miembro?" class="mb-3" />


                    <div class="grid gap-4 sm:grid-cols-2">


                        <!-- ================================================= -->
                        <!-- MIEMBRO -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-center rounded-lg border p-4 transition" :class="form.es_miembro === '1'
                            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100'
                            : 'border-gray-200 hover:border-gray-300'">

                            <input v-model="form.es_miembro" type="radio" value="1" class="h-4 w-4 text-indigo-600" />

                            <div class="ms-3">

                                <div class="font-medium text-gray-900">
                                    Sí, soy miembro
                                </div>

                                <div class="text-sm text-gray-500">
                                    Ya pertenezco a la organización.
                                </div>

                            </div>

                        </label>


                        <!-- ================================================= -->
                        <!-- NO MIEMBRO -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-center rounded-lg border p-4 transition" :class="form.es_miembro === '0'
                            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100'
                            : 'border-gray-200 hover:border-gray-300'">

                            <input v-model="form.es_miembro" type="radio" value="0" class="h-4 w-4 text-indigo-600" />

                            <div class="ms-3">

                                <div class="font-medium text-gray-900">
                                    No soy miembro
                                </div>

                                <div class="text-sm text-gray-500">
                                    Quiero solicitar mi admisión.
                                </div>

                            </div>

                        </label>

                    </div>


                    <InputError class="mt-2" :message="form.errors.es_miembro" />

                </section>



                <!-- ===================================================== -->
                <!-- 2. VALIDACIÓN DE AVAL PARA NO MIEMBROS -->
                <!-- ===================================================== -->

                <section v-if="noEsMiembro" class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Aval de un miembro
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Para solicitar el ingreso como no miembro debes
                            contar con el aval de una persona que sea miembro.
                        </p>

                    </div>


                    <InputLabel value="¿Cuentas con el aval de un miembro?" class="mb-3" />


                    <div class="grid gap-4 sm:grid-cols-2">


                        <!-- ================================================= -->
                        <!-- SÍ TIENE AVAL -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-center rounded-lg border p-4 transition" :class="form.tiene_aval === '1'
                            ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100'
                            : 'border-gray-200 hover:border-gray-300'">

                            <input v-model="form.tiene_aval" type="radio" value="1" class="h-4 w-4 text-indigo-600" />

                            <div class="ms-3">

                                <div class="font-medium text-gray-900">
                                    Sí, tengo el aval de un miembro
                                </div>

                                <div class="text-sm text-gray-500">
                                    Puedo proporcionar sus datos.
                                </div>

                            </div>

                        </label>


                        <!-- ================================================= -->
                        <!-- NO TIENE AVAL -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-center rounded-lg border p-4 transition" :class="form.tiene_aval === '0'
                            ? 'border-red-400 bg-red-50 ring-2 ring-red-100'
                            : 'border-gray-200 hover:border-gray-300'">

                            <input v-model="form.tiene_aval" type="radio" value="0" class="h-4 w-4 text-red-600" />

                            <div class="ms-3">

                                <div class="font-medium text-gray-900">
                                    No tengo un aval
                                </div>

                                <div class="text-sm text-gray-500">
                                    No cuento actualmente con un miembro que
                                    pueda respaldar mi solicitud.
                                </div>

                            </div>

                        </label>

                    </div>


                    <InputError class="mt-2" :message="form.errors.tiene_aval" />


                    <!-- ================================================= -->
                    <!-- MENSAJE CUANDO NO TIENE AVAL -->
                    <!-- ================================================= -->

                    <div v-if="form.tiene_aval === '0'" class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-5">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 text-amber-600">
                                ⚠
                            </div>

                            <div>

                                <h3 class="font-semibold text-amber-900">
                                    No puedes continuar con la solicitud
                                </h3>

                                <p class="mt-1 text-sm leading-6 text-amber-800">
                                    Para solicitar el ingreso al Portal Deseret
                                    debes ser miembro o contar con el aval de
                                    un miembro.
                                </p>

                            </div>

                        </div>

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 3. INFORMACIÓN PERSONAL -->
                <!-- ===================================================== -->

                <section v-if="puedeContinuar" class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Información personal
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Ingresa tus datos básicos.
                        </p>

                    </div>


                    <div class="grid gap-5 sm:grid-cols-2">


                        <!-- Nombres -->
                        <div>

                            <InputLabel for="nombres" value="Nombres" />

                            <TextInput id="nombres" v-model="form.nombres" type="text"
                                class="mt-1 block w-full bg-white/35 text-gray-900" autocomplete="given-name" />

                            <InputError class="mt-2" :message="form.errors.nombres" />

                        </div>


                        <!-- Apellidos -->
                        <div>

                            <InputLabel for="apellidos" value="Apellidos" />

                            <TextInput id="apellidos" v-model="form.apellidos" type="text"
                                class="mt-1 block w-full bg-white/35 text-gray-900" autocomplete="family-name" />

                            <InputError class="mt-2" :message="form.errors.apellidos" />

                        </div>


                        <!-- Tipo de documento -->
                        <div>

                            <InputLabel for="tipo_documento" value="Tipo de documento" />

                            <select id="tipo_documento" v-model="form.tipo_documento"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    Selecciona una opción
                                </option>

                                <option value="CC">
                                    Cédula de ciudadanía
                                </option>

                                <option value="CE">
                                    Cédula de extranjería
                                </option>

                                <option value="TI">
                                    Tarjeta de identidad
                                </option>

                                <option value="PAS">
                                    Pasaporte
                                </option>

                            </select>

                            <InputError class="mt-2" :message="form.errors.tipo_documento" />

                        </div>


                        <!-- Número de documento -->
                        <div>

                            <InputLabel for="documento" value="Número de documento" />

                            <TextInput id="documento" v-model="form.documento" type="text" maxlength="10"
                                inputmode="numeric" class="mt-1 block w-full bg-white text-gray-900"
                                autocomplete="off" />

                            <InputError class="mt-2" :message="form.errors.documento" />

                        </div>


                        <!-- País -->
                        <div>

                            <InputLabel for="pais" value="País" />

                            <select id="pais" v-model="form.pais"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option v-for="country in countries" :key="country.code" :value="country.code">
                                    {{ country.name }}
                                    ({{ country.dial_code }})
                                </option>

                            </select>

                            <InputError class="mt-2" :message="form.errors.pais" />

                        </div>


                        <!-- Región -->
                        <div>

                            <InputLabel for="region" value="Departamento / Estado / Región" />

                            <TextInput id="region" v-model="form.region" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" autocomplete="address-level1"
                                placeholder="Ej. Cundinamarca" />

                            <InputError class="mt-2" :message="form.errors.region" />

                        </div>


                        <!-- Ciudad -->
                        <div>

                            <InputLabel for="ciudad" value="Ciudad / Municipio" />

                            <TextInput id="ciudad" v-model="form.ciudad" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" autocomplete="address-level2" />

                            <InputError class="mt-2" :message="form.errors.ciudad" />

                        </div>

                    </div>



                    <!-- ================================================= -->
                    <!-- TELÉFONO -->
                    <!-- ================================================= -->

                    <div class="mt-5">

                        <InputLabel for="telefono" value="Teléfono" />

                        <div class="mt-1 flex">


                            <!-- Indicativo -->
                            <div
                                class="flex min-w-[90px] items-center justify-center rounded-l-md border border-r-0 border-gray-300 bg-gray-100 px-3 text-sm font-medium text-gray-700">
                                {{ codigoPais || '+--' }}
                            </div>


                            <!-- Número -->
                            <TextInput id="telefono" v-model="form.telefono" type="tel" maxlength="15" inputmode="tel"
                                class="block w-full rounded-l-none bg-white text-gray-900" autocomplete="tel"
                                placeholder="Número de teléfono" />

                        </div>


                        <InputError class="mt-2" :message="form.errors.telefono" />

                    </div>



                    <!-- ================================================= -->
                    <!-- EMAIL -->
                    <!-- ================================================= -->

                    <div class="mt-5">

                        <InputLabel for="email" value="Correo electrónico" />

                        <TextInput id="email" v-model="form.email" type="email"
                            class="mt-1 block w-full bg-white text-gray-900" autocomplete="email"
                            placeholder="ejemplo@correo.com" />

                        <InputError class="mt-2" :message="form.errors.email" />

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 4. INFORMACIÓN COMO MIEMBRO -->
                <!-- ===================================================== -->

                <section v-if="esMiembro" class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Información como miembro
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Indica la información de tu estaca y barrio de
                            asistencia para facilitar la verificación de tu
                            solicitud.
                        </p>

                    </div>


                    <div class="grid gap-5 sm:grid-cols-2">


                        <!-- Estaca -->
                        <div>

                            <InputLabel for="estaca" value="Estaca a la que asistes" />

                            <TextInput id="estaca" v-model="form.estaca" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.estaca" />

                        </div>


                        <!-- Barrio -->
                        <div>

                            <InputLabel for="barrio" value="Barrio al que asistes" />

                            <TextInput id="barrio" v-model="form.barrio" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.barrio" />

                        </div>


                        <!-- Obispo -->
                        <div class="sm:col-span-2">

                            <InputLabel for="nombre_obispo" value="Nombre del obispo" />

                            <TextInput id="nombre_obispo" v-model="form.nombre_obispo" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.nombre_obispo" />

                        </div>

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 5. INFORMACIÓN DE NO MIEMBRO -->
                <!-- ===================================================== -->

                <section v-if="noEsMiembro && tieneAval"
                    class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Solicitud de admisión
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Como no miembro, tu solicitud deberá ser revisada
                            antes de ser admitida en la plataforma.
                        </p>

                    </div>


                    <div>

                        <InputLabel for="motivo_ingreso" value="¿Por qué deseas ingresar a la plataforma?" />

                        <textarea id="motivo_ingreso" v-model="form.motivo_ingreso" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white/35 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>

                        <InputError class="mt-2" :message="form.errors.motivo_ingreso" />

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 6. INFORMACIÓN DEL AVAL -->
                <!-- ===================================================== -->

                <section v-if="noEsMiembro && tieneAval"
                    class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Información del aval
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Proporciona los datos del miembro que respalda tu
                            solicitud de ingreso.
                        </p>

                    </div>


                    <div class="grid gap-5 sm:grid-cols-2">


                        <!-- Nombres -->
                        <div>

                            <InputLabel for="aval_nombres" value="Nombres del aval" />

                            <TextInput id="aval_nombres" v-model="form.aval_nombres" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.aval_nombres" />

                        </div>


                        <!-- Apellidos -->
                        <div>

                            <InputLabel for="aval_apellidos" value="Apellidos del aval" />

                            <TextInput id="aval_apellidos" v-model="form.aval_apellidos" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.aval_apellidos" />

                        </div>


                        <!-- Email -->
                        <div>

                            <InputLabel for="aval_email" value="Correo electrónico del aval" />

                            <TextInput id="aval_email" v-model="form.aval_email" type="email"
                                class="mt-1 block w-full bg-white text-gray-900" autocomplete="email"
                                placeholder="ejemplo@correo.com" />

                            <InputError class="mt-2" :message="form.errors.aval_email" />

                        </div>


                        <!-- Teléfono -->
                        <div>

                            <InputLabel for="aval_telefono" value="Teléfono del aval" />

                            <TextInput id="aval_telefono" v-model="form.aval_telefono" type="tel" maxlength="15"
                                inputmode="tel" class="mt-1 block w-full bg-white text-gray-900" autocomplete="tel"
                                placeholder="Número de teléfono" />

                            <InputError class="mt-2" :message="form.errors.aval_telefono" />

                        </div>


                        <!-- Barrio -->
                        <div class="sm:col-span-2">

                            <InputLabel for="aval_barrio" value="Barrio al que asiste el aval" />

                            <TextInput id="aval_barrio" v-model="form.aval_barrio" type="text"
                                class="mt-1 block w-full bg-white text-gray-900" />

                            <InputError class="mt-2" :message="form.errors.aval_barrio" />

                        </div>

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 7. INFORMACIÓN ADICIONAL -->
                <!-- ===================================================== -->

                <section v-if="puedeContinuar" class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Información adicional
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Si deseas agregar información adicional a tu
                            solicitud, puedes hacerlo en este espacio.
                        </p>

                    </div>


                    <div>

                        <div class="flex items-center justify-between gap-4">

                            <InputLabel for="observaciones" value="Observaciones" />


                            <!-- Contador -->
                            <span class="rounded-full px-3 py-1 text-xs font-medium" :class="form.observaciones.length >= 250
                                ? 'bg-red-100 text-red-700'
                                : 'bg-gray-100 text-gray-600'">
                                {{ form.observaciones.length }}/250
                            </span>

                        </div>


                        <textarea id="observaciones" v-model="form.observaciones" maxlength="250" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Escribe aquí cualquier información adicional que consideres importante."></textarea>


                        <div class="mt-1 flex justify-between gap-4">

                            <InputError :message="form.errors.observaciones" />

                            <span v-if="form.observaciones.length >= 230" class="text-xs text-gray-500">
                                Te quedan
                                {{ 250 - form.observaciones.length }}
                                caracteres.
                            </span>

                        </div>

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 8. TÉRMINOS Y PROTECCIÓN DE DATOS -->
                <!-- ===================================================== -->

                <section v-if="puedeContinuar" class="rounded-xl border border-gray-200 bg-white/35 p-6 shadow-sm">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Términos y protección de datos
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Revisa la siguiente información antes de enviar tu
                            solicitud.
                        </p>

                    </div>


                    <div class="space-y-5 bg-white/35">


                        <!-- ================================================= -->
                        <!-- TÉRMINOS -->
                        <!-- ================================================= -->
                       <label class="flex cursor-pointer items-start gap-3">
                            <input v-model="form.acepta_terminos" type="checkbox"
                                class="mt-1 h-5 w-5 rounded border-2 border-blue-600 text-indigo-600 focus:border-blue-600 focus:ring-2 focus:ring-blue-600" />

                            <span class="text-sm leading-6 text-gray-600">
                                Acepto los

                                <a href="/terminos" target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-indigo-600 underline hover:text-indigo-800">
                                    Términos y condiciones
                                </a>

                                de Portal Deseret.
                            </span>
                        </label>

                        <InputError :message="form.errors.acepta_terminos" />


                        <!-- ================================================= -->
                        <!-- PRIVACIDAD -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-start gap-3">
                            <input v-model="form.ha_leido_privacidad" type="checkbox"
                                class="mt-1 h-5 w-5 rounded border-2 border-blue-700 text-blue-700 focus:border-blue-700 focus:ring-2 focus:ring-blue-700" />

                            <span class="text-sm leading-6 text-gray-600">
                                He leído la

                                <a href="/privacidad" target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-indigo-600 underline hover:text-indigo-800">
                                    Política de privacidad
                                </a>

                                de Portal Deseret.
                            </span>
                        </label>

                        <InputError :message="form.errors.ha_leido_privacidad" />


                        <!-- ================================================= -->
                        <!-- TRATAMIENTO DE DATOS -->
                        <!-- ================================================= -->

                        <label class="flex cursor-pointer items-start gap-3">
                            <input v-model="form.autoriza_tratamiento_datos" type="checkbox"
                                class="mt-1 h-5 w-5 rounded border-2 border-blue-700 text-blue-700 focus:border-blue-700 focus:ring-2 focus:ring-blue-700" />

                            <span class="text-sm leading-6 text-gray-600">
                                Autorizo de manera previa, expresa e informada
                                el tratamiento de mis datos personales de
                                acuerdo con las finalidades y condiciones
                                informadas en la

                                <a href="/privacidad" target="_blank" rel="noopener noreferrer"
                                    class="font-semibold text-indigo-600 underline hover:text-indigo-800">
                                    Política de privacidad
                                </a>

                                de Portal Deseret.
                            </span>
                        </label>

                        <InputError :message="form.errors.autoriza_tratamiento_datos" />

                    </div>

                </section>



                <!-- ===================================================== -->
                <!-- 9. BOTONES -->
                <!-- ===================================================== -->

                <div v-if="puedeContinuar"
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <Link :href="route('login')"
                        class="text-center text-sm text-gray-600 underline hover:text-gray-900">
                        Ya tengo una cuenta
                    </Link>


                    <PrimaryButton type="submit" class="justify-center" :class="{
                        'opacity-25': form.processing
                    }" :disabled="form.processing">

                        {{
                            form.processing
                                ? 'Enviando solicitud...'
                                : 'Enviar solicitud'
                        }}

                    </PrimaryButton>

                </div>

            </form>

        </div>


    </GuestLayout>

</template>