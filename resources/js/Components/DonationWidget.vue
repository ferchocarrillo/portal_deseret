<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const isOpen = ref(false);
const activeTab = ref('wompi'); // 'wompi' | 'qr' | 'bank'

const form = useForm({
    amount: 20000,
    donor_name: '',
    donor_email: '',
    message: '',
    gateway: 'wompi'
});

const selectAmount = (val) => {
    form.amount = val;
};

const submitDonation = () => {
    // 1. Validar que el monto exista y sea un número válido mayor o igual a 1000
    const monto = Number(form.amount);
    if (!monto || isNaN(monto) || monto < 1000) {
        alert('Por favor, selecciona o ingresa un monto válido (Mínimo $1.000 COP)');
        return;
    }

    if (form.gateway === 'wompi') {
        axios.post(route('donations.store'), {
            amount: monto,
            donor_name: form.donor_name,
            donor_email: form.donor_email,
            message: form.message,
            gateway: 'wompi'
        })
            .then(response => {
                const data = response.data;
                const publicKey = page.props.wompiPublicKey || data.public_key;

                if (!publicKey) {
                    alert('Error: La llave pública de Wompi no está configurada.');
                    return;
                }

                // 2. Abrir el Widget oficial de Wompi con la conversión asegurada a centavos
                let checkout = new WidgetCheckout({
                    currency: 'COP',
                    amountInCents: parseInt(monto * 100), // Asegura que siempre se envíe en centavos
                    reference: data.reference,
                    publicKey: publicKey,
                    customerData: {
                        email: form.donor_email || 'soporte@portaldeseret.com',
                        fullName: form.donor_name || 'Anónimo',
                    }
                });

                checkout.open(function (result) {
                    let transaction = result.transaction;
                    if (transaction && transaction.status === 'APPROVED') {
                        alert('¡Mil gracias por tu aporte! Transacción aprobada con éxito 💛');
                        isOpen.value = false;
                        form.reset();
                    } else if (transaction && transaction.status === 'DECLINED') {
                        alert('La transacción fue declinada.');
                    }
                });
            })
            .catch(error => {
                console.error(error);
                alert('Ocurrió un error al procesar la solicitud con Wompi.');
            });
    } else {
        // Para QR o Transferencia bancaria manual
        form.post(route('donations.store'), {
            onSuccess: () => {
                alert('¡Información registrada con éxito! Gracias por apoyar el portal.');
                isOpen.value = false;
                form.reset();
            }
        });
    }
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert('¡Número copiado al portapapeles!');
};
</script>

