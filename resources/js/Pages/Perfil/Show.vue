<script setup>
import { computed, ref, reactive, onMounted, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import axios from 'axios';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UbicacionSelector from '@/Components/UbicacionSelector.vue';
import MapaSelector from '@/Components/MapaSelector.vue';

const page = usePage();

const usuario = computed(() => page.props.auth?.user ?? {});

const formatearFechaInput = (fecha) => {
    if (!fecha) return '';
    const valor = String(fecha).substring(0, 10);
    const partes = valor.split('-');
    if (partes.length !== 3) return '';
    const [anio, mes, dia] = partes;
    return `${dia}/${mes}/${anio}`;
};

const fechaParaBackend = (fecha) => {
    if (!fecha) return null;
    const valor = String(fecha).trim();
    const partes = valor.split('/');
    if (partes.length !== 3) return valor;
    const [dia, mes, anio] = partes;
    return `${anio}-${mes}-${dia}`;
};

const manejarFecha = (formulario, campo, event) => {
    let valor = event.target.value.replace(/\D/g, '');
    if (valor.length > 8) valor = valor.substring(0, 8);
    if (valor.length >= 5) {
        valor = `${valor.substring(0, 2)}/${valor.substring(2, 4)}/${valor.substring(4)}`;
    } else if (valor.length >= 3) {
        valor = `${valor.substring(0, 2)}/${valor.substring(2)}`;
    }
    formulario[campo] = valor;
};

/*
|--------------------------------------------------------------------------
| OBLIGATORIEDAD INICIAL (MODAL BLOQUEANTE)
|--------------------------------------------------------------------------
*/
const modalObligatorioAbierto = ref(false);
const inputFotoObligatoria = ref(null);

const formObligatorio = useForm({
    telefono: usuario.value?.telefono ?? '',
    fecha_nacimiento: formatearFechaInput(usuario.value?.fecha_nacimiento),
    foto: null,
});

const previewFotoObligatoria = ref(null);

const perfilIncompleto = computed(() => {
    const u = usuario.value;
    const faltaTelefono = !u?.telefono || String(u.telefono).trim() === '';
    const faltaFecha = !u?.fecha_nacimiento;
    const faltaFoto = !u?.foto;
    return faltaTelefono || faltaFecha || faltaFoto;
});

onMounted(() => {
    if (perfilIncompleto.value) {
        modalObligatorioAbierto.value = true;
    }
});

const seleccionarFotoObligatoria = () => {
    inputFotoObligatoria.value?.click();
};

const cambiarFotoObligatoria = (event) => {
    const archivo = event.target.files?.[0];
    if (!archivo) return;
    formObligatorio.foto = archivo;
    previewFotoObligatoria.value = URL.createObjectURL(archivo);
};

const guardarDatosObligatorios = () => {
    // 1. Validaciones manuales
    if (!formObligatorio.telefono || String(formObligatorio.telefono).trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Teléfono requerido',
            text: 'Debes ingresar tu número de teléfono.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    if (!formObligatorio.fecha_nacimiento || formObligatorio.fecha_nacimiento.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Fecha de nacimiento requerida',
            text: 'Ingresa una fecha de nacimiento válida (dd/mm/aaaa).',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    if (!usuario.value?.foto && !formObligatorio.foto) {
        Swal.fire({
            icon: 'warning',
            title: 'Fotografía requerida',
            text: 'Debes seleccionar una foto de perfil.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    formObligatorio.processing = true;

    // 2. Usamos router.put de Inertia con preserveState para mantener el modal abierto
    router.put(route('perfil.update'), {
        name: usuario.value?.name,
        email: (usuario.value?.email ?? '').toLowerCase(),
        telefono: formObligatorio.telefono,
        fecha_nacimiento: fechaParaBackend(formObligatorio.fecha_nacimiento),
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            // 3. Si se guardó la info de texto, subimos la foto (si hay una nueva)
            if (formObligatorio.foto) {
                router.post(route('perfil.foto.update'), {
                    foto: formObligatorio.foto,
                }, {
                    forceFormData: true,
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        formObligatorio.processing = false;
                        modalObligatorioAbierto.value = false;
                        Swal.fire({
                            icon: 'success',
                            title: '¡Perfil completado!',
                            text: 'Tus datos se han registrado correctamente.',
                            confirmButtonColor: '#172A3D',
                        });
                    },
                    onError: (errors) => {
                        formObligatorio.processing = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error con la foto',
                            text: Object.values(errors)[0] ?? 'No se pudo subir la fotografía.',
                            confirmButtonColor: '#172A3D',
                        });
                    }
                });
            } else {
                formObligatorio.processing = false;
                modalObligatorioAbierto.value = false;
                Swal.fire({
                    icon: 'success',
                    title: '¡Perfil completado!',
                    text: 'Tus datos obligatorios se han registrado correctamente.',
                    confirmButtonColor: '#172A3D',
                });
            }
        },
        onError: (errors) => {
            formObligatorio.processing = false;
            const mensaje = Object.values(errors)[0] || 'Ocurrió un error al guardar los datos.';
            Swal.fire({
                icon: 'error',
                title: 'Revisa la información',
                text: mensaje,
                confirmButtonColor: '#172A3D',
            });
        }
    });
};

/*
|--------------------------------------------------------------------------
| INFORMACIÓN PERSONAL (Actualización general)
|--------------------------------------------------------------------------
*/
const formularioPerfil = useForm({
    name: usuario.value?.name ?? '',
    email: usuario.value?.email ?? '',
    telefono: usuario.value?.telefono ?? '',
    fecha_nacimiento: formatearFechaInput(usuario.value?.fecha_nacimiento),
});

const guardarPerfil = () => {
    formularioPerfil.transform((data) => ({
        ...data,
        email: (data.email ?? '').toLowerCase(), // Previene el error 'lowercase' en el validador
        fecha_nacimiento: fechaParaBackend(data.fecha_nacimiento),
    })).put(route('perfil.update'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Perfil actualizado',
                text: 'La información de tu perfil fue actualizada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudieron guardar los cambios',
                text: primerError ?? 'Ocurrió un error al actualizar tu información.',
                confirmButtonColor: '#172A3D',
            });
        },
    });
};

/*
|--------------------------------------------------------------------------
| FOTOGRAFÍA
|--------------------------------------------------------------------------
*/
const formularioFoto = useForm({ foto: null });
const inputFoto = ref(null);

const seleccionarFoto = () => inputFoto.value?.click();

const cambiarFoto = (event) => {
    const archivo = event.target.files[0];
    if (!archivo) return;
    formularioFoto.foto = archivo;
};

const guardarFoto = () => {
    formularioFoto.post(route('perfil.foto.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            formularioFoto.reset('foto');
            if (inputFoto.value) inputFoto.value.value = '';
            Swal.fire({
                icon: 'success',
                title: 'Fotografía actualizada',
                text: 'Tu fotografía de perfil fue actualizada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudo actualizar la fotografía',
                text: primerError ?? 'Ocurrió un error al subir la fotografía.',
                confirmButtonColor: '#172A3D',
            });
        },
    });
};

/*
|--------------------------------------------------------------------------
| CONTRASEÑA
|--------------------------------------------------------------------------
*/
const formularioPassword = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const cambiarPassword = () => {
    formularioPassword.put(route('perfil.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            formularioPassword.reset();
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: 'Tu contraseña fue cambiada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudo cambiar la contraseña',
                text: primerError ?? 'Ocurrió un error al actualizar la contraseña.',
                confirmButtonColor: '#172A3D',
            });
        },
    });
};

const tieneFoto = computed(() => !!usuario.value?.foto);

const urlFoto = computed(() => {
    if (!usuario.value?.foto) return null;
    if (
        usuario.value.foto.startsWith('http://') ||
        usuario.value.foto.startsWith('https://') ||
        usuario.value.foto.startsWith('/')
    ) {
        return usuario.value.foto;
    }
    return `/storage/${usuario.value.foto}`;
});

const inicial = computed(() => usuario.value?.name?.charAt(0)?.toUpperCase() ?? '?');

