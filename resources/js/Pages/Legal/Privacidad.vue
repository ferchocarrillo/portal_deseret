<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-[#F3F0E8] text-[#172A3D]">

        <!-- HEADER -->
        <header class="border-b border-[#DDD7CA] bg-[#FFFDF8]">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8">
                <Link href="/" class="flex items-center">
                    <img
                        src="/images/landing/logo.png"
                        alt="Portal Deseret"
                        class="h-auto w-[190px]"
                    />
                </Link>

                <Link
                    href="/"
                    class="rounded-xl px-4 py-2 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#172A3D]"
                >
                    Volver al portal
                </Link>
            </div>
        </header>

        <!-- CONTENIDO -->
        <main class="mx-auto max-w-4xl px-6 py-12 lg:px-8 lg:py-16">

            <div class="rounded-[2rem] bg-[#FFFDF8] p-8 shadow-lg ring-1 ring-[#DDD7CA] sm:p-10 lg:p-14">

                <span class="text-xs font-black uppercase tracking-[0.2em] text-[#9A6A12]">
                    Información legal
                </span>

                <h1 class="mt-3 text-4xl font-black leading-tight text-[#172A3D] sm:text-5xl">
                    Política de privacidad
                </h1>

                <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]"></div>

                <p class="mt-6 text-sm leading-6 text-[#718096]">
                    Última actualización: 26 de agosto de 2026
                </p>

                <div class="mt-10 space-y-9 text-[15px] leading-7 text-[#526174]">

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">1. Responsable del tratamiento</h2>
                        <p class="mt-3">
                            Portal Deseret administra la plataforma y realiza el tratamiento de los datos
                            personales necesarios para prestar sus servicios. Para efectos de esta política,
                            se utilizará la denominación Portal Deseret como referencia al servicio.
                        </p>
                        <p class="mt-3">
                            Antes de publicar esta política como documento definitivo, deberá incorporarse
                            la identificación jurídica completa del responsable del tratamiento, cuando corresponda.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">2. Datos que pueden ser tratados</h2>
                        <p class="mt-3">
                            Dependiendo de las funcionalidades utilizadas, Portal Deseret podrá tratar datos
                            suministrados por el usuario durante el registro, la solicitud de acceso, la creación
                            del perfil, las publicaciones y las comunicaciones relacionadas con la plataforma.
                        </p>
                        <p class="mt-3">
                            Estos pueden incluir datos de identificación y contacto, información profesional,
                            información sobre servicios, productos, cursos o emprendimientos y los datos
                            necesarios para administrar la cuenta.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">3. Finalidades</h2>
                        <p class="mt-3">
                            Los datos personales podrán ser tratados para gestionar registros y solicitudes de
                            acceso, administrar cuentas, habilitar funcionalidades, facilitar conexiones dentro
                            de la comunidad, gestionar publicaciones, prestar soporte y enviar comunicaciones
                            relacionadas con el funcionamiento de Portal Deseret.
                        </p>
                        <p class="mt-3">
                            Cuando corresponda, podrán existir finalidades adicionales que deberán ser informadas
                            al usuario y contar con la autorización requerida por la normativa aplicable.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">4. Autorización</h2>
                        <p class="mt-3">
                            Cuando la legislación aplicable lo exija, Portal Deseret solicitará al titular una
                            autorización previa, expresa e informada para el tratamiento de sus datos personales.
                            La autorización deberá poder ser consultada posteriormente.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">5. Datos publicados por el usuario</h2>
                        <p class="mt-3">
                            Algunas funcionalidades pueden permitir que el usuario decida publicar información
                            para facilitar la conexión con otros miembros de la comunidad. El usuario deberá
                            evitar publicar datos sensibles, información de terceros o información que no esté
                            autorizado para divulgar.
                        </p>
                        <p class="mt-3">
                            La información que el usuario decida hacer visible dentro de la plataforma podrá
                            ser consultada por las personas autorizadas para acceder a dicha funcionalidad,
                            de acuerdo con la configuración y reglas del portal.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">6. Derechos de los titulares</h2>
                        <p class="mt-3">
                            De acuerdo con la normativa colombiana aplicable, los titulares pueden ejercer,
                            entre otros, los derechos de conocer, actualizar y rectificar sus datos personales,
                            solicitar información sobre su tratamiento y, cuando corresponda, solicitar la
                            supresión de los datos o revocar la autorización, salvo las excepciones legales.
                        </p>
                        <p class="mt-3">
                            Estos derechos se ejercen conforme a los procedimientos y términos establecidos
                            por la normativa aplicable.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">7. Seguridad</h2>
                        <p class="mt-3">
                            Portal Deseret adoptará medidas técnicas, humanas y administrativas razonables
                            orientadas a proteger los datos personales frente a pérdida, adulteración,
                            consulta, uso o acceso no autorizado, de acuerdo con la naturaleza de la información
                            y los riesgos asociados.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">8. Encargados y terceros</h2>
                        <p class="mt-3">
                            Para operar determinados componentes tecnológicos, Portal Deseret podrá utilizar
                            proveedores que actúen como encargados del tratamiento o que presten servicios de
                            infraestructura, almacenamiento, comunicaciones, seguridad u otras funciones necesarias.
                        </p>
                        <p class="mt-3">
                            Cuando corresponda, se adoptarán las medidas contractuales y legales necesarias
                            para proteger los datos tratados por terceros.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">9. Conservación</h2>
                        <p class="mt-3">
                            Los datos se conservarán durante el tiempo necesario para cumplir las finalidades
                            informadas, atender obligaciones legales y contractuales, resolver reclamaciones
                            y mantener los registros que resulten necesarios.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">10. Contacto para asuntos de privacidad</h2>
                        <p class="mt-3">
                            Para consultas o solicitudes relacionadas con el tratamiento de datos personales,
                            puedes comunicarte a:
                        </p>

                        <div class="mt-4 rounded-2xl bg-[#F3F0E8] p-5">
                            <p class="font-bold text-[#172A3D]">Correo</p>
                            <a
                                href="mailto:info@portaldesere.com"
                                class="text-[#9A6A12] hover:underline"
                            >
                                info@portaldesere.com
                            </a>

                            <p class="mt-4 font-bold text-[#172A3D]">WhatsApp</p>
                            <a
                                href="https://wa.me/573112819811"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-[#9A6A12] hover:underline"
                            >
                                311 281 9811
                            </a>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">11. Marco normativo</h2>
                        <p class="mt-3">
                            Esta política se formula teniendo como referencia el régimen colombiano de
                            protección de datos personales, en particular la Ley 1581 de 2012 y sus normas
                            reglamentarias y complementarias vigentes.
                        </p>
                    </section>

                    <section class="border-t border-[#E5DCC9] pt-7">
                        <p class="text-sm text-[#718096]">
                            Esta política constituye una base inicial para la implementación del portal.
                            Antes de su publicación definitiva deberá ajustarse a la identificación jurídica
                            del responsable, las bases de datos reales, los formularios utilizados, los
                            proveedores tecnológicos, las transferencias o transmisiones de datos y los
                            procedimientos internos de atención de solicitudes.
                        </p>
                    </section>

                </div>
            </div>
        </main>

        <footer class="bg-[#101D2B] px-6 py-8 text-center text-sm text-white/60">
            © {{ new Date().getFullYear() }} Portal Deseret. Todos los derechos reservados.
        </footer>
    </div>
</template>