<template>
    <div>
        <!-- ===================================================== -->
        <!-- BOTÓN FLOTANTE ESTILIZADO                             -->
        <!-- ===================================================== -->
        <button @click="isOpen = true"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-[#172A3D] hover:bg-[#203852] text-[#FFFDF9] px-5 py-3.5 rounded-full shadow-xl transition-all duration-300 hover:scale-105 border border-amber-400/40 font-medium group">
            <div
                class="w-7 h-7 rounded-full bg-amber-400 flex items-center justify-center shadow-inner overflow-hidden">
                <img src="/images/landing/logo.png" alt="Portal Deseret" class="w-full h-full object-cover" />
            </div>
            <span class="tracking-wide text-sm font-semibold">Apoya el Portal</span>
            <span class="text-amber-400 group-hover:scale-125 transition-transform">💛</span>
        </button>

        <!-- ===================================================== -->
        <!-- MODAL DE DONACIONES                                   -->
        <!-- ===================================================== -->
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
            <div
                class="bg-[#FFFDF9] text-[#172A3D] rounded-2xl max-w-lg w-full p-6 shadow-2xl relative border border-amber-100 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800">

                <!-- Botón Cerrar -->
                <button @click="isOpen = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-[#172A3D] dark:hover:text-white text-lg font-bold w-8 h-8 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-800 transition">
                    ✕
                </button>

                <!-- Cabecera con Logo Centrado -->
                <div class="text-center mb-5 pt-2">
                    <div class="flex justify-center mb-3">
                        <img src="/images/landing/logo.png" alt="Portal Deseret Logo"
                            class="h-16 w-auto object-contain drop-shadow-sm" />
                    </div>
                    <h3 class="text-lg font-bold text-[#172A3D] dark:text-white flex items-center justify-center gap-2">
                        Apoya Portal Deseret
                        <span class="text-amber-500 text-sm">✨</span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                        Proyecto comunitario y gratuito operado como persona natural.
                    </p>
                </div>

                <!-- Pestañas de Navegación -->
                <div
                    class="grid grid-cols-3 gap-1 bg-amber-50/60 dark:bg-gray-800 p-1 rounded-xl mb-5 border border-amber-100 dark:border-gray-700">
                    <button @click="activeTab = 'wompi'; form.gateway = 'wompi'"
                        :class="['py-2 text-xs font-semibold rounded-lg transition-all', activeTab === 'wompi' ? 'bg-[#172A3D] text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:text-[#172A3D]']">
                        💳 Wompi / Online
                    </button>
                    <button @click="activeTab = 'qr'; form.gateway = 'qr'"
                        :class="['py-2 text-xs font-semibold rounded-lg transition-all', activeTab === 'qr' ? 'bg-[#172A3D] text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:text-[#172A3D]']">
                        📱 Billeteras QR
                    </button>
                    <button @click="activeTab = 'bank'; form.gateway = 'bank'"
                        :class="['py-2 text-xs font-semibold rounded-lg transition-all', activeTab === 'bank' ? 'bg-[#172A3D] text-white shadow' : 'text-gray-600 dark:text-gray-300 hover:text-[#172A3D]']">
                        🏦 Transferencia
                    </button>
                </div>

                <!-- Pestaña 1: Wompi -->
                <div v-if="activeTab === 'wompi'" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Selecciona o escribe el monto de tu aporte (COP)
                        </label>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <button type="button" @click="selectAmount(10000)"
                                :class="['py-2 rounded-xl text-xs font-bold border transition', form.amount === 10000 ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-amber-400']">$10.000</button>
                            <button type="button" @click="selectAmount(25000)"
                                :class="['py-2 rounded-xl text-xs font-bold border transition', form.amount === 25000 ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-amber-400']">$25.000</button>
                            <button type="button" @click="selectAmount(50000)"
                                :class="['py-2 rounded-xl text-xs font-bold border transition', form.amount === 50000 ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-amber-400']">$50.000</button>
                        </div>
                        <input v-model.number="form.amount" type="number" placeholder="Otro monto personalizado"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-amber-500 focus:ring-amber-500" />
                    </div>

                    <div class="space-y-2">
                        <input v-model="form.donor_name" type="text" placeholder="Tu nombre (Opcional / Anónimo)"
                            class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-amber-500 focus:ring-amber-500" />
                        <input v-model="form.donor_email" type="email" placeholder="Correo electrónico (Opcional)"
                            class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-amber-500 focus:ring-amber-500" />
                        <textarea v-model="form.message"
                            placeholder="Déjanos un mensaje de aliento para la comunidad (Opcional)" rows="2"
                            class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-amber-500 focus:ring-amber-500"></textarea>
                    </div>

                    <button @click="submitDonation"
                        class="w-full bg-[#172A3D] hover:bg-[#203852] text-amber-400 font-bold py-3 rounded-xl transition shadow-md text-sm flex items-center justify-center gap-2 border border-amber-400/30">
                        <span>Pagar Seguro con Wompi</span>
                        <span>🚀</span>
                    </button>
                </div>

                <!-- Pestaña 2: Códigos QR (Nequi / Bre-b) -->
                <div v-if="activeTab === 'qr'" class="space-y-4 text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">Escanea el código QR desde tu app o copia el
                        número:</p>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Nequi -->
                        <div
                            class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-amber-100 dark:border-gray-700 shadow-sm flex flex-col items-center">
                            <span class="font-bold text-xs text-pink-600 mb-1">Nequi</span>
                            <div
                                class="w-28 h-28 bg-gray-50 rounded-lg p-1 border border-gray-200 flex items-center justify-center overflow-hidden my-1">
                                <img src="/images/landing/qr-nequi.png" alt="QR Nequi"
                                    class="w-full h-full object-contain" />
                            </div>
                            <button @click="copyToClipboard('3187200092')"
                                class="w-full mt-1 text-[11px] bg-pink-50 text-pink-700 py-1.5 rounded-lg font-semibold hover:bg-pink-100 transition">
                                Copiar 318 7200092
                            </button>
                        </div>

                        <!-- Bre-b -->
                        <div
                            class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-amber-100 dark:border-gray-700 shadow-sm flex flex-col items-center">
                            <span class="font-bold text-xs text-red-600 mb-1">Bre-b</span>
                            <div
                                class="w-28 h-28 bg-gray-50 rounded-lg p-1 border border-gray-200 flex items-center justify-center overflow-hidden my-1">
                                <img src="/images/landing/qr-bre-b.png" alt="QR Bre-b"
                                    class="w-full h-full object-contain" />
                            </div>
                            <button @click="copyToClipboard('3112819811')"
                                class="w-full mt-1 text-[11px] bg-red-50 text-red-700 py-1.5 rounded-lg font-semibold hover:bg-red-100 transition">
                                Copiar 311 2819811
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pestaña 3: Cuentas Bancarias (Davivienda) -->
                <div v-if="activeTab === 'bank'" class="space-y-3 text-sm">
                    <div class="p-5 bg-white text-gray-900 rounded-2xl border border-gray-200 shadow-md text-center">
                        <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
                            <div class="flex items-center gap-2">
                                <img src="/images/landing/davivienda.png" alt="Davivienda"
                                    class="h-7 w-auto object-contain" />
                                <span class="font-bold text-xs text-gray-800 uppercase tracking-wider">Davivienda
                                    (Ahorros)</span>
                            </div>
                            <span
                                class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-semibold">Principal</span>
                        </div>

                        <div class="my-3 py-2 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 font-medium mb-0.5">Número de Cuenta</p>
                            <p class="text-gray-900 font-mono text-2xl font-extrabold tracking-wider">
                                4884 4140 9684
                            </p>
                        </div>

                        <div class="text-left space-y-1 mb-2 px-1">
                            <p class="text-gray-700 text-xs font-semibold">Titular: <span
                                    class="font-normal text-gray-600">Fernando Alexander Carrillo Leon</span></p>
                            <p class="text-gray-700 text-xs font-semibold">C.C.: <span
                                    class="font-normal text-gray-600">79.760.755</span></p>
                        </div>

                        <button @click="copyToClipboard('488441409684')"
                            class="mt-2 w-full text-sm bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl font-bold transition shadow-sm flex items-center justify-center gap-1.5">
                            <span>📋 Copiar Número de Cuenta</span>
                        </button>
                    </div>
                </div>

                <!-- Pie de Modal Informativo -->
                <div class="mt-5 pt-3 border-t border-amber-100 dark:border-gray-800 text-center">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        Gracias por mantener vivo este espacio de comunidad. 💛
                    </p>
                </div>

            </div>
        </div>
    </div>
</template>