const formatoFecha = (fecha) => {
    if (!fecha) return 'Nunca';
    return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatoFechaHora = (fecha) => {
    if (!fecha) return 'Nunca';
    return new Date(fecha).toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const nombreRol = computed(() => {
    if (usuario.value?.roles?.length) return usuario.value.roles[0].name;
    return usuario.value?.role ?? 'Usuario';
});

const debeCambiarPassword = computed(() => usuario.value?.debe_cambiar_password === true);

const diasDesdeUltimaVisita = computed(() => {
    if (!usuario.value?.ultima_visita) return null;
    const ultimaVisita = new Date(usuario.value.ultima_visita);
    const ahora = new Date();
    const diferencia = ahora.getTime() - ultimaVisita.getTime();
    return Math.max(0, Math.floor(diferencia / (1000 * 60 * 60 * 24)));
});

const formaciones = computed(() => page.props.formaciones ?? []);
const experiencias = computed(() => page.props.experiencias ?? []);
const skills = computed(() => page.props.skills ?? []);

/*
|--------------------------------------------------------------------------
| SKILLS
|--------------------------------------------------------------------------
*/
const modalSkillsAbierto = ref(false);
const cargandoBancoSkills = ref(false);
const bancoSkills = ref([]);
const categoriaSeleccionadaId = ref('');
const subcategoriaSeleccionadaId = ref('');
const busquedaSkill = ref('');

const formularioSkills = useForm({ skills: [] });
const nivelesSkill = ['Básico', 'Intermedio', 'Avanzado', 'Experto'];
const maxSkills = 25;

const dropdownCategoriaSkillAbierto = ref(false);
const dropdownSubcategoriaSkillAbierto = ref(false);
const dropdownNivelSkillAbierto = ref(null);

const skillsUsuario = computed(() => {
    return skills.value.map((skill) => ({
        id: skill.id,
        name: skill.name ?? skill.nombre ?? '',
        nivel: skill.nivel ?? skill.pivot?.nivel ?? 'Intermedio',
    }));
});

const cantidadSkills = computed(() => formularioSkills.skills.length);

const categoriaSeleccionada = computed(() => {
    return bancoSkills.value.find(
        (categoria) => Number(categoria.id) === Number(categoriaSeleccionadaId.value)
    ) ?? null;
});

const subcategoriasDisponibles = computed(() => categoriaSeleccionada.value?.subcategories ?? []);

const subcategoriaSeleccionada = computed(() => {
    return subcategoriasDisponibles.value.find(
        (subcategoria) => Number(subcategoria.id) === Number(subcategoriaSeleccionadaId.value)
    ) ?? null;
});

const skillsDisponibles = computed(() => {
    const lista = subcategoriaSeleccionada.value?.skills ?? [];
    const termino = busquedaSkill.value.trim().toLowerCase();
    if (!termino) return lista;
    return lista.filter((skill) => {
        const nombre = String(skill.name ?? '').toLowerCase();
        const descripcion = String(skill.description ?? '').toLowerCase();
        return nombre.includes(termino) || descripcion.includes(termino);
    });
});

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const cargarBancoSkills = async () => {
    cargandoBancoSkills.value = true;
    try {
        const respuesta = await fetch(route('perfil.skills.index'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!respuesta.ok) throw new Error('No se pudo cargar el banco de skills.');
        const datos = await respuesta.json();
        bancoSkills.value = datos.categories ?? [];
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'No se pudo cargar el banco',
            text: 'Ocurrió un error al cargar las categorías y skills disponibles.',
            confirmButtonColor: '#172A3D',
        });
    } finally {
        cargandoBancoSkills.value = false;
    }
};

const abrirModalSkills = async () => {
    busquedaSkill.value = '';
    categoriaSeleccionadaId.value = '';
    subcategoriaSeleccionadaId.value = '';
    formularioSkills.clearErrors();
    formularioSkills.skills = skillsUsuario.value.map((skill) => ({
        skill_id: skill.id,
        nivel: skill.nivel,
    }));
    modalSkillsAbierto.value = true;
    if (!bancoSkills.value.length) await cargarBancoSkills();
};

const cerrarModalSkills = () => {
    if (formularioSkills.processing) return;
    dropdownCategoriaSkillAbierto.value = false;
    dropdownSubcategoriaSkillAbierto.value = false;
    dropdownNivelSkillAbierto.value = null;
    modalSkillsAbierto.value = false;
    busquedaSkill.value = '';
    categoriaSeleccionadaId.value = '';
    subcategoriaSeleccionadaId.value = '';
    formularioSkills.clearErrors();
};

const cambiarCategoriaSkills = () => {
    subcategoriaSeleccionadaId.value = '';
    busquedaSkill.value = '';
};

const seleccionarCategoriaModal = (catId) => {
    categoriaSeleccionadaId.value = catId;
    dropdownCategoriaSkillAbierto.value = false;
    cambiarCategoriaSkills();
};

const seleccionarSubcategoriaModal = (subId) => {
    subcategoriaSeleccionadaId.value = subId;
    dropdownSubcategoriaSkillAbierto.value = false;
    busquedaSkill.value = '';
};

const toggleDropdownNivel = (skillId) => {
    dropdownNivelSkillAbierto.value = dropdownNivelSkillAbierto.value === skillId ? null : skillId;
};

const seleccionarNivelParaSkill = (skillId, nivel) => {
    cambiarNivelSkill(skillId, nivel);
    dropdownNivelSkillAbierto.value = null;
};

const skillSeleccionada = (skillId) => {
    return formularioSkills.skills.some((skill) => Number(skill.skill_id) === Number(skillId));
};

const obtenerNivelSkill = (skillId) => {
    const seleccion = formularioSkills.skills.find((skill) => Number(skill.skill_id) === Number(skillId));
    return seleccion?.nivel ?? 'Intermedio';
};

const cambiarNivelSkill = (skillId, nivel) => {
    const seleccion = formularioSkills.skills.find((skill) => Number(skill.skill_id) === Number(skillId));
    if (seleccion) seleccion.nivel = nivel;
};

const alternarSkill = (skill) => {
    const index = formularioSkills.skills.findIndex((item) => Number(item.skill_id) === Number(skill.id));
    if (index !== -1) {
        formularioSkills.skills.splice(index, 1);
        return;
    }
    if (formularioSkills.skills.length >= maxSkills) {
        Swal.fire({
            icon: 'info',
            title: 'Límite alcanzado',
            text: `Puedes seleccionar máximo ${maxSkills} skills.`,
            confirmButtonColor: '#172A3D',
        });
        return;
    }
    formularioSkills.skills.push({
        skill_id: skill.id,
        nivel: 'Intermedio',
    });
};

const guardarSkills = async () => {
    if (cantidadSkills.value === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Selecciona al menos una skill',
            text: 'Agrega las habilidades que deseas mostrar en tu perfil.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }
    formularioSkills.processing = true;
    try {
        const respuesta = await fetch(route('perfil.skills.store'), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
            body: JSON.stringify({ skills: formularioSkills.skills }),
        });
        const datos = await respuesta.json().catch(() => ({}));
        if (!respuesta.ok) {
            const primerError = Object.values(datos.errors ?? {})[0];
            throw new Error(
                Array.isArray(primerError)
                    ? primerError[0]
                    : (primerError ?? datos.message ?? 'No se pudieron guardar las skills.')
            );
        }
        cerrarModalSkills();
        await router.reload({ preserveScroll: true, only: ['auth', 'skills'] });
        Swal.fire({
            icon: 'success',
            title: 'Skills actualizadas',
            text: datos.message ?? 'Tus skills fueron actualizadas correctamente.',
            confirmButtonColor: '#172A3D',
        });
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'No se pudieron guardar las skills',
            text: error.message ?? 'Ocurrió un error al guardar tus skills.',
            confirmButtonColor: '#172A3D',
        });
    } finally {
        formularioSkills.processing = false;
    }
};

const eliminarSkill = (skill) => {
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar skill?',
        text: `Se eliminará "${skill.name}" de tu perfil.`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#C24141',
        cancelButtonColor: '#172A3D',
    }).then(async (resultado) => {
        if (!resultado.isConfirmed) return;
        try {
            const respuesta = await fetch(route('perfil.skills.destroy', skill.id), {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                credentials: 'same-origin',
            });
            const datos = await respuesta.json().catch(() => ({}));
            if (!respuesta.ok) throw new Error(datos.message ?? 'No se pudo eliminar la skill.');
            await router.reload({ preserveScroll: true, only: ['auth', 'skills'] });
            Swal.fire({
                icon: 'success',
                title: 'Skill eliminada',
                text: datos.message ?? 'La skill fue eliminada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'No se pudo eliminar',
                text: error.message ?? 'Ocurrió un error al eliminar la skill.',
                confirmButtonColor: '#172A3D',
            });
        }
    });
};

/*
|--------------------------------------------------------------------------
| HOJA DE VIDA
|--------------------------------------------------------------------------
*/
const hojaVida = computed(() => page.props.hoja_vida ?? null);
const inputHojaVida = ref(null);
const formularioHojaVida = useForm({ archivo: null });

const seleccionarHojaVida = () => inputHojaVida.value?.click();

const cambiarHojaVida = (event) => {
    const archivo = event.target.files?.[0];
    if (!archivo) return;
    if (archivo.type !== 'application/pdf') {
        formularioHojaVida.reset('archivo');
        if (inputHojaVida.value) inputHojaVida.value.value = '';
        Swal.fire({
            icon: 'warning',
            title: 'Archivo no válido',
            text: 'La hoja de vida debe estar en formato PDF.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }
    if (archivo.size > 10 * 1024 * 1024) {
        formularioHojaVida.reset('archivo');
        if (inputHojaVida.value) inputHojaVida.value.value = '';
        Swal.fire({
            icon: 'warning',
            title: 'Archivo demasiado grande',
            text: 'La hoja de vida no puede superar los 10 MB.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }
    formularioHojaVida.archivo = archivo;
};

const guardarHojaVida = () => {
    if (!formularioHojaVida.archivo) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un archivo',
            text: 'Debes seleccionar tu hoja de vida en formato PDF.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }
    formularioHojaVida.post(route('perfil.hoja-vida.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            formularioHojaVida.reset();
            if (inputHojaVida.value) inputHojaVida.value.value = '';
            Swal.fire({
                icon: 'success',
                title: 'Hoja de vida cargada',
                text: 'Tu hoja de vida fue guardada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudo cargar la hoja de vida',
                text: primerError ?? 'Ocurrió un error al cargar el documento.',
                confirmButtonColor: '#172A3D',
            });
        },
    });
};

const eliminarHojaVida = () => {
    if (!hojaVida.value?.id) return;
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar hoja de vida?',
        text: 'El documento será eliminado de tu perfil.',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#C24141',
        cancelButtonColor: '#172A3D',
    }).then((resultado) => {
        if (!resultado.isConfirmed) return;
        formularioHojaVida.delete(route('perfil.hoja-vida.destroy', hojaVida.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                formularioHojaVida.reset();
                if (inputHojaVida.value) inputHojaVida.value.value = '';
            },
            onError: (errors) => {
                const primerError = Object.values(errors)[0];
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo eliminar',
                    text: primerError ?? 'Ocurrió un error al eliminar la hoja de vida.',
                    confirmButtonColor: '#172A3D',
                });
            },
        });
    });
};

const urlHojaVida = (id) => route('perfil.hoja-vida.show', id);
const urlDescargaHojaVida = (id) => route('perfil.hoja-vida.download', id);

const formatoTamanioArchivo = (bytes) => {
    if (!bytes) return '0 KB';
    const mb = bytes / (1024 * 1024);
    if (mb >= 1) return `${mb.toFixed(2)} MB`;
    return `${Math.max(1, Math.round(bytes / 1024))} KB`;
};

/*
|--------------------------------------------------------------------------
| FORMACIÓN ACADÉMICA
|--------------------------------------------------------------------------
*/
const modalFormacionAbierto = ref(false);
const editandoFormacion = ref(false);
const formacionSeleccionada = ref(null);
const dropdownNivelAbierto = ref(false);

const nivelesAcademicos = [
    'Bachillerato',
    'Técnico',
    'Tecnólogo',
    'Profesional',
    'Especialización',
    'Maestría',
    'Doctorado',
    'Otro',
];

const formularioFormacion = useForm({
    nivel: '',
    institucion: '',
    programa: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    en_curso: false,
});

const seleccionarNivel = (nivel) => {
    formularioFormacion.nivel = nivel;
    dropdownNivelAbierto.value = false;
};

const abrirModalFormacion = () => {
    editandoFormacion.value = false;
    formacionSeleccionada.value = null;
    dropdownNivelAbierto.value = false;
    formularioFormacion.reset();
    formularioFormacion.clearErrors();
    modalFormacionAbierto.value = true;
};

const cerrarModalFormacion = () => {
    if (formularioFormacion.processing) return;
    dropdownNivelAbierto.value = false;
    modalFormacionAbierto.value = false;
    editandoFormacion.value = false;
    formacionSeleccionada.value = null;
    formularioFormacion.reset();
    formularioFormacion.clearErrors();
};

const editarFormacion = (formacion) => {
    editandoFormacion.value = true;
    formacionSeleccionada.value = formacion;
    dropdownNivelAbierto.value = false;
    formularioFormacion.clearErrors();
    formularioFormacion.nivel = formacion.nivel ?? '';
    formularioFormacion.institucion = formacion.institucion ?? '';
    formularioFormacion.programa = formacion.programa ?? '';
    formularioFormacion.descripcion = formacion.descripcion ?? '';
    formularioFormacion.fecha_inicio = formacion.fecha_inicio ? String(formacion.fecha_inicio).substring(0, 10) : '';
    formularioFormacion.fecha_fin = formacion.fecha_fin ? String(formacion.fecha_fin).substring(0, 10) : '';
    formularioFormacion.en_curso = Boolean(formacion.en_curso);
    modalFormacionAbierto.value = true;
};

const guardarFormacion = () => {
    const estabaEditando = editandoFormacion.value;
    const opciones = {
        preserveScroll: true,
        onSuccess: () => {
            cerrarModalFormacion();
            Swal.fire({
                icon: 'success',
                title: estabaEditando ? 'Formación actualizada' : 'Formación agregada',
                text: estabaEditando
                    ? 'La formación académica fue actualizada correctamente.'
                    : 'La formación académica fue agregada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudo guardar la formación',
                text: primerError ?? 'Revisa la información ingresada.',
                confirmButtonColor: '#172A3D',
            });
        },
    };

    if (editandoFormacion.value && formacionSeleccionada.value?.id) {
        formularioFormacion.put(route('perfil.formacion.update', formacionSeleccionada.value.id), opciones);
        return;
    }
    formularioFormacion.post(route('perfil.formacion.store'), opciones);
};

const eliminarFormacion = (formacion) => {
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar formación?',
        text: 'Esta formación académica será eliminada de tu perfil.',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#C24141',
        cancelButtonColor: '#172A3D',
    }).then((resultado) => {
        if (!resultado.isConfirmed) return;
        formularioFormacion.delete(route('perfil.formacion.destroy', formacion.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Formación eliminada',
                    text: 'La formación académica fue eliminada correctamente.',
                    confirmButtonColor: '#172A3D',
                });
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo eliminar',
                    text: 'Ocurrió un error al eliminar la formación académica.',
                    confirmButtonColor: '#172A3D',
                });
            },
        });
    });
};

/*
|--------------------------------------------------------------------------
| EXPERIENCIA LABORAL
|--------------------------------------------------------------------------
*/
const modalExperienciaAbierto = ref(false);
const editandoExperiencia = ref(false);
const experienciaSeleccionada = ref(null);

const formularioExperiencia = useForm({
    empresa: '',
    cargo: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    actualmente_trabaja: false,
    sin_experiencia: false,
});

const abrirModalExperiencia = () => {
    editandoExperiencia.value = false;
    experienciaSeleccionada.value = null;
    formularioExperiencia.reset();
    formularioExperiencia.clearErrors();
    modalExperienciaAbierto.value = true;
};

const cerrarModalExperiencia = () => {
    if (formularioExperiencia.processing) return;
    modalExperienciaAbierto.value = false;
    editandoExperiencia.value = false;
    experienciaSeleccionada.value = null;
    formularioExperiencia.reset();
    formularioExperiencia.clearErrors();
};

const editarExperiencia = (experiencia) => {
    editandoExperiencia.value = true;
    experienciaSeleccionada.value = experiencia;
    formularioExperiencia.clearErrors();
    formularioExperiencia.empresa = experiencia.empresa ?? '';
    formularioExperiencia.cargo = experiencia.cargo ?? '';
    formularioExperiencia.descripcion = experiencia.descripcion ?? '';
    formularioExperiencia.fecha_inicio = experiencia.fecha_inicio ? String(experiencia.fecha_inicio).substring(0, 10) : '';
    formularioExperiencia.fecha_fin = experiencia.fecha_fin ? String(experiencia.fecha_fin).substring(0, 10) : '';
    formularioExperiencia.actualmente_trabaja = Boolean(experiencia.actualmente_trabaja);
    formularioExperiencia.sin_experiencia = Boolean(experiencia.sin_experiencia);
    modalExperienciaAbierto.value = true;
};

const guardarExperiencia = () => {
    const estabaEditando = editandoExperiencia.value;
    const opciones = {
        preserveScroll: true,
        onSuccess: () => {
            cerrarModalExperiencia();
            Swal.fire({
                icon: 'success',
                title: estabaEditando ? 'Experiencia actualizada' : 'Experiencia agregada',
                text: estabaEditando
                    ? 'La experiencia laboral fue actualizada correctamente.'
                    : 'La experiencia laboral fue agregada correctamente.',
                confirmButtonColor: '#172A3D',
            });
        },
        onError: (errors) => {
            const primerError = Object.values(errors)[0];
            Swal.fire({
                icon: 'error',
                title: 'No se pudo guardar la experiencia',
                text: primerError ?? 'Revisa la información ingresada.',
                confirmButtonColor: '#172A3D',
            });
        },
    };

    if (editandoExperiencia.value && experienciaSeleccionada.value?.id) {
        formularioExperiencia.put(route('perfil.experiencia.update', experienciaSeleccionada.value.id), opciones);
        return;
    }
    formularioExperiencia.post(route('perfil.experiencia.store'), opciones);
};

const eliminarExperiencia = (experiencia) => {
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar experiencia?',
        text: 'Esta experiencia laboral será eliminada de tu perfil.',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#C24141',
        cancelButtonColor: '#172A3D',
    }).then((resultado) => {
        if (!resultado.isConfirmed) return;
        formularioExperiencia.delete(route('perfil.experiencia.destroy', experiencia.id), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Experiencia eliminada',
                    text: 'La experiencia laboral fue eliminada correctamente.',
                    confirmButtonColor: '#172A3D',
                });
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo eliminar',
                    text: 'Ocurrió un error al eliminar la experiencia laboral.',
                    confirmButtonColor: '#172A3D',
                });
            },
        });
    });
};

const formatoPeriodoExperiencia = (experiencia) => {
    const inicio = experiencia.fecha_inicio
        ? new Date(experiencia.fecha_inicio).toLocaleDateString('es-CO', { year: 'numeric', month: 'short' })
        : 'Fecha no indicada';

    if (experiencia.actualmente_trabaja) return `${inicio} — Actualmente`;

    const fin = experiencia.fecha_fin
        ? new Date(experiencia.fecha_fin).toLocaleDateString('es-CO', { year: 'numeric', month: 'short' })
        : 'Actualmente';

    return `${inicio} — ${fin}`;
};

const cerrarModalConEscape = (event) => {
    if (event.key !== 'Escape') return;
    if (modalObligatorioAbierto.value) return;
    dropdownNivelAbierto.value = false;
    dropdownTipoViaAbierto.value = false;
    dropdownSectorAbierto.value = false;
    if (modalFormacionAbierto.value) { cerrarModalFormacion(); return; }
    if (modalExperienciaAbierto.value) { cerrarModalExperiencia(); return; }
    if (modalSkillsAbierto.value) { cerrarModalSkills(); return; }
    if (mostrarModalRecepcion.value) { cerrarModalRecepcion(); }
};

// ==========================================================
// INFORMACIÓN DE RESIDENCIA Y GEORREFERENCIACIÓN
// ==========================================================
const informacionRecepcion = ref(page.props.informacion_recepcion ?? null);
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

// Sincronizar número de vía en tiempo real
watch(direccionCompletaGenerada, (nuevaDireccion) => {
    if (!cargandoModal.value && nuevaDireccion) {
        formRecepcion.numero_via = nuevaDireccion;
        formRecepcion.tipo_via = direccionEstandar.tipo;
    }
});

// Resetear coordenadas al cambiar componentes de dirección
watch(
    () => [formRecepcion.ciudad, formRecepcion.departamento, direccionCompletaGenerada.value],
    () => {
        if (!cargandoModal.value && mostrarModalRecepcion.value) {
            formRecepcion.latitud = null;
            formRecepcion.longitud = null;
        }
    }
);

// Desglose de dirección existente
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
    setTimeout(() => {
        cargandoModal.value = false;
    }, 150);
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
            if (error.code === error.PERMISSION_DENIED) {
                msg = 'Permiso denegado por el navegador para acceder al GPS.';
            }
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
    // 1. Validar dirección física
    if (direccionCompletaGenerada.value) {
        formRecepcion.numero_via = direccionCompletaGenerada.value;
        formRecepcion.tipo_via = direccionEstandar.tipo;
    }

    if (!formRecepcion.numero_via || formRecepcion.numero_via.trim() === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Dirección requerida',
            text: 'Debes completar los datos de tu dirección física.',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    // 2. Validar ubicación exacta en el mapa (Obligatoria)
    if (!formRecepcion.latitud || !formRecepcion.longitud) {
        Swal.fire({
            icon: 'warning',
            title: 'Ubicación en el mapa requerida',
            text: 'Por favor, haz clic o arrastra el marcador en el mapa hasta tu ubicación exacta, o usa el botón "Usar mi GPS actual".',
            confirmButtonColor: '#172A3D',
        });
        return;
    }

    guardandoRecepcion.value = true;
    try {
        const response = await axios.post(route('perfil.informacion-recepcion.store'), formRecepcion);
        informacionRecepcion.value = response.data.informacion;
        mostrarModalRecepcion.value = false;
        Swal.fire({
            icon: 'success',
            title: 'Residencia registrada',
            text: 'La dirección y su ubicación exacta en el mapa han sido guardadas correctamente.',
            confirmButtonColor: '#172A3D',
        });
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: error.response?.data?.message ?? 'Ocurrió un error al guardar la información.',
            confirmButtonColor: '#172A3D',
        });
    } finally {
        guardandoRecepcion.value = false;
    }
}

async function eliminarRecepcion() {
    if (guardandoRecepcion.value) return;
    guardandoRecepcion.value = true;
    try {
        await axios.delete(route('perfil.informacion-recepcion.destroy'));
        informacionRecepcion.value = null;
        Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            text: 'La información de residencia fue eliminada.',
            confirmButtonColor: '#172A3D',
        });
    } catch (error) {
        console.error('Error al eliminar información de residencia:', error);
    } finally {
        guardandoRecepcion.value = false;
    }
}

const iniciarSinExperiencia = async (marcado) => {
    if (!marcado) return;
    const resultado = await Swal.fire({
        title: '¿No tienes experiencia laboral?',
        text: 'Puedes confirmar que buscas tu primer empleo o cancelar y registrar tu experiencia laboral.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar y registrar experiencia',
        confirmButtonColor: '#3B7A70',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
    });

    if (resultado.isConfirmed) {
        formularioExperiencia.reset();
        formularioExperiencia.clearErrors();
        formularioExperiencia.sin_experiencia = true;
        formularioExperiencia.post(route('perfil.experiencia.store'), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Perfil actualizado',
                    text: 'Has indicado que estás buscando tu primer empleo.',
                    confirmButtonColor: '#3B7A70',
                });
            },
            onError: () => {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo guardar',
                    text: 'Ocurrió un error al registrar tu declaración.',
                });
            },
        });
        return;
    }
    formularioExperiencia.sin_experiencia = false;
};
</script>

