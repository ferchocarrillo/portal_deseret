<script setup>

import { ref, onMounted } from 'vue'
import axios from 'axios'


// ==========================================================
// PROPS
// ==========================================================

const props = defineProps({

    pais: {
        type: [String, Number, null],
        default: null,
    },

    departamento: {
        type: [String, Number, null],
        default: null,
    },

    ciudad: {
        type: [String, Number, null],
        default: null,
    },

})


// ==========================================================
// EMITS
// ==========================================================

const emit = defineEmits([
    'update:pais',
    'update:departamento',
    'update:ciudad',
])


// ==========================================================
// DATOS
// ==========================================================

const paises = ref([])

const divisiones = ref([])

const ciudades = ref([])


// ==========================================================
// ESTADO INTERNO
// ==========================================================

// IMPORTANTE:
// Estos valores internos son IDs.
// El componente los utiliza para consultar las relaciones
// geográficas, pero hacia el formulario emitimos NOMBRES.

const paisSeleccionado = ref(
    props.pais
)

const divisionSeleccionada = ref(
    props.departamento
)

const ciudadSeleccionada = ref(
    props.ciudad
)


// ==========================================================
// LABELS
// ==========================================================

const divisionLabel = ref(
    'Departamento'
)

const ciudadLabel = ref(
    'Ciudad'
)


// ==========================================================
// CARGANDO
// ==========================================================

const cargandoPaises = ref(false)

const cargandoDivisiones = ref(false)

const cargandoCiudades = ref(false)


// ==========================================================
// BUSCAR ELEMENTOS
// ==========================================================

// Permite trabajar tanto con ID como con nombre.
// Esto hace compatible el componente con los datos antiguos.

function encontrarPorIdONombre(lista, valor) {

    if (
        valor === null ||
        valor === undefined ||
        valor === ''
    ) {
        return null
    }

    return lista.find(item =>

        String(item.id) === String(valor)

        ||

        String(item.nombre).toLowerCase() ===
        String(valor).toLowerCase()

    ) ?? null
}


// ==========================================================
// CARGAR PAÍSES
// ==========================================================

async function cargarPaises() {

    cargandoPaises.value = true

    try {

        const response = await axios.get(
            '/ubicaciones/paises'
        )

        paises.value = response.data


        // ======================================================
        // SI YA EXISTE UN PAÍS
        // ======================================================

        if (props.pais) {

            const pais = encontrarPorIdONombre(
                paises.value,
                props.pais
            )

            if (pais) {

                // Internamente usamos ID
                paisSeleccionado.value = pais.id

                actualizarLabels(pais)

                await cargarDivisiones(
                    pais.id
                )

                // Buscar departamento existente
                if (props.departamento) {

                    const departamento =
                        encontrarPorIdONombre(
                            divisiones.value,
                            props.departamento
                        )

                    if (departamento) {

                        divisionSeleccionada.value =
                            departamento.id

                        await cargarCiudades(
                            departamento.id
                        )

                        // Buscar ciudad existente
                        if (props.ciudad) {

                            const ciudad =
                                encontrarPorIdONombre(
                                    ciudades.value,
                                    props.ciudad
                                )

                            if (ciudad) {

                                ciudadSeleccionada.value =
                                    ciudad.id

                            }

                        }

                    }

                }

            }

            return
        }


        // ======================================================
        // COLOMBIA POR DEFECTO
        // ======================================================

        const colombia = paises.value.find(
            item =>
                item.codigo_iso2 === 'CO'
        )


        if (colombia) {

            paisSeleccionado.value =
                colombia.id

            // IMPORTANTE:
            // El formulario recibe el NOMBRE,
            // no el ID.

            emit(
                'update:pais',
                colombia.nombre
            )

            actualizarLabels(
                colombia
            )

            await cargarDivisiones(
                colombia.id
            )

        }

    } catch (error) {

        console.error(
            'Error cargando países:',
            error
        )

    } finally {

        cargandoPaises.value = false

    }

}


// ==========================================================
// ACTUALIZAR LABELS
// ==========================================================

function actualizarLabels(pais) {

    divisionLabel.value =
        pais?.division_label ||
        'Departamento'

    ciudadLabel.value =
        pais?.localidad_label ||
        'Ciudad'

}


// ==========================================================
// CAMBIAR PAÍS
// ==========================================================

async function cambiarPais() {

    const pais = paises.value.find(
        item =>
            String(item.id) ===
            String(paisSeleccionado.value)
    )


    // ======================================================
    // LIMPIAR DEPENDENCIAS
    // ======================================================

    divisionSeleccionada.value = null

    ciudadSeleccionada.value = null

    divisiones.value = []

    ciudades.value = []


    // ======================================================
    // ENVIAR NOMBRE DEL PAÍS
    // ======================================================

    emit(
        'update:pais',
        pais
            ? pais.nombre
            : null
    )

    emit(
        'update:departamento',
        null
    )

    emit(
        'update:ciudad',
        null
    )


    if (!pais) {

        divisionLabel.value =
            'Departamento'

        ciudadLabel.value =
            'Ciudad'

        return

    }


    actualizarLabels(
        pais
    )


    await cargarDivisiones(
        pais.id
    )

}


// ==========================================================
// CARGAR DIVISIONES
// ==========================================================

