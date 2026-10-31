<script setup>
import { onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Corregir problema de iconos de Leaflet en Vite/Webpack
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

const props = defineProps({
    latitud: {
        type: [Number, String],
        default: null,
    },
    longitud: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(['update:latitud', 'update:longitud']);

const contenedorMapa = ref(null);
let mapa = null;
let marcador = null;

// Centro por defecto (Bogotá) si no hay coordenadas previas
const LAT_DEFAULT = 4.6728;
const LNG_DEFAULT = -74.1234;

onMounted(() => {
    const latInicial = props.latitud ? Number(props.latitud) : LAT_DEFAULT;
    const lngInicial = props.longitud ? Number(props.longitud) : LNG_DEFAULT;

    mapa = L.map(contenedorMapa.value).setView([latInicial, lngInicial], props.latitud ? 16 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(mapa);

    marcador = L.marker([latInicial, lngInicial], {
        draggable: true,
    }).addTo(mapa);

    // Al arrastrar el pin
    marcador.on('dragend', () => {
        const posicion = marcador.getLatLng();
        emitirCoordenadas(posicion.lat, posicion.lng);
    });

    // Al hacer clic en cualquier parte del mapa
    mapa.on('click', (evento) => {
        marcador.setLatLng(evento.latlng);
        emitirCoordenadas(evento.latlng.lat, evento.latlng.lng);
    });

    // Forzar renderizado de tiles al abrir en un modal
    setTimeout(() => {
        mapa.invalidateSize();
    }, 200);
});

const emitirCoordenadas = (lat, lng) => {
    emit('update:latitud', Number(lat.toFixed(7)));
    emit('update:longitud', Number(lng.toFixed(7)));
};

// Escuchar cambios externos (por ejemplo, si presiona "Usar mi GPS actual")
watch(
    () => [props.latitud, props.longitud],
    ([nuevaLat, nuevaLng]) => {
        if (nuevaLat && nuevaLng && mapa && marcador) {
            const coords = [Number(nuevaLat), Number(nuevaLng)];
            marcador.setLatLng(coords);
            mapa.setView(coords, 16);
        }
    }
);
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between text-xs text-slate-600">
            <span class="font-bold text-[#172A3D]">Mueve el marcador o haz clic en tu ubicación exacta:</span>
            <span class="text-[11px] text-[#846000]">📍 Arrastrable</span>
        </div>
        <div ref="contenedorMapa" class="h-64 w-full rounded-xl border border-slate-300 shadow-inner z-0"></div>
    </div>
</template>

<style scoped>
/* Asegurar z-index inferior al header/modales */
:deep(.leaflet-pane) {
    z-index: 10 !important;
}

:deep(.leaflet-top),
:deep(.leaflet-bottom) {
    z-index: 11 !important;
}
</style>