<template>

    <Head title="Mi perfil" />

    <AuthenticatedLayout>
        <div class="perfil-page min-h-screen overflow-x-hidden bg-[#FFF9F0] text-[#172A3D]">

            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
                <div class="perfil-orb perfil-orb-one"></div>
                <div class="perfil-orb perfil-orb-two"></div>
                <div class="perfil-honey"></div>
            </div>

            <main class="mx-auto max-w-[1320px] px-4 py-5 sm:px-6 lg:px-8">

                <!-- ENCABEZADO -->
                <div class="mb-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="text-lg">🐝</span>
                                <span class="text-[9px] font-black uppercase tracking-[0.22em] text-[#D99A24]">
                                    Portal Deseret
                                </span>
                            </div>
                            <h1 class="text-3xl font-black tracking-tight text-[#172A3D] sm:text-4xl">
                                Mi perfil
                            </h1>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-[#667587]">
                                Tu identidad dentro de Portal Deseret. Completa tu información para aprovechar mejor
                                empleos, servicios y comunidad.
                            </p>
                        </div>

                        <div
                            class="hidden rounded-2xl border border-white/80 bg-white/80 px-4 py-3 text-right shadow-sm backdrop-blur sm:block">
                            <p class="text-[8px] font-black uppercase tracking-[0.18em] text-[#9A6A12]">Tu espacio</p>
                            <p class="mt-1 text-sm font-black text-[#172A3D]">{{ nombreRol }}</p>
                        </div>
                    </div>
                </div>

                <!-- AVISO CONTRASEÑA -->
                <div v-if="debeCambiarPassword"
                    class="mb-5 rounded-2xl border border-[#F4C542]/50 bg-[#FFF3C7] p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="text-xl">⚠️</span>
                        <div>
                            <h2 class="text-sm font-black text-[#7A5700]">Debes cambiar tu contraseña</h2>
                            <p class="mt-1 text-sm leading-5 text-[#8B6818]">
                                Por seguridad, debes actualizar tu contraseña antes de continuar utilizando tu cuenta.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- HERO DEL PERFIL -->
                <section class="profile-hero mb-5">
                    <div class="relative z-10 flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-7">
                        <div class="relative shrink-0">
                            <div class="profile-photo">
                                <img v-if="tieneFoto" :src="urlFoto" :alt="`Fotografía de ${usuario.name}`"
                                    class="h-full w-full object-cover" />
                                <span v-else class="text-4xl font-black text-[#172A3D]">{{ inicial }}</span>
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 flex h-9 w-9 items-center justify-center rounded-full border-4 border-white bg-[#F4C542] text-sm shadow-lg">
                                🐝
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                    {{ usuario.name }}
                                </h2>
                                <span
                                    class="rounded-full bg-[#FFF1B8] px-3 py-1 text-[8px] font-black uppercase tracking-[0.12em] text-[#795900]">
                                    {{ nombreRol }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-[#667587]">{{ usuario.email }}</p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="profile-pill">✓ Cuenta activa</span>
                                <span v-if="usuario.email_verified_at" class="profile-pill">✓ Correo verificado</span>
                                <span class="profile-pill profile-pill-gold">🐝 Miembro de Portal Deseret</span>
                            </div>
                        </div>

                        <div class="hidden min-w-[170px] text-right lg:block">
                            <p class="text-[8px] font-black uppercase tracking-[0.16em] text-[#9A6A12]">Miembro desde
                            </p>
                            <p class="mt-1 text-sm font-black text-[#172A3D]">{{ formatoFecha(usuario.created_at) }}</p>
                        </div>
                    </div>
                </section>

                <!-- 01 · INFORMACIÓN PERSONAL -->
                <section class="profile-card">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">01 · Identidad</span>
                            <h2 class="section-title">Información personal</h2>
                            <p class="section-description">Mantén actualizados los datos principales de tu perfil.</p>
                        </div>
                        <span class="section-icon">👤</span>
                    </div>

                    <form @submit.prevent="guardarPerfil" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="form-label">Nombre completo</label>
                            <input id="name" v-model="formularioPerfil.name" type="text" autocomplete="name"
                                class="form-input" />
                            <p v-if="formularioPerfil.errors.name" class="form-error">{{ formularioPerfil.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" v-model="formularioPerfil.email" type="email" autocomplete="email"
                                class="form-input" />
                            <p v-if="formularioPerfil.errors.email" class="form-error">{{ formularioPerfil.errors.email
                                }}</p>
                        </div>

                        <div>
                            <label for="telefono" class="form-label">Número de teléfono</label>
                            <input id="telefono" v-model="formularioPerfil.telefono" type="tel" autocomplete="tel"
                                placeholder="Ej. 3001234567" class="form-input" />
                            <p class="form-help">Este número podrá utilizarse para contactarte en las funcionalidades
                                que lo requieran.</p>
                            <p v-if="formularioPerfil.errors.telefono" class="form-error">{{
                                formularioPerfil.errors.telefono }}</p>
                        </div>

                        <div>
                            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                            <input id="fecha_nacimiento" :value="formularioPerfil.fecha_nacimiento"
                                @input="manejarFecha(formularioPerfil, 'fecha_nacimiento', $event)" type="text"
                                inputmode="numeric" autocomplete="bday" placeholder="dd/mm/aaaa" maxlength="10"
                                class="form-input" />
                            <p class="form-help">Este dato será privado y no se mostrará públicamente por defecto.</p>
                            <p v-if="formularioPerfil.errors.fecha_nacimiento" class="form-error">{{
                                formularioPerfil.errors.fecha_nacimiento }}</p>
                        </div>

                        <div class="flex items-end justify-end md:col-span-2">
                            <button type="submit" :disabled="formularioPerfil.processing" class="primary-button">
                                {{ formularioPerfil.processing ? 'Guardando...' : 'Guardar información' }}
                            </button>
                        </div>
                    </form>
                </section>

                <!-- FORMACIÓN + EXPERIENCIA -->
                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <!-- 02 · FORMACIÓN -->
                    <section class="profile-card">
                        <div class="profile-section-title">
                            <div>
                                <span class="section-kicker">02 · Formación</span>
                                <h2 class="section-title">Formación académica</h2>
                                <p class="section-description">Registra tus estudios, títulos y procesos de formación.
                                </p>
                            </div>
                            <span class="section-icon section-icon-blue">🎓</span>
                        </div>

                        <div v-if="formaciones.length" class="space-y-3">
                            <article v-for="formacion in formaciones" :key="formacion.id" class="timeline-card">
                                <div class="timeline-dot bg-[#2F80ED]"></div>
                                <div>
                                    <h3 class="font-black text-[#172A3D]">{{ formacion.programa }}</h3>
                                    <p class="mt-1 text-xs font-bold text-[#526174]">{{ formacion.institucion }}</p>
                                    <p class="mt-1 text-[11px] text-[#718096]">{{ formacion.nivel }}</p>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" class="small-button" @click="editarFormacion(formacion)">✏️
                                        Editar</button>
                                    <button type="button" class="small-button" @click="eliminarFormacion(formacion)"
                                        :disabled="formularioFormacion.processing">🗑️ Eliminar</button>
                                </div>
                            </article>
                        </div>

                        <div v-else class="empty-professional">
                            <span class="empty-icon">🎓</span>
                            <p class="font-black text-[#172A3D]">Tu formación aparecerá aquí</p>
                            <p class="mt-1 text-xs leading-5 text-[#718096]">Agrega tus estudios para búsquedas
                                laborales.</p>
                        </div>

                        <button type="button" class="secondary-button mt-4" @click="abrirModalFormacion">
                            ＋ Agregar formación
                        </button>
                    </section>

                    <!-- 03 · EXPERIENCIA -->
                    <section class="profile-card">
                        <div class="profile-section-title">
                            <div>
                                <span class="section-kicker">03 · Trayectoria</span>
                                <h2 class="section-title">Experiencia laboral</h2>
                                <p class="section-description">Construye tu historial profesional para futuras
                                    oportunidades.</p>
                            </div>
                            <span class="section-icon section-icon-green">💼</span>
                        </div>

                        <div v-if="experiencias.length" class="space-y-3">
                            <article v-for="experiencia in experiencias" :key="experiencia.id" class="timeline-card">
                                <div class="timeline-dot bg-[#3B7A70]"></div>
                                <div class="min-w-0 flex-1">
                                    <template v-if="experiencia.sin_experiencia">
                                        <h3 class="font-black text-[#172A3D]">Sin experiencia laboral</h3>
                                        <p class="mt-1 text-xs font-bold text-[#526174]">Buscando mi primer empleo</p>
                                    </template>
                                    <template v-else>
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <h3 class="font-black text-[#172A3D]">{{ experiencia.cargo }}</h3>
                                                <p class="mt-1 text-xs font-bold text-[#526174]">{{ experiencia.empresa
                                                    }}</p>
                                                <p class="mt-1 text-[11px] text-[#718096]">{{
                                                    formatoPeriodoExperiencia(experiencia) }}</p>
                                            </div>
                                            <span v-if="experiencia.actualmente_trabaja"
                                                class="inline-flex w-fit rounded-full bg-[#E6F4F0] px-2.5 py-1 text-[9px] font-black text-[#287247]">
                                                Actualmente
                                            </span>
                                        </div>
                                        <p v-if="experiencia.descripcion" class="mt-2 text-xs leading-5 text-[#718096]">
                                            {{ experiencia.descripcion }}
                                        </p>
                                    </template>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button type="button" class="small-button"
                                            @click="editarExperiencia(experiencia)">✏️ Editar</button>
                                        <button type="button" class="small-button"
                                            @click="eliminarExperiencia(experiencia)"
                                            :disabled="formularioExperiencia.processing">🗑️ Eliminar</button>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div v-else class="empty-professional">
                            <span class="empty-icon">💼</span>
                            <p class="font-black text-[#172A3D]">Tu experiencia aparecerá aquí</p>
                            <p class="mt-1 text-xs leading-5 text-[#718096]">Registra tu trayectoria o indica si buscas
                                tu primer empleo.</p>
                        </div>

                        <div v-if="experiencias.length === 0"
                            class="mt-4 rounded-xl border border-[#DCE8E4] bg-[#F5FAF8] p-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-[#3B7A70] focus:ring-[#3B7A70]"
                                    :checked="false" @change="iniciarSinExperiencia($event.target.checked)"
                                    :disabled="formularioExperiencia.processing" />
                                <span>
                                    <span class="block text-sm font-bold text-[#172A3D]">No tengo experiencia
                                        laboral</span>
                                    <span class="mt-1 block text-xs leading-5 text-[#718096]">Estoy buscando mi primer
                                        empleo.</span>
                                </span>
                            </label>
                        </div>

                        <button v-if="experiencias.length === 0 || !experiencias.some(e => e.sin_experiencia)"
                            type="button" @click="abrirModalExperiencia" class="secondary-button mt-4">
                            ＋ Agregar experiencia
                        </button>
                    </section>
                </div>

                <!-- 04 · SKILLS -->
                <section class="profile-card mt-5">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">04 · Capacidades</span>
                            <h2 class="section-title">Skills y habilidades</h2>
                            <p class="section-description">Selecciona los conocimientos y habilidades técnicas que
                                puedes aportar.</p>
                        </div>
                        <span class="section-icon section-icon-purple">🧠</span>
                    </div>

                    <div v-if="skillsUsuario.length" class="flex flex-wrap gap-2">
                        <div v-for="skill in skillsUsuario" :key="skill.id" class="skill-tag">
                            <span>{{ skill.name }}</span>
                            <small>· {{ skill.nivel }}</small>
                            <button type="button" class="ml-1 text-[#9A6A12] transition hover:text-[#C24141]"
                                title="Eliminar skill" @click="eliminarSkill(skill)">×</button>
                        </div>
                    </div>

                    <div v-else class="empty-professional">
                        <span class="empty-icon">🧠</span>
                        <p class="font-black text-[#172A3D]">Construye tu perfil de habilidades</p>
                        <p class="mt-1 max-w-xl text-xs leading-5 text-[#718096]">Puedes registrar hasta 25 skills con
                            su nivel de
                            dominio.</p>
                    </div>

                    <button type="button" class="secondary-button mt-4" @click="abrirModalSkills">
                        ＋ {{ skillsUsuario.length ? 'Editar skills' : 'Agregar skills' }}
                    </button>
                </section>

                <!-- 05 · HOJA DE VIDA -->
                <section class="profile-card mt-5">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">05 · Empleo</span>
                            <h2 class="section-title">Mi hoja de vida</h2>
                            <p class="section-description">Guarda tu hoja de vida en PDF para compartirla en
                                postulaciones laborales.
                            </p>
                        </div>
                        <span class="section-icon section-icon-red">📄</span>
                    </div>

                    <div v-if="hojaVida" class="space-y-4">
                        <div class="cv-file">
                            <div class="cv-file-icon">📄</div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-black text-[#172A3D]">{{ hojaVida.nombre_original ?? 'Hoja de vida.pdf' }}</p>
                                <p class="mt-1 text-xs text-[#718096]">
                                    PDF
                                    <span v-if="hojaVida.tamanio">· {{ formatoTamanioArchivo(hojaVida.tamanio) }}</span>
                                    <span>· Actualizada {{ formatoFecha(hojaVida.updated_at ?? hojaVida.created_at)
                                        }}</span>
                                </p>
                            </div>
                            <a :href="urlHojaVida(hojaVida.id)" target="_blank" rel="noopener noreferrer"
                                class="small-button">👁️
                                Ver</a>
                            <a :href="urlDescargaHojaVida(hojaVida.id)" class="small-button">↓ Descargar</a>
                            <button type="button" class="small-button" :disabled="formularioHojaVida.processing"
                                @click="eliminarHojaVida">🗑️ Eliminar</button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <input ref="inputHojaVida" type="file" accept=".pdf,application/pdf" class="hidden"
                                @change="cambiarHojaVida" />
                            <button type="button" class="secondary-button" @click="seleccionarHojaVida">📄 Reemplazar
                                hoja de
                                vida</button>
                            <button v-if="formularioHojaVida.archivo" type="button" class="primary-button"
                                :disabled="formularioHojaVida.processing" @click="guardarHojaVida">
                                {{ formularioHojaVida.processing ? 'Subiendo...' : 'Guardar nuevo PDF' }}
                            </button>
                        </div>
                        <p v-if="formularioHojaVida.archivo" class="text-xs font-bold text-[#3B7A70]">Archivo
                            seleccionado: {{
                            formularioHojaVida.archivo.name }}</p>
                    </div>

                    <div v-else class="cv-empty">
                        <div class="cv-upload-symbol">📄</div>
                        <div class="flex-1">
                            <h3 class="font-black text-[#172A3D]">Aún no tienes una hoja de vida</h3>
                            <p class="mt-1 text-xs leading-5 text-[#718096]">Sube tu documento en PDF para postularte a
                                ofertas de
                                empleo.</p>
                        </div>
                        <div class="w-full sm:w-auto">
                            <input ref="inputHojaVida" type="file" accept=".pdf,application/pdf" class="hidden"
                                @change="cambiarHojaVida" />
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="secondary-button" @click="seleccionarHojaVida">📄
                                    Seleccionar PDF</button>
                                <button v-if="formularioHojaVida.archivo" type="button" class="primary-button"
                                    :disabled="formularioHojaVida.processing" @click="guardarHojaVida">
                                    {{ formularioHojaVida.processing ? 'Subiendo...' : 'Cargar hoja de vida' }}
                                </button>
                            </div>
                            <p v-if="formularioHojaVida.archivo" class="mt-2 text-xs font-bold text-[#3B7A70]">{{
                                formularioHojaVida.archivo.name }}</p>
                        </div>
                    </div>
                </section>

                <!-- 06 · RESIDENCIA Y GEORREFERENCIACIÓN -->
                <section class="profile-card mt-5">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">06 · Residencia y Georreferenciación</span>
                            <h2 class="section-title">Ubicación de residencia</h2>
                            <p class="section-description">
                                Base territorial utilizada para calcular distancias en entregas, servicios presenciales,
                                cursos locales
                                y empleos de tu zona.
                            </p>
                        </div>
                        <span class="section-icon section-icon-blue">📍</span>
                    </div>

                    <div v-if="informacionRecepcion" class="rounded-2xl border border-[#E9EDF0] bg-[#FBFCFD] p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex min-w-0 gap-3">
                                <div class="future-module-icon">🏡</div>
                                <div class="min-w-0">
                                    <h3 class="font-black text-[#172A3D]">Residencia registrada</h3>
                                    <p class="mt-1 text-sm font-bold text-[#526174]">
                                        {{ informacionRecepcion.numero_via }}
                                    </p>
                                    <p v-if="informacionRecepcion.complemento" class="mt-1 text-xs text-[#718096]">{{
                                        informacionRecepcion.complemento }}</p>
                                    <p class="mt-1 text-xs text-[#718096]">
                                        {{ informacionRecepcion.barrio }} · {{ informacionRecepcion.ciudad }}, {{
                                            informacionRecepcion.departamento }}
                                    </p>

                                    <!-- BADGE DE GEORREFERENCIACIÓN -->
                                    <div class="mt-2.5 flex items-center gap-2">
                                        <span v-if="informacionRecepcion.latitud && informacionRecepcion.longitud"
                                            class="inline-flex items-center gap-1.5 rounded-full bg-[#EEF8F2] px-2.5 py-1 text-[9px] font-black text-black">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#37A866]"></span>
                                            Georreferenciado (Lat: {{ Number(informacionRecepcion.latitud).toFixed(4)
                                            }}, Lng: {{
                                                Number(informacionRecepcion.longitud).toFixed(4) }})
                                        </span>
                                        <span v-else
                                            class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF4CD] px-2.5 py-1 text-[9px] font-black text-black">
                                            ⚠️ Sin coordenadas fijas
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="secondary-button" @click="abrirModalRecepcion">✏️
                                    Editar</button>
                                <button type="button" class="small-button" :disabled="guardandoRecepcion"
                                    @click="eliminarRecepcion">🗑️
                                    Eliminar</button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="future-module">
                        <div class="future-module-icon">📍</div>
                        <div>
                            <h3 class="font-black text-[#172A3D]">Aún no has registrado tu residencia</h3>
                            <p class="mt-1 text-xs leading-5 text-[#718096]">
                                Registra tu lugar base para habilitar el filtro por cercanía en servicios, cursos,
                                compras y ofertas
                                laborales.
                            </p>
                        </div>
                    </div>

                    <button type="button" class="primary-button mt-4" @click="abrirModalRecepcion">
                        ＋ {{ informacionRecepcion ? 'Editar residencia y ubicación' : 'Registrar residencia' }}
                    </button>
                </section>

                <!-- 07 · INFORMACIÓN DE CUENTA -->
                <section class="profile-card mt-5">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">07 · Cuenta</span>
                            <h2 class="section-title">Información de cuenta</h2>
                            <p class="section-description">Información general relacionada con tu cuenta.</p>
                        </div>
                        <span class="section-icon">⚙️</span>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="account-stat">
                            <span>Miembro desde</span>
                            <strong>{{ formatoFecha(usuario.created_at) }}</strong>
                        </div>
                        <div class="account-stat">
                            <span>Última visita</span>
                            <strong>{{ formatoFechaHora(usuario.ultima_visita) }}</strong>
                            <small v-if="diasDesdeUltimaVisita !== null">Hace {{ diasDesdeUltimaVisita }} días</small>
                        </div>
                        <div class="account-stat">
                            <span>Número de accesos</span>
                            <strong>{{ usuario.numero_accesos ?? 0 }}</strong>
                        </div>
                        <div class="account-stat">
                            <span>Estado</span>
                            <strong class="flex items-center gap-2 text-[#247A4A]">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#37A866]"></span> Activa
                            </strong>
                        </div>
                    </div>
                </section>

                <!-- 08 · FOTOGRAFÍA -->
                <section class="profile-card mt-5">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">08 · Imagen</span>
                            <h2 class="section-title">Fotografía de perfil</h2>
                            <p class="section-description">Utiliza una fotografía que ayude a identificarte en la
                                comunidad.</p>
                        </div>
                        <span class="section-icon">📷</span>
                    </div>

                    <form @submit.prevent="guardarFoto" enctype="multipart/form-data"
                        class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="profile-photo-small">
                            <img v-if="tieneFoto" :src="urlFoto" :alt="`Fotografía de ${usuario.name}`"
                                class="h-full w-full object-cover" />
                            <span v-else class="text-3xl font-black text-[#172A3D]">{{ inicial }}</span>
                        </div>

                        <div class="flex-1">
                            <input ref="inputFoto" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                                @change="cambiarFoto" />
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="seleccionarFoto" class="secondary-button">Seleccionar
                                    fotografía</button>
                                <button type="submit" :disabled="formularioFoto.processing || !formularioFoto.foto"
                                    class="primary-button">
                                    {{ formularioFoto.processing ? 'Subiendo...' : 'Guardar fotografía' }}
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-[#718096]">JPG, JPEG, PNG o WEBP. Tamaño máximo: 5 MB.</p>
                            <p v-if="formularioFoto.foto" class="mt-2 text-xs font-bold text-[#3B7A70]">{{
                                formularioFoto.foto.name }}
                            </p>
                        </div>
                    </form>
                </section>

                <!-- 09 · SEGURIDAD -->
                <section class="profile-card mt-5 mb-8">
                    <div class="profile-section-title">
                        <div>
                            <span class="section-kicker">09 · Seguridad</span>
                            <h2 class="section-title">Seguridad de la cuenta</h2>
                            <p class="section-description">Cambia tu contraseña cuando lo necesites.</p>
                        </div>
                        <span class="section-icon">🔐</span>
                    </div>

                    <form @submit.prevent="cambiarPassword" class="grid grid-cols-1 gap-5 md:grid-cols-3">
                        <div>
                            <label for="current_password" class="form-label">Contraseña actual</label>
                            <input id="current_password" v-model="formularioPassword.current_password" type="password"
                                autocomplete="current-password" class="form-input" />
                        </div>
                        <div>
                            <label for="password" class="form-label">Nueva contraseña</label>
                            <input id="password" v-model="formularioPassword.password" type="password"
                                autocomplete="new-password" class="form-input" />
                        </div>
                        <div>
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <input id="password_confirmation" v-model="formularioPassword.password_confirmation"
                                type="password" autocomplete="new-password" class="form-input" />
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <button type="submit" :disabled="formularioPassword.processing" class="primary-button">
                                {{ formularioPassword.processing ? 'Actualizando...' : 'Cambiar contraseña' }}
                            </button>
                        </div>
                    </form>
                </section>

            </main>
        </div>
    </AuthenticatedLayout>

    <!-- ============================================================= -->
    <!-- MODAL OBLIGATORIO: DATOS INICIALES (FOTO, TELÉFONO, NACIMIENTO) -->
    <!-- ============================================================= -->
    <Teleport to="body">
        <div v-if="modalObligatorioAbierto"
            class="fixed inset-0 z-[200] flex items-center justify-center bg-[#172A3D]/80 px-4 py-6 backdrop-blur-md">
            <div
                class="max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-[1.75rem] bg-white p-6 sm:p-8 shadow-2xl border border-amber-300">
                <div class="text-center">
                    <span
                        class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#FFF1B8] text-2xl shadow-sm">
                        🐝
                    </span>
                    <h2 class="mt-3 text-2xl font-black text-[#172A3D]">Completa tu perfil</h2>
                    <p class="mt-2 text-sm text-[#718096]">
                        Para poder continuar utilizando la plataforma, es obligatorio registrar tu fotografía, teléfono
                        y
                        fecha de nacimiento.
                    </p>
                </div>

                <form @submit.prevent="guardarDatosObligatorios" class="mt-6 space-y-5">
                    <div class="flex flex-col items-center justify-center border-b border-[#E9EDF0] pb-5">
                        <label class="form-label mb-2 text-center">Fotografía de perfil *</label>
                        <div class="relative cursor-pointer" @click="seleccionarFotoObligatoria">
                            <div class="profile-photo h-24 w-24">
                                <img v-if="previewFotoObligatoria" :src="previewFotoObligatoria"
                                    class="h-full w-full object-cover" />
                                <img v-else-if="tieneFoto" :src="urlFoto" class="h-full w-full object-cover" />
                                <span v-else class="text-3xl font-black text-[#172A3D]">{{ inicial }}</span>
                            </div>
                            <div class="absolute bottom-0 right-0 rounded-full bg-[#172A3D] p-1.5 text-white shadow">
                                📷
                            </div>
                        </div>
                        <input ref="inputFotoObligatoria" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                            @change="cambiarFotoObligatoria" />
                        <button type="button" @click="seleccionarFotoObligatoria"
                            class="mt-3 text-xs font-bold text-[#D99A24] hover:underline">
                            {{ formObligatorio.foto || tieneFoto ? 'Cambiar fotografía' : 'Seleccionar fotografía' }}
                        </button>
                    </div>

                    <div>
                        <label for="tel-obligatorio" class="form-label">Número de teléfono *</label>
                        <input id="tel-obligatorio" v-model="formObligatorio.telefono" type="tel"
                            placeholder="Ej. 3001234567" required class="form-input" />
                    </div>

                    <div>
                        <label for="nacimiento-obligatorio" class="form-label">Fecha de nacimiento (dd/mm/aaaa)
                            *</label>
                        <input id="nacimiento-obligatorio" :value="formObligatorio.fecha_nacimiento"
                            @input="manejarFecha(formObligatorio, 'fecha_nacimiento', $event)" type="text"
                            inputmode="numeric" placeholder="dd/mm/aaaa" maxlength="10" required class="form-input" />
                    </div>

                    <button type="submit" :disabled="formObligatorio.processing"
                        class="primary-button mt-4 w-full py-3.5 text-sm">
                        {{ formObligatorio.processing ? 'Guardando datos...' : 'Guardar y continuar' }}
                    </button>
                </form>
            </div>
        </div>
    </Teleport>

    <!-- MODAL FORMACIÓN -->
    <Teleport to="body">
        <div v-if="modalFormacionAbierto"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#172A3D]/55 px-4 py-6 backdrop-blur-sm"
            @click.self="cerrarModalFormacion" @keydown="cerrarModalConEscape" tabindex="-1">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[1.5rem] bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-[#E9EDF0] px-5 py-4 sm:px-6">
                    <div>
                        <span class="section-kicker">Perfil profesional</span>
                        <h2 class="mt-1 text-xl font-black text-[#172A3D]">
                            {{ editandoFormacion ? 'Editar formación académica' : 'Agregar formación académica' }}
                        </h2>
                    </div>
                    <button type="button" class="small-button shrink-0 text-lg" @click="cerrarModalFormacion">×</button>
                </div>
                <form class="space-y-5 px-5 py-5 sm:px-6" @submit.prevent="guardarFormacion">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="relative">
                            <label class="form-label">Nivel académico</label>
                            <button type="button" @click="dropdownNivelAbierto = !dropdownNivelAbierto"
                                class="form-input flex w-full items-center justify-between text-left font-semibold text-[#172A3D]">
                                <span
                                    :class="formularioFormacion.nivel ? 'text-[#172A3D]' : 'text-slate-400 font-normal'">
                                    {{ formularioFormacion.nivel || 'Selecciona' }}
                                </span>
                                <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': dropdownNivelAbierto }" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div v-if="dropdownNivelAbierto"
                                class="absolute left-0 top-full z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl">
                                <button v-for="opcion in nivelesAcademicos" :key="opcion" type="button"
                                    @click="seleccionarNivel(opcion)"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs sm:text-sm font-semibold transition"
                                    :class="formularioFormacion.nivel === opcion ? 'bg-[#FFF9E8] text-[#9A6A12]' : 'text-slate-800 hover:bg-slate-100 hover:text-slate-950'">
                                    <span>{{ opcion }}</span>
                                    <span v-if="formularioFormacion.nivel === opcion"
                                        class="text-xs text-[#D99A24]">✓</span>
                                </button>
                            </div>
                            <p v-if="formularioFormacion.errors.nivel" class="form-error">{{
                                formularioFormacion.errors.nivel }}</p>
                        </div>

                        <div>
                            <label class="form-label">Institución</label>
                            <input v-model="formularioFormacion.institucion" type="text" class="form-input"
                                placeholder="Nombre de la institución" />
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Programa o título</label>
                        <input v-model="formularioFormacion.programa" type="text" class="form-input"
                            placeholder="Ej. Ingeniería" required />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Fecha de inicio</label>
                            <input v-model="formularioFormacion.fecha_inicio" type="date" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Fecha de finalización</label>
                            <input v-model="formularioFormacion.fecha_fin" type="date" class="form-input"
                                :disabled="formularioFormacion.en_curso" />
                        </div>
                    </div>
                    <label
                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-[#E9EDF0] bg-[#FBFCFD] p-4">
                        <input v-model="formularioFormacion.en_curso" type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded text-[#D99A24]" />
                        <span class="text-sm font-black text-[#172A3D]">Actualmente en curso</span>
                    </label>
                    <div class="flex justify-end gap-2 border-t border-[#E9EDF0] pt-4">
                        <button type="button" class="secondary-button" @click="cerrarModalFormacion">Cancelar</button>
                        <button type="submit" class="primary-button"
                            :disabled="formularioFormacion.processing">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <!-- MODAL EXPERIENCIA -->
    <Teleport to="body">
        <div v-if="modalExperienciaAbierto"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#172A3D]/55 px-4 py-6 backdrop-blur-sm"
            @click.self="cerrarModalExperiencia" @keydown="cerrarModalConEscape" tabindex="-1">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[1.5rem] bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-[#E9EDF0] px-5 py-4 sm:px-6">
                    <div>
                        <span class="section-kicker">Perfil profesional</span>
                        <h2 class="mt-1 text-xl font-black text-[#172A3D]">
                            {{ editandoExperiencia ? 'Editar experiencia laboral' : 'Agregar experiencia laboral' }}
                        </h2>
                    </div>
                    <button type="button" class="small-button shrink-0 text-lg"
                        @click="cerrarModalExperiencia">×</button>
                </div>
                <form class="space-y-5 px-5 py-5 sm:px-6" @submit.prevent="guardarExperiencia">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Empresa</label>
                            <input v-model="formularioExperiencia.empresa" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label class="form-label">Cargo</label>
                            <input v-model="formularioExperiencia.cargo" type="text" class="form-input" required />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Fecha de inicio</label>
                            <input v-model="formularioExperiencia.fecha_inicio" type="date" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Fecha de finalización</label>
                            <input v-model="formularioExperiencia.fecha_fin" type="date" class="form-input"
                                :disabled="formularioExperiencia.actualmente_trabaja" />
                        </div>
                    </div>
                    <label
                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-[#E9EDF0] bg-[#FBFCFD] p-4">
                        <input v-model="formularioExperiencia.actualmente_trabaja" type="checkbox"
                            class="mt-0.5 h-4 w-4 rounded text-[#D99A24]" />
                        <span class="text-sm font-black text-[#172A3D]">Actualmente trabajo aquí</span>
                    </label>
                    <div class="flex justify-end gap-2 border-t border-[#E9EDF0] pt-4">
                        <button type="button" class="secondary-button" @click="cerrarModalExperiencia">Cancelar</button>
                        <button type="submit" class="primary-button"
                            :disabled="formularioExperiencia.processing">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <!-- MODAL SKILLS -->
    <Teleport to="body">
        <div v-if="modalSkillsAbierto"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#172A3D]/55 px-4 py-6 backdrop-blur-sm"
            @click.self="cerrarModalSkills" @keydown="cerrarModalConEscape" tabindex="-1">
            <div
                class="flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-[1.5rem] bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-[#E9EDF0] px-5 py-4 sm:px-6">
                    <div>
                        <span class="section-kicker">Perfil profesional</span>
                        <h2 class="mt-1 text-xl font-black text-[#172A3D]">Mis skills y habilidades</h2>
                    </div>
                    <button type="button" class="small-button shrink-0 text-lg" @click="cerrarModalSkills">×</button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">
                    <div v-if="cargandoBancoSkills" class="text-center py-10">Cargando catálogo...</div>
                    <div v-else class="space-y-4">
                        <div class="relative">
                            <label class="form-label mb-1">1. Categoría</label>
                            <button type="button"
                                @click="dropdownCategoriaSkillAbierto = !dropdownCategoriaSkillAbierto"
                                class="form-input flex w-full items-center justify-between text-left font-semibold text-[#172A3D]">
                                <span :class="categoriaSeleccionada ? 'text-[#172A3D]' : 'text-slate-400 font-normal'">
                                    {{ categoriaSeleccionada?.name || 'Selecciona una categoría' }}
                                </span>
                                <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': dropdownCategoriaSkillAbierto }" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="dropdownCategoriaSkillAbierto"
                                class="absolute left-0 top-full z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl">
                                <button v-for="cat in bancoSkills" :key="cat.id" type="button"
                                    @click="seleccionarCategoriaModal(cat.id)"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs sm:text-sm font-semibold transition"
                                    :class="Number(categoriaSeleccionadaId) === Number(cat.id) ? 'bg-[#FFF9E8] text-[#9A6A12]' : 'text-slate-800 hover:bg-slate-100 hover:text-slate-950'">
                                    <span>{{ cat.name }}</span>
                                    <span v-if="Number(categoriaSeleccionadaId) === Number(cat.id)"
                                        class="text-xs text-[#D99A24]">✓</span>
                                </button>
                            </div>
                        </div>
                        <div v-if="subcategoriasDisponibles.length" class="relative">
                            <label class="form-label mb-1">2. Subcategoría</label>
                            <button type="button"
                                @click="dropdownSubcategoriaSkillAbierto = !dropdownSubcategoriaSkillAbierto"
                                class="form-input flex w-full items-center justify-between text-left font-semibold text-[#172A3D]">
                                <span
                                    :class="subcategoriaSeleccionada ? 'text-[#172A3D]' : 'text-slate-400 font-normal'">
                                    {{ subcategoriaSeleccionada?.name || 'Selecciona una subcategoría' }}
                                </span>
                                <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': dropdownSubcategoriaSkillAbierto }" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="dropdownSubcategoriaSkillAbierto"
                                class="absolute left-0 top-full z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl">
                                <button v-for="sub in subcategoriasDisponibles" :key="sub.id" type="button"
                                    @click="seleccionarSubcategoriaModal(sub.id)"
                                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs sm:text-sm font-semibold transition"
                                    :class="Number(subcategoriaSeleccionadaId) === Number(sub.id) ? 'bg-[#FFF9E8] text-[#9A6A12]' : 'text-slate-800 hover:bg-slate-100 hover:text-slate-950'">
                                    <span>{{ sub.name }}</span>
                                    <span v-if="Number(subcategoriaSeleccionadaId) === Number(sub.id)"
                                        class="text-xs text-[#D99A24]">✓</span>
                                </button>
                            </div>
                        </div>
                        <div v-if="subcategoriaSeleccionada">
                            <input v-model="busquedaSkill" type="search" class="form-input"
                                placeholder="Buscar habilidad dentro de la subcategoría..." />
                        </div>
                        <div v-if="skillsDisponibles.length" class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div v-for="skill in skillsDisponibles" :key="skill.id"
                                class="rounded-xl border bg-white p-3 transition"
                                :class="skillSeleccionada(skill.id) ? 'border-[#D99A24] bg-[#FFF9E8]' : 'border-[#E9EDF0]'">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input type="checkbox" :checked="skillSeleccionada(skill.id)"
                                        class="mt-1 h-4 w-4 rounded border-[#CBD2D9] text-[#D99A24]"
                                        @change="alternarSkill(skill)" />
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-xs font-black text-[#172A3D]">{{ skill.name }}</span>
                                        <span v-if="skill.description" class="mt-1 block text-[10px] text-[#718096]">{{
                                            skill.description }}</span>
                                    </span>
                                </label>
                                <div v-if="skillSeleccionada(skill.id)"
                                    class="relative mt-3 border-t border-[#E8DDBF] pt-2">
                                    <button type="button" @click="toggleDropdownNivel(skill.id)"
                                        class="flex w-full items-center justify-between rounded-lg border bg-white px-3 py-1.5 text-left text-xs font-bold text-[#172A3D]">
                                        <span>{{ obtenerNivelSkill(skill.id) }}</span>
                                        <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div v-if="dropdownNivelSkillAbierto === skill.id"
                                        class="absolute left-0 top-full z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white p-1 shadow-lg">
                                        <button v-for="nivel in nivelesSkill" :key="nivel" type="button"
                                            @click="seleccionarNivelParaSkill(skill.id, nivel)"
                                            class="flex w-full items-center justify-between rounded px-2.5 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-100 hover:text-slate-950">
                                            <span>{{ nivel }}</span>
                                            <span v-if="obtenerNivelSkill(skill.id) === nivel"
                                                class="text-xs text-[#D99A24]">✓</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-[#E9EDF0] p-4 flex justify-end gap-2">
                    <button type="button" class="secondary-button" @click="cerrarModalSkills">Cancelar</button>
                    <button type="button" class="primary-button"
                        :disabled="formularioSkills.processing || cantidadSkills === 0"
                        @click="guardarSkills">Guardar</button>
                </div>
            </div>
        </div>
    </Teleport>

    <!-- ============================================================= -->
    <!-- MODAL RESIDENCIA Y GEORREFERENCIACIÓN -->
    <!-- ============================================================= -->
    <Teleport to="body">
        <div v-if="mostrarModalRecepcion"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#172A3D]/60 px-4 py-6 backdrop-blur-sm"
            @click.self="cerrarModalRecepcion" @keydown="cerrarModalConEscape" tabindex="-1">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-[1.5rem] bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-start justify-between border-b border-[#E9EDF0] pb-4">
                    <div>
                        <span class="section-kicker">Ancla Territorial</span>
                        <h2 class="text-xl font-black text-[#172A3D]">Información de residencia</h2>
                    </div>
                    <button type="button" class="small-button" @click="cerrarModalRecepcion">×</button>
                </div>

                <form class="space-y-4" @submit.prevent="guardarRecepcion">
                    <!-- SELECTOR DE UBICACIÓN -->
                    <UbicacionSelector v-model:pais="formRecepcion.pais"
                        v-model:departamento="formRecepcion.departamento" v-model:ciudad="formRecepcion.ciudad" />

                    <!-- DIRECCIÓN GUIADA Y ESTANDARIZADA -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="form-label text-slate-800">Dirección estructurada *</label>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Nomenclatura
                                oficial</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-6">
                            <!-- 1. TIPO DE VÍA (CUSTOM DROPDOWN 100% LEGIBLE) -->
                            <div class="relative col-span-2 sm:col-span-2">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Tipo de
                                    vía</label>
                                <button type="button"
                                    @click="dropdownTipoViaAbierto = !dropdownTipoViaAbierto; dropdownSectorAbierto = false"
                                    class="form-input mt-1 flex w-full items-center justify-between bg-white text-left font-bold text-slate-900 shadow-sm border border-slate-300">
                                    <span>{{ direccionEstandar.tipo || 'Selecciona' }}</span>
                                    <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                        :class="{ 'rotate-180': dropdownTipoViaAbierto }" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div v-if="dropdownTipoViaAbierto"
                                    class="absolute left-0 top-full z-[120] mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-2xl">
                                    <button v-for="via in tiposVia" :key="via" type="button"
                                        @click="seleccionarTipoVia(via)"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition"
                                        :class="direccionEstandar.tipo === via
                                            ? 'bg-[#FFF9E8] text-[#9A6A12]'
                                            : 'text-slate-900 hover:bg-slate-100 hover:text-slate-950'">
                                        <span>{{ via }}</span>
                                        <span v-if="direccionEstandar.tipo === via"
                                            class="text-xs text-[#D99A24]">✓</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. VÍA PRINCIPAL -->
                            <div class="col-span-2 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Nº /
                                    Vía</label>
                                <input v-model="direccionEstandar.viaPrincipal" type="text" placeholder="Ej. 85G"
                                    class="form-input mt-1 bg-white font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 3. VÍA GENERADORA (# CRUCE) -->
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">#
                                    Cruce</label>
                                <input v-model="direccionEstandar.viaGeneradora" type="text" placeholder="Ej. 24C"
                                    class="form-input mt-1 bg-white font-bold uppercase text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 4. PLACA / PUERTA -->
                            <div class="col-span-1 sm:col-span-1">
                                <label class="text-[9px] font-bold uppercase tracking-wider text-slate-600">-
                                    Placa</label>
                                <input v-model="direccionEstandar.placa" type="text" placeholder="Ej. 50"
                                    class="form-input mt-1 bg-white font-bold text-slate-900 border border-slate-300"
                                    required />
                            </div>

                            <!-- 5. SECTOR (CUSTOM DROPDOWN 100% LEGIBLE) -->
                            <div class="relative col-span-2 sm:col-span-1">
                                <label
                                    class="text-[9px] font-bold uppercase tracking-wider text-slate-600">Sector</label>
                                <button type="button"
                                    @click="dropdownSectorAbierto = !dropdownSectorAbierto; dropdownTipoViaAbierto = false"
                                    class="form-input mt-1 flex w-full items-center justify-between bg-white text-left font-bold text-slate-900 shadow-sm border border-slate-300">
                                    <span>{{ direccionEstandar.sector || 'Ninguno' }}</span>
                                    <svg class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                        :class="{ 'rotate-180': dropdownSectorAbierto }" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div v-if="dropdownSectorAbierto"
                                    class="absolute left-0 top-full z-[120] mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-2xl">
                                    <button v-for="sec in sufijosSector" :key="sec" type="button"
                                        @click="seleccionarSector(sec)"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-xs font-bold transition"
                                        :class="direccionEstandar.sector === sec
                                            ? 'bg-[#FFF9E8] text-[#9A6A12]'
                                            : 'text-slate-900 hover:bg-slate-100 hover:text-slate-950'">
                                        <span>{{ sec }}</span>
                                        <span v-if="direccionEstandar.sector === sec"
                                            class="text-xs text-[#D99A24]">✓</span>
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

                    <!-- BARRIO Y COMPLEMENTOS -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Barrio</label>
                            <input v-model="formRecepcion.barrio" type="text" class="form-input"
                                placeholder="Ej. Modelia" />
                        </div>
                        <div>
                            <label class="form-label">Complemento / Edificio / Apto (opcional)</label>
                            <input v-model="formRecepcion.complemento" type="text" class="form-input"
                                placeholder="Ej. Torre 2 Apto 401" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Punto de referencia</label>
                        <input v-model="formRecepcion.referencia_direccion" type="text" class="form-input"
                            placeholder="Ej. Frente al parque principal" />
                    </div>

                    <!-- MAPA INTERACTIVO Y COORDENADAS -->
                    <div class="rounded-2xl border border-[#E8DDBF] bg-[#FFFDF8] p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-[#9A6A12]">
                                    📍 Georreferenciación Exacta
                                </h4>
                                <p class="mt-1 text-xs text-[#718096]">
                                    Verifica tu ubicación en el mapa interactivo o captúrala con tu GPS satelital.
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button v-if="formRecepcion.latitud || formRecepcion.longitud" type="button"
                                    class="small-button text-xs font-bold text-[#846000] hover:text-black"
                                    @click="limpiarCoordenadas" title="Borrar coordenadas actuales para recalcular">
                                    🔄 Recalcular por dirección
                                </button>
                                <button type="button" class="secondary-button shrink-0" :disabled="obteniendoGps"
                                    @click="obtenerUbicacionGPS">
                                    🛰️ {{ obteniendoGps ? 'Detectando GPS...' : 'Usar mi GPS actual' }}
                                </button>
                            </div>
                        </div>

                        <!-- MINI MAPA LEAFLET -->
                        <div class="mt-3">
                            <MapaSelector v-model:latitud="formRecepcion.latitud"
                                v-model:longitud="formRecepcion.longitud" />
                        </div>

                        <!-- INDICADOR DE COORDENADAS DETECTADAS -->
                        <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-[#E8DDBF]/60 pt-3">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-bold text-black">Latitud:</span>
                                <input v-model="formRecepcion.latitud" type="number" step="any"
                                    class="w-32 rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs text-black font-semibold"
                                    placeholder="Auto (Vacío)" />
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-bold text-black">Longitud:</span>
                                <input v-model="formRecepcion.longitud" type="number" step="any"
                                    class="w-32 rounded-lg border border-slate-300 bg-white px-2 py-1 text-xs text-black font-semibold"
                                    placeholder="Auto (Vacío)" />
                            </div>
                            <p class="text-[11px] text-[#8B6818]">
                                * El pin se ajusta automáticamente, o puedes arrastrarlo directo a tu puerta en el mapa.
                            </p>
                        </div>
                    </div>

                    <!-- PREFERENCIAS DE CONTACTO -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Teléfono de contacto</label>
                            <input v-model="formRecepcion.telefono_contacto" type="tel" class="form-input"
                                placeholder="Ej. 3001234567" />
                        </div>
                        <div>
                            <label class="form-label">WhatsApp</label>
                            <input v-model="formRecepcion.whatsapp" type="tel" class="form-input"
                                placeholder="Ej. 3001234567" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-[#E9EDF0] pt-4">
                        <button type="button" class="secondary-button" @click="cerrarModalRecepcion">Cancelar</button>
                        <button type="submit" class="primary-button" :disabled="guardandoRecepcion">
                            {{ guardandoRecepcion ? 'Guardando...' : 'Guardar residencia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.perfil-page {
    position: relative;
}

.perfil-orb {
    position: absolute;
    border-radius: 9999px;
    filter: blur(75px);
    opacity: .22;
    animation: perfil-drift 18s ease-in-out infinite alternate;
}

.perfil-orb-one {
    top: 120px;
    left: -100px;
    width: 320px;
    height: 320px;
    background: #F4C542;
}

.perfil-orb-two {
    top: 620px;
    right: -130px;
    width: 350px;
    height: 350px;
    background: #2F526D;
    animation-delay: -7s;
}

.perfil-honey {
    position: absolute;
    inset: 0;
    opacity: .12;
    background-image: linear-gradient(30deg, rgba(23, 42, 61, .05) 12%, transparent 12.5%, transparent 87%, rgba(23, 42, 61, .05) 87.5%, rgba(23, 42, 61, .05)), linear-gradient(150deg, rgba(23, 42, 61, .05) 12%, transparent 12.5%, transparent 87%, rgba(23, 42, 61, .05) 87.5%, rgba(23, 42, 61, .05)), linear-gradient(60deg, rgba(23, 42, 61, .04) 25%, transparent 25.5%, transparent 75%, rgba(23, 42, 61, .04) 75%);
    background-size: 48px 84px;
    animation: honey-slide 35s linear infinite;
}

.profile-hero {
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, .95);
    border-radius: 1.65rem;
    background: radial-gradient(circle at 85% 20%, rgba(244, 197, 66, .32), transparent 28%), linear-gradient(115deg, rgba(255, 255, 255, .98), rgba(255, 249, 238, .94));
    box-shadow: 0 16px 42px rgba(23, 42, 61, .10);
}

.profile-card {
    border: 1px solid rgba(255, 255, 255, .95);
    border-radius: 1.45rem;
    background: rgba(255, 255, 255, .95);
    padding: 1.35rem;
    box-shadow: 0 10px 30px rgba(23, 42, 61, .07);
    backdrop-filter: blur(12px);
}

.profile-section-title {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.35rem;
}

.section-kicker {
    display: block;
    color: #9A6A12;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.section-title {
    margin-top: .3rem;
    color: #172A3D;
    font-size: 1.3rem;
    font-weight: 900;
    letter-spacing: -.025em;
}

.section-description {
    margin-top: .3rem;
    max-width: 650px;
    color: #718096;
    font-size: .76rem;
    line-height: 1.45rem;
}

.section-icon {
    display: flex;
    width: 43px;
    height: 43px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #FFF1B8;
    font-size: 18px;
    box-shadow: 0 8px 18px rgba(217, 154, 36, .12);
}

.section-icon-blue {
    background: #E8F2FF;
}

.section-icon-green {
    background: #E6F4F0;
}

.section-icon-purple {
    background: #F1EAFE;
}

.section-icon-red {
    background: #FBEAE5;
}

.profile-photo {
    display: flex;
    width: 108px;
    height: 108px;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 5px solid rgba(255, 255, 255, .9);
    border-radius: 9999px;
    background: #FFF1B8;
    box-shadow: 0 12px 30px rgba(23, 42, 61, .15);
}

.profile-photo-small {
    display: flex;
    width: 82px;
    height: 82px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 9999px;
    background: #FFF1B8;
    box-shadow: 0 8px 20px rgba(23, 42, 61, .10);
}

.profile-pill {
    border-radius: 9999px;
    background: #EEF8F2;
    padding: .38rem .7rem;
    color: #287247;
    font-size: 8px;
    font-weight: 900;
}

.profile-pill-gold {
    background: #FFF4CD;
    color: #846000;
}

.form-label {
    display: block;
    color: #39495A;
    font-size: .73rem;
    font-weight: 800;
}

.form-input {
    margin-top: .45rem;
    display: block;
    width: 100%;
    border: 1px solid #DDE2E7;
    border-radius: .85rem;
    background: #FBFCFD;
    padding: .72rem .85rem;
    color: #263646;
    font-size: .8rem;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

.form-input:focus {
    border-color: #D99A24;
    background: white;
    box-shadow: 0 0 0 3px rgba(217, 154, 36, .12);
}

.form-help {
    margin-top: .4rem;
    color: #8793A0;
    font-size: .65rem;
    line-height: 1.15rem;
}

.form-error {
    margin-top: .3rem;
    color: #C24141;
    font-size: .68rem;
}

.primary-button,
.secondary-button,
.small-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: .85rem;
    font-size: .72rem;
    font-weight: 900;
    transition: transform .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease;
}

.primary-button {
    background: #172A3D;
    color: #FFFFFF !important;
    padding: .72rem 1.1rem;
    box-shadow: 0 8px 20px rgba(23, 42, 61, .12);
}

.primary-button:hover {
    transform: translateY(-2px);
    background: #233C55;
    color: #FFFFFF !important;
    box-shadow: 0 10px 22px rgba(23, 42, 61, .18);
}

.primary-button:disabled,
.secondary-button:disabled,
.small-button:disabled {
    cursor: not-allowed;
    opacity: .5;
    transform: none;
    box-shadow: none;
}

.secondary-button {
    border: 1px solid #DDE2E7;
    background: #FFFFFF;
    color: #263646 !important;
    padding: .68rem .95rem;
}

.secondary-button:hover {
    transform: translateY(-2px);
    background: #172A3D;
    border-color: #172A3D;
    color: #FFFFFF !important;
    box-shadow: 0 8px 18px rgba(23, 42, 61, .12);
}

.small-button {
    border: 1px solid #E1E5E9;
    background: #FFFFFF;
    padding: .5rem .75rem;
    color: #263646 !important;
    font-size: .65rem;
}

.small-button:hover {
    background: #F1F4F8;
    border-color: #CBD5E1;
    color: #0F172A !important;
}

.timeline-card {
    position: relative;
    display: flex;
    gap: .85rem;
    border: 1px solid #E9EDF0;
    border-radius: 1rem;
    background: #FBFCFD;
    padding: .9rem;
}

.timeline-dot {
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    margin-top: .35rem;
    border-radius: 9999px;
    box-shadow: 0 0 0 4px rgba(23, 42, 61, .04);
}

.empty-professional {
    display: flex;
    min-height: 135px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px dashed #D8DEE5;
    border-radius: 1rem;
    background: #FBFCFD;
    padding: 1.2rem;
    text-align: center;
}

.empty-icon {
    display: flex;
    width: 42px;
    height: 42px;
    align-items: center;
    justify-content: center;
    margin-bottom: .65rem;
    border-radius: 14px;
    background: #FFF1B8;
    font-size: 18px;
}

.skill-tag {
    display: inline-flex;
    align-items: center;
    border: 1px solid #E6D9B9;
    border-radius: 9999px;
    background: #FFF9E8;
    padding: .52rem .78rem;
    color: #68501A;
    font-size: .72rem;
    font-weight: 900;
}

.cv-empty,
.cv-file {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    border: 1px solid #E9E0CF;
    border-radius: 1.1rem;
    background: #FFFDF8;
    padding: 1rem;
}

.cv-upload-symbol,
.cv-file-icon {
    display: flex;
    width: 50px;
    height: 50px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background: #FBEAE5;
    font-size: 21px;
}

.cv-file-icon {
    background: #FFF1B8;
}

.future-module {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    border: 1px dashed #D9DDE2;
    border-radius: 1rem;
    background: #FBFCFD;
    padding: 1rem;
}

.future-module-icon {
    display: flex;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background: #E8F2FF;
    font-size: 21px;
}

.account-stat {
    border: 1px solid #E9EDF0;
    border-radius: 1rem;
    background: #FBFCFD;
    padding: .9rem;
}

.account-stat span {
    display: block;
    color: #8A96A3;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.account-stat strong {
    display: block;
    margin-top: .4rem;
    color: #172A3D;
    font-size: .78rem;
    line-height: 1.25rem;
}

.account-stat small {
    display: block;
    margin-top: .15rem;
    color: #8A96A3;
    font-size: .62rem;
}

@keyframes perfil-drift {
    from {
        transform: translate3d(-8px, -5px, 0) scale(1);
    }

    to {
        transform: translate3d(25px, 18px, 0) scale(1.08);
    }
}

@keyframes honey-slide {
    from {
        background-position: 0 0, 0 0, 0 0;
    }

    to {
        background-position: 48px 84px, 48px 84px, 48px 84px;
    }
}
</style>