async function cargarDivisiones(
    paisId
) {

    cargandoDivisiones.value = true

    try {

        const response = await axios.get(
            `/ubicaciones/paises/${paisId}/divisiones`
        )


        /*
         * Nuestro controlador devuelve:
         *
         * {
         *     pais: {...},
         *     divisiones: [...]
         * }
         */

        divisiones.value =
            response.data.divisiones || []


        // ==================================================
        // ACTUALIZAR LABEL DESDE EL PAÍS
        // ==================================================

        if (response.data.pais) {

            actualizarLabels(
                response.data.pais
            )

        }

    } catch (error) {

        console.error(
            'Error cargando divisiones:',
            error
        )

        divisiones.value = []

    } finally {

        cargandoDivisiones.value = false

    }

}


// ==========================================================
// CAMBIAR DIVISIÓN
// ==========================================================

async function cambiarDivision() {

    ciudadSeleccionada.value = null

    ciudades.value = []


    const division =
        divisiones.value.find(
            item =>
                String(item.id) ===
                String(divisionSeleccionada.value)
        )


    // ======================================================
    // ENVIAR NOMBRE DEL DEPARTAMENTO
    // ======================================================

    emit(
        'update:departamento',
        division
            ? division.nombre
            : null
    )

    emit(
        'update:ciudad',
        null
    )


    if (!divisionSeleccionada.value) {

        return

    }


    await cargarCiudades(
        divisionSeleccionada.value
    )

}


// ==========================================================
// CARGAR CIUDADES
// ==========================================================

async function cargarCiudades(
    divisionId
) {

    cargandoCiudades.value = true

    try {

        const response = await axios.get(
            `/ubicaciones/divisiones/${divisionId}/ciudades`
        )


        /*
         * Nuestro controlador devuelve:
         *
         * {
         *     division: {...},
         *     ciudades: [...]
         * }
         */

        ciudades.value =
            response.data.ciudades || []

    } catch (error) {

        console.error(
            'Error cargando ciudades:',
            error
        )

        ciudades.value = []

    } finally {

        cargandoCiudades.value = false

    }

}


// ==========================================================
// CAMBIAR CIUDAD
// ==========================================================

function cambiarCiudad() {

    const ciudad =
        ciudades.value.find(
            item =>
                String(item.id) ===
                String(ciudadSeleccionada.value)
        )


    // ======================================================
    // ENVIAR NOMBRE DE LA CIUDAD
    // ======================================================

    emit(
        'update:ciudad',
        ciudad
            ? ciudad.nombre
            : null
    )

}


// ==========================================================
// INICIALIZAR
// ==========================================================

onMounted(() => {

    cargarPaises()

})

</script>


<template>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">


        <!-- ==================================================
             PAÍS
        =================================================== -->

        <div>

            <label class="mb-2 block text-sm font-semibold text-[#526174]">
                País
            </label>

            <select v-model="paisSeleccionado" @change="cambiarPais" :disabled="cargandoPaises"
                class="w-full rounded-xl border border-[#D9E0E8] bg-white px-4 py-3 text-sm text-[#26364A] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-[#D99A24]/20 disabled:cursor-not-allowed disabled:bg-[#F5F7FA]">

                <option :value="null" disabled>
                    {{
                        cargandoPaises
                            ? 'Cargando países...'
                            : 'Seleccione un país'
                    }}
                </option>

                <option v-for="pais in paises" :key="pais.id" :value="pais.id">
                    {{ pais.nombre }}
                </option>

            </select>

        </div>


        <!-- ==================================================
             DIVISIÓN ADMINISTRATIVA
        =================================================== -->

        <div>

            <label class="mb-2 block text-sm font-semibold text-[#526174]">
                {{ divisionLabel }}
            </label>

            <select v-model="divisionSeleccionada" @change="cambiarDivision" :disabled="cargandoDivisiones ||
                !paisSeleccionado
                "
                class="w-full rounded-xl border border-[#D9E0E8] bg-white px-4 py-3 text-sm text-[#26364A] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-[#D99A24]/20 disabled:cursor-not-allowed disabled:bg-[#F5F7FA]">

                <option :value="null">

                    {{
                        cargandoDivisiones
                            ? 'Cargando...'
                            : `Seleccione ${divisionLabel.toLowerCase()}`
                    }}

                </option>

                <option v-for="division in divisiones" :key="division.id" :value="division.id">
                    {{ division.nombre }}
                </option>

            </select>

        </div>


        <!-- ==================================================
             CIUDAD
        =================================================== -->

        <div>

            <label class="mb-2 block text-sm font-semibold text-[#526174]">
                {{ ciudadLabel }}
            </label>

            <select v-model="ciudadSeleccionada" @change="cambiarCiudad" :disabled="cargandoCiudades ||
                !divisionSeleccionada
                "
                class="w-full rounded-xl border border-[#D9E0E8] bg-white px-4 py-3 text-sm text-[#26364A] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-[#D99A24]/20 disabled:cursor-not-allowed disabled:bg-[#F5F7FA]">

                <option :value="null">

                    {{
                        cargandoCiudades
                            ? 'Cargando...'
                            : `Seleccione ${ciudadLabel.toLowerCase()}`
                    }}

                </option>

                <option v-for="ciudad in ciudades" :key="ciudad.id" :value="ciudad.id">
                    {{ ciudad.nombre }}
                </option>

            </select>

        </div>

    </div>

</template>