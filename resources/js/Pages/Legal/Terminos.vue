<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-[#F3F0E8] text-[#172A3D]">

        <!-- HEADER -->
        <header class="border-b border-[#DDD7CA] bg-[#FFFDF8]">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8">
                <Link href="/" class="flex items-center">
                    <img src="/images/landing/logo.png" alt="Portal Deseret" class="h-auto w-[190px]" />
                </Link>

                <Link href="/"
                    class="rounded-xl px-4 py-2 text-sm font-bold text-[#526174] transition hover:bg-[#F3F0E8] hover:text-[#172A3D]">
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
                    Términos y condiciones
                </h1>

                <div class="mt-5 h-1 w-12 rounded-full bg-[#D99A24]"></div>

                <p class="mt-6 text-sm leading-6 text-[#718096]">
                    Última actualización: 26 de agosto de 2026
                </p>

                <div class="mt-10 space-y-9 text-[15px] leading-7 text-[#526174]">

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">1. Aceptación</h2>
                        <p class="mt-3">
                            Estos términos regulan el acceso y uso de Portal Deseret, una plataforma
                            orientada a facilitar la conexión entre personas de la comunidad, sus
                            conocimientos, servicios, oportunidades, productos, cursos y emprendimientos.
                            Al registrarse, solicitar acceso o utilizar las funcionalidades disponibles,
                            el usuario manifiesta que ha leído y acepta estos términos.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">2. Registro y solicitud de acceso</h2>
                        <p class="mt-3">
                            Para utilizar las funcionalidades reservadas a usuarios registrados, la persona
                            deberá suministrar información verdadera, completa y actualizada. El registro
                            constituye una solicitud de acceso y no implica aprobación automática.
                        </p>
                        <p class="mt-3">
                            Portal Deseret podrá revisar las solicitudes y aprobarlas o rechazarlas de acuerdo
                            con sus criterios de acceso y las reglas de la comunidad.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">3. Cuenta y responsabilidad del usuario</h2>
                        <p class="mt-3">
                            Cada usuario es responsable de la información que proporciona, de mantener la
                            confidencialidad de sus credenciales y de las actividades realizadas desde su cuenta.
                            No está permitido utilizar una identidad ajena, suministrar información falsa,
                            compartir credenciales o intentar acceder sin autorización a cuentas, datos o
                            funcionalidades de terceros.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">4. Uso de la plataforma</h2>
                        <p class="mt-3">
                            Portal Deseret podrá utilizarse para explorar información de la comunidad,
                            encontrar servicios, oportunidades laborales, productos, cursos y emprendimientos,
                            establecer conexiones y publicar contenidos permitidos por la plataforma.
                        </p>
                        <p class="mt-3">
                            El usuario se compromete a utilizar la plataforma de manera lícita, respetuosa y
                            coherente con su finalidad. No podrá emplearla para actividades fraudulentas,
                            ilícitas, engañosas, abusivas, discriminatorias, invasivas de la privacidad o que
                            puedan afectar la seguridad de otras personas.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">5. Publicaciones y contenidos</h2>
                        <p class="mt-3">
                            Los usuarios pueden publicar información sobre sus servicios, productos,
                            conocimientos, cursos o emprendimientos cuando la funcionalidad correspondiente
                            esté disponible.
                        </p>
                        <p class="mt-3">
                            El usuario es responsable de que sus publicaciones sean veraces, lícitas y no
                            infrinjan derechos de terceros. Portal Deseret podrá retirar, limitar o modificar
                            la visibilidad de contenidos que incumplan estos términos o las reglas de la comunidad.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">6. Relaciones entre usuarios</h2>
                        <p class="mt-3">
                            Portal Deseret facilita la conexión entre miembros, pero no constituye por sí mismo
                            una parte en las relaciones, contratos, compras, ventas, servicios, empleos,
                            colaboraciones o acuerdos que puedan surgir entre los usuarios.
                        </p>
                        <p class="mt-3">
                            Cada usuario deberá verificar directamente la información que considere necesaria
                            antes de contratar, comprar, vender, prestar servicios, aceptar una oferta laboral
                            o establecer cualquier otro acuerdo con otra persona.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">7. Propiedad intelectual</h2>
                        <p class="mt-3">
                            Los elementos propios de Portal Deseret, incluyendo su identidad visual, diseño,
                            textos, código y componentes, estarán protegidos por las normas aplicables de
                            propiedad intelectual. El contenido aportado por los usuarios seguirá perteneciendo
                            a sus respectivos titulares, sin perjuicio de las autorizaciones necesarias para
                            mostrarlo y operar la plataforma.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">8. Disponibilidad y cambios</h2>
                        <p class="mt-3">
                            Portal Deseret podrá actualizar, modificar, suspender o retirar funcionalidades
                            de la plataforma cuando resulte necesario para su operación, mantenimiento,
                            seguridad o evolución. También podrá actualizar estos términos, informando la
                            versión vigente mediante la publicación correspondiente.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">9. Suspensión o terminación del acceso</h2>
                        <p class="mt-3">
                            El acceso podrá ser limitado, suspendido o terminado cuando existan incumplimientos
                            de estos términos, uso indebido de la plataforma, riesgos de seguridad o razones
                            relacionadas con la protección de la comunidad y el funcionamiento del servicio.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-black text-[#172A3D]">10. Contacto</h2>
                        <p class="mt-3">
                            Para consultas relacionadas con estos términos, puedes comunicarte a:
                        </p>
                        <div class="mt-4 rounded-2xl bg-[#F3F0E8] p-5">
                            <p class="font-bold text-[#172A3D]">Correo</p>
                            <a href="mailto:info@portaldeseret.com" class="text-[#9A6A12] hover:underline">
                                info@portaldeseret.com
                            </a>

                            <p class="mt-4 font-bold text-[#172A3D]">WhatsApp</p>
                            <a href="https://wa.me/573112819811" target="_blank" rel="noopener noreferrer"
                                class="text-[#9A6A12] hover:underline">
                                311 281 9811
                            </a>
                        </div>
                    </section>

                    <section class="border-t border-[#E5DCC9] pt-7">
                        <p class="text-sm text-[#718096]">
                            Estos términos deben revisarse y ajustarse a la estructura jurídica,
                            modelo operativo y políticas definitivas de Portal Deseret antes de su
                            publicación como documento legal definitivo.
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
