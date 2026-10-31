import fs from 'fs';
import path from 'path';

const carpetaSalida = path.join(process.cwd(), 'public', 'images', 'landing');

if (!fs.existsSync(carpetaSalida)) {
  fs.mkdirSync(carpetaSalida, { recursive: true });
}

function crearSvg({ ancho = 800, alto = 500, colorFondo1, colorFondo2, icono, texto, detalle }) {
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${ancho} ${alto}" width="100%" height="100%">
  <defs>
    <linearGradient id="gradiente" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="${colorFondo1}" />
      <stop offset="100%" stop-color="${colorFondo2}" />
    </linearGradient>
    <pattern id="rejilla" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
    </pattern>
  </defs>

  <rect width="100%" height="100%" fill="url(#gradiente)" />
  <rect width="100%" height="100%" fill="url(#rejilla)" />

  <circle cx="${ancho * 0.85}" cy="${alto * 0.2}" r="140" fill="rgba(255,255,255,0.06)" />
  <circle cx="${ancho * 0.15}" cy="${alto * 0.85}" r="110" fill="rgba(0,0,0,0.12)" />

  <g transform="translate(${ancho / 2}, ${alto / 2 - 30})">
    <circle cx="0" cy="0" r="48" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.25)" stroke-width="2"/>
    <text x="0" y="16" font-size="44" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">${icono}</text>
  </g>

  <text x="${ancho / 2}" y="${alto / 2 + 55}" font-size="24" font-weight="800" fill="#ffffff" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" letter-spacing="0.5">
    ${texto}
  </text>
  <text x="${ancho / 2}" y="${alto / 2 + 85}" font-size="13" font-weight="500" fill="rgba(255,255,255,0.75)" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif">
    ${detalle}
  </text>
</svg>`;
}

const imagenes = [
  // Banners Hero
  {
    archivo: 'banner1.svg',
    ancho: 1200,
    alto: 450,
    colorFondo1: '#172A3D',
    colorFondo2: '#2F526D',
    icono: '🐝',
    texto: 'Portal Deseret',
    detalle: 'Comunidad, servicios y nuevas ideas en un solo lugar',
  },
  {
    archivo: 'banner2.svg',
    ancho: 1200,
    alto: 450,
    colorFondo1: '#1B3B5A',
    colorFondo2: '#2F80ED',
    icono: '💼',
    texto: 'Oportunidades Laborales',
    detalle: 'Conectando talentos con proyectos locales',
  },
  {
    archivo: 'banner3.svg',
    ancho: 1200,
    alto: 450,
    colorFondo1: '#7A3E20',
    colorFondo2: '#D99A24',
    icono: '🚀',
    texto: 'Emprendimientos y Crecimiento',
    detalle: 'Impulsando los negocios de nuestra comunidad',
  },

  // Servicios
  {
    archivo: 'servicios01.svg',
    colorFondo1: '#1E4E45',
    colorFondo2: '#3B7A70',
    icono: '🛠️',
    texto: 'Servicios Profesionales',
    detalle: 'Mano de obra, asesorías y soluciones técnicas',
  },
  {
    archivo: 'servicios02.svg',
    colorFondo1: '#13352E',
    colorFondo2: '#235D52',
    icono: '🔧',
    texto: 'Mantenimiento y Oficios',
    detalle: 'Soluciones directas para el hogar y negocio',
  },

  // Requerimientos (Solicitudes)
  {
    archivo: 'requerimientos01.svg',
    colorFondo1: '#9A5B00',
    colorFondo2: '#D97706',
    icono: '⚡',
    texto: 'Solicitud Abierta',
    detalle: 'Miembro de la comunidad requiere asistencia',
  },
  {
    archivo: 'requerimientos02.svg',
    colorFondo1: '#B45309',
    colorFondo2: '#F59E0B',
    icono: '📢',
    texto: 'Urgencia Comunitaria',
    detalle: 'Petición activa con respuesta prioritaria',
  },

  // Empleos
  {
    archivo: 'empleos01.svg',
    colorFondo1: '#1E40AF',
    colorFondo2: '#3B82F6',
    icono: '💼',
    texto: 'Vacante Disponible',
    detalle: 'Postúlate a nuevas ofertas laborales',
  },
  {
    archivo: 'empleos02.svg',
    colorFondo1: '#1E3A8A',
    colorFondo2: '#2563EB',
    icono: '📋',
    texto: 'Convocatoria Laboral',
    detalle: 'Proyectos y empresas de la colmena',
  },

  // Marketplace
  {
    archivo: 'marketplace01.svg',
    colorFondo1: '#92400E',
    colorFondo2: '#D99A24',
    icono: '🛍️',
    texto: 'Catálogo de Productos',
    detalle: 'Compra y venta entre miembros',
  },
  {
    archivo: 'marketplace02.svg',
    colorFondo1: '#78350F',
    colorFondo2: '#B45309',
    icono: '🏷️',
    texto: 'Artículos Destacados',
    detalle: 'Ofertas disponibles en la comunidad',
  },

  // Cursos, Emprendimientos y Clasificados
  {
    archivo: 'cursos01.svg',
    colorFondo1: '#334155',
    colorFondo2: '#496B86',
    icono: '📚',
    texto: 'Aprende y Desarrolla Habilidades',
    detalle: 'Talleres, cursos y formación comunitaria',
  },
  {
    archivo: 'emprendimientos01.svg',
    colorFondo1: '#7C2D12',
    colorFondo2: '#A65A45',
    icono: '🚀',
    texto: 'Ideas y Negocios',
    detalle: 'Proyectos con visión comunitaria',
  },
  {
    archivo: 'clasificados01.svg',
    colorFondo1: '#9A3412',
    colorFondo2: '#C96A32',
    icono: '📣',
    texto: 'Avisos Comunitarios',
    detalle: 'Publicaciones y anuncios generales',
  },
];

imagenes.forEach((config) => {
  const rutaDestino = path.join(carpetaSalida, config.archivo);
  const contenidoSvg = crearSvg(config);
  fs.writeFileSync(rutaDestino, contenidoSvg, 'utf-8');
  console.log(`Generado: ${config.archivo}`);
});

console.log('\nTodas las imágenes se crearon en public/images/landing/');