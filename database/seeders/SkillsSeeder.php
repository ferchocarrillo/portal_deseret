<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SkillSubcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillsSeeder extends Seeder
{
    /**
     * Banco Maestro de Skills - Portal Deseret.
     *
     * Estructura:
     *
     * Categoría
     *     └── Subcategoría
     *             └── Skills
     *
     * El Seeder puede ejecutarse nuevamente sin duplicar
     * categorías, subcategorías ni skills.
     */
    public function run(): void
    {
        $catalogo = [

            // ==========================================================
            // 01. TECNOLOGÍA
            // ==========================================================

            'Tecnología' => [

                'Programación y desarrollo' => [
                    'Programación',
                    'Desarrollo de software',
                    'Desarrollo web',
                    'Desarrollo frontend',
                    'Desarrollo backend',
                    'Desarrollo full stack',
                    'Desarrollo de aplicaciones móviles',
                    'Desarrollo de aplicaciones de escritorio',
                    'Desarrollo de APIs',
                    'Desarrollo de microservicios',
                    'Programación orientada a objetos',
                    'Programación funcional',
                    'PHP',
                    'Laravel',
                    'Symfony',
                    'JavaScript',
                    'TypeScript',
                    'Vue.js',
                    'React',
                    'Angular',
                    'Node.js',
                    'Python',
                    'Django',
                    'Flask',
                    'Java',
                    'Spring Boot',
                    'C#',
                    '.NET',
                    'C++',
                    'C',
                    'Go',
                    'Rust',
                    'Ruby',
                    'Ruby on Rails',
                    'Kotlin',
                    'Swift',
                    'Dart',
                    'Flutter',
                    'React Native',
                    'WordPress',
                    'Desarrollo de plugins',
                    'Desarrollo de temas',
                ],

                'Bases de datos' => [
                    'Diseño de bases de datos',
                    'Modelado de datos',
                    'Administración de bases de datos',
                    'SQL',
                    'MySQL',
                    'MariaDB',
                    'PostgreSQL',
                    'SQL Server',
                    'Oracle Database',
                    'SQLite',
                    'MongoDB',
                    'Redis',
                    'Optimización de consultas',
                    'Migración de bases de datos',
                    'Respaldos de bases de datos',
                    'Integración de bases de datos',
                ],

                'Sistemas y servidores' => [
                    'Administración de sistemas',
                    'Administración de servidores',
                    'Linux',
                    'Windows Server',
                    'Ubuntu Server',
                    'Configuración de servidores',
                    'Instalación de servidores',
                    'Mantenimiento de servidores',
                    'Virtualización',
                    'VMware',
                    'VirtualBox',
                    'Hyper-V',
                    'Docker',
                    'Kubernetes',
                    'Git',
                    'GitHub',
                    'GitLab',
                    'Bitbucket',
                ],

                'Computación en la nube' => [
                    'Computación en la nube',
                    'Amazon Web Services',
                    'AWS',
                    'Microsoft Azure',
                    'Google Cloud',
                    'Cloudflare',
                    'Cloud Hosting',
                    'Administración de servicios cloud',
                    'Migración a la nube',
                    'Arquitectura cloud',
                    'Almacenamiento cloud',
                    'Seguridad cloud',
                ],

                'Redes y conectividad' => [
                    'Redes informáticas',
                    'Instalación de redes',
                    'Configuración de redes',
                    'Administración de redes',
                    'Cableado estructurado',
                    'Configuración de routers',
                    'Configuración de switches',
                    'Wi-Fi',
                    'Redes inalámbricas',
                    'VPN',
                    'TCP/IP',
                    'DNS',
                    'DHCP',
                    'Firewall',
                    'Diagnóstico de redes',
                ],

                'Soporte técnico' => [
                    'Soporte técnico',
                    'Mantenimiento de computadores',
                    'Mantenimiento de portátiles',
                    'Formateo de computadores',
                    'Instalación de sistemas operativos',
                    'Instalación de software',
                    'Configuración de software',
                    'Configuración de impresoras',
                    'Reparación de computadores',
                    'Reparación de portátiles',
                    'Recuperación de información',
                    'Respaldo de información',
                    'Diagnóstico de fallas informáticas',
                    'Soporte remoto',
                    'Asistencia informática',
                ],

                'Datos e inteligencia artificial' => [
                    'Análisis de datos',
                    'Procesamiento de datos',
                    'Limpieza de datos',
                    'Visualización de datos',
                    'Ciencia de datos',
                    'Estadística aplicada',
                    'Power BI',
                    'Tableau',
                    'Excel avanzado',
                    'Machine Learning',
                    'Deep Learning',
                    'Inteligencia artificial',
                    'IA generativa',
                    'Procesamiento de lenguaje natural',
                    'Visión artificial',
                    'Automatización con IA',
                    'Prompt engineering',
                    'Modelos predictivos',
                ],

                'Ciberseguridad' => [
                    'Ciberseguridad',
                    'Seguridad informática',
                    'Seguridad de redes',
                    'Seguridad de sistemas',
                    'Auditoría de seguridad',
                    'Gestión de vulnerabilidades',
                    'Protección de datos',
                    'Control de accesos',
                    'Gestión de contraseñas',
                    'Seguridad web',
                    'Seguridad de aplicaciones',
                    'Copias de seguridad',
                    'Respuesta ante incidentes',
                ],

                'Automatización y productividad digital' => [
                    'Automatización de procesos',
                    'Automatización de tareas',
                    'Integración de sistemas',
                    'Integración de APIs',
                    'Automatización con Python',
                    'Automatización con JavaScript',
                    'Zapier',
                    'Make',
                    'Power Automate',
                    'RPA',
                    'Macros de Excel',
                    'Google Apps Script',
                    'Optimización de procesos digitales',
                ],
            ],

            // ==========================================================
            // 02. DISEÑO Y CREATIVIDAD
            // ==========================================================

            'Diseño y creatividad' => [

                'Diseño gráfico' => [
                    'Diseño gráfico',
                    'Diseño de logotipos',
                    'Diseño de identidad visual',
                    'Diseño corporativo',
                    'Diseño editorial',
                    'Diseño publicitario',
                    'Diseño de folletos',
                    'Diseño de volantes',
                    'Diseño de catálogos',
                    'Diseño de presentaciones',
                    'Diseño de infografías',
                    'Diseño de piezas para redes sociales',
                    'Diseño de empaques',
                    'Diseño de etiquetas',
                    'Ilustración digital',
                ],

                'Diseño UI y UX' => [
                    'Diseño UI',
                    'Diseño UX',
                    'Diseño de interfaces',
                    'Diseño de experiencia de usuario',
                    'Prototipado',
                    'Wireframing',
                    'Diseño de aplicaciones',
                    'Diseño de sitios web',
                    'Arquitectura de información',
                    'Investigación de usuarios',
                    'Pruebas de usabilidad',
                    'Figma',
                    'Adobe XD',
                    'Sketch',
                ],

                'Ilustración y artes visuales' => [
                    'Ilustración',
                    'Dibujo',
                    'Dibujo artístico',
                    'Dibujo técnico',
                    'Pintura',
                    'Acuarela',
                    'Pintura al óleo',
                    'Pintura acrílica',
                    'Caricatura',
                    'Arte digital',
                    'Escultura',
                    'Grabado',
                    'Caligrafía',
                    'Lettering',
                    'Muralismo',
                ],

                'Manualidades y artesanía' => [
                    'Manualidades',
                    'Artesanía',
                    'Bisutería',
                    'Joyería artesanal',
                    'Trabajo con madera',
                    'Trabajo con cuero',
                    'Trabajo con arcilla',
                    'Cerámica artesanal',
                    'Macramé',
                    'Tejido',
                    'Crochet',
                    'Origami',
                    'Decoración artesanal',
                    'Elaboración de velas',
                    'Elaboración de jabones artesanales',
                ],
            ],

            // ==========================================================
            // 03. FOTOGRAFÍA Y AUDIOVISUAL
            // ==========================================================

            'Fotografía y audiovisual' => [

                'Fotografía' => [
                    'Fotografía',
                    'Fotografía de retrato',
                    'Fotografía de producto',
                    'Fotografía de eventos',
                    'Fotografía empresarial',
                    'Fotografía gastronómica',
                    'Fotografía inmobiliaria',
                    'Fotografía de moda',
                    'Fotografía deportiva',
                    'Fotografía documental',
                    'Fotografía artística',
                    'Fotografía de naturaleza',
                    'Fotografía con drones',
                    'Edición fotográfica',
                    'Retoque fotográfico',
                    'Revelado digital',
                ],

                'Producción audiovisual' => [
                    'Producción audiovisual',
                    'Grabación de video',
                    'Dirección audiovisual',
                    'Producción de cortometrajes',
                    'Producción de documentales',
                    'Producción de contenido digital',
                    'Producción de videos corporativos',
                    'Producción de videos para redes sociales',
                    'Edición de video',
                    'Montaje audiovisual',
                    'Corrección de color',
                    'Motion graphics',
                    'Animación 2D',
                    'Animación 3D',
                    'Creación de reels',
                    'Creación de contenido audiovisual',
                ],

                'Audio y producción sonora' => [
                    'Grabación de audio',
                    'Edición de audio',
                    'Mezcla de audio',
                    'Masterización',
                    'Producción musical',
                    'Diseño sonoro',
                    'Podcast',
                    'Edición de podcasts',
                    'Locución',
                    'Narración',
                    'Doblaje',
                    'Sonido para eventos',
                ],
            ],

            // ==========================================================
            // 04. COMUNICACIÓN Y REDACCIÓN
            // ==========================================================

            'Comunicación y redacción' => [

                'Redacción y escritura' => [
                    'Redacción',
                    'Redacción empresarial',
                    'Redacción académica',
                    'Redacción técnica',
                    'Redacción creativa',
                    'Redacción publicitaria',
                    'Copywriting',
                    'Creación de contenidos',
                    'Redacción para blogs',
                    'Redacción para redes sociales',
                    'Creación de guiones',
                    'Edición de textos',
                    'Corrección de textos',
                    'Corrección ortográfica',
                    'Corrección gramatical',
                ],

                'Comunicación' => [
                    'Comunicación oral',
                    'Comunicación escrita',
                    'Comunicación empresarial',
                    'Comunicación digital',
                    'Comunicación institucional',
                    'Oratoria',
                    'Presentaciones',
                    'Conferencias',
                    'Moderación de eventos',
                    'Locución',
                    'Narración',
                    'Entrevistas',
                ],

                'Periodismo y contenidos' => [
                    'Periodismo',
                    'Periodismo digital',
                    'Redacción de noticias',
                    'Investigación periodística',
                    'Entrevistas periodísticas',
                    'Crónica',
                    'Reportaje',
                    'Creación de boletines',
                    'Gestión de contenidos',
                    'Curación de contenidos',
                ],
            ],

            // ==========================================================
            // 05. EDUCACIÓN Y ENSEÑANZA
            // ==========================================================

            'Educación y enseñanza' => [

                'Docencia' => [
                    'Docencia',
                    'Enseñanza',
                    'Tutorías',
                    'Clases particulares',
                    'Capacitación',
                    'Formación empresarial',
                    'Educación virtual',
                    'Educación presencial',
                    'Diseño de cursos',
                    'Diseño de material educativo',
                    'Diseño curricular',
                    'Evaluación educativa',
                ],

                'Ciencias exactas' => [
                    'Matemáticas',
                    'Álgebra',
                    'Geometría',
                    'Cálculo',
                    'Estadística',
                    'Física',
                    'Química',
                    'Biología',
                    'Informática',
                    'Programación educativa',
                ],

                'Humanidades y ciencias sociales' => [
                    'Historia',
                    'Geografía',
                    'Filosofía',
                    'Literatura',
                    'Lengua castellana',
                    'Ciencias sociales',
                    'Economía',
                    'Sociología',
                    'Cultura general',
                ],

                'Educación infantil y apoyo escolar' => [
                    'Educación infantil',
                    'Apoyo escolar',
                    'Refuerzo escolar',
                    'Lectoescritura',
                    'Estimulación educativa',
                    'Técnicas de estudio',
                    'Preparación para exámenes',
                    'Orientación académica',
                ],
            ],

            // ==========================================================
            // 06. IDIOMAS
            // ==========================================================

            'Idiomas' => [

                'Idiomas extranjeros' => [
                    'Inglés',
                    'Francés',
                    'Portugués',
                    'Italiano',
                    'Alemán',
                    'Mandarín',
                    'Chino',
                    'Japonés',
                    'Coreano',
                    'Ruso',
                    'Árabe',
                ],

                'Español' => [
                    'Español',
                    'Español para extranjeros',
                    'Gramática española',
                    'Ortografía española',
                    'Redacción en español',
                    'Comprensión lectora',
                    'Expresión oral',
                ],

                'Servicios lingüísticos' => [
                    'Traducción',
                    'Traducción técnica',
                    'Traducción jurídica',
                    'Traducción comercial',
                    'Traducción audiovisual',
                    'Interpretación',
                    'Interpretación consecutiva',
                    'Corrección de traducciones',
                    'Transcripción',
                    'Subtitulación',
                    'Enseñanza de idiomas',
                ],
            ],

            // ==========================================================
            // 07. NEGOCIOS Y EMPRENDIMIENTO
            // ==========================================================

            'Negocios y emprendimiento' => [

                'Emprendimiento' => [
                    'Emprendimiento',
                    'Creación de empresas',
                    'Creación de negocios',
                    'Desarrollo de ideas de negocio',
                    'Validación de ideas',
                    'Modelos de negocio',
                    'Plan de negocios',
                    'Planeación empresarial',
                    'Innovación',
                    'Innovación empresarial',
                    'Franquicias',
                    'Negocios digitales',
                ],

                'Consultoría empresarial' => [
                    'Consultoría empresarial',
                    'Consultoría estratégica',
                    'Consultoría comercial',
                    'Consultoría administrativa',
                    'Consultoría de procesos',
                    'Análisis de negocios',
                    'Planeación estratégica',
                    'Gestión empresarial',
                    'Optimización de procesos',
                    'Transformación digital',
                ],

                'Gestión de proyectos' => [
                    'Gestión de proyectos',
                    'Dirección de proyectos',
                    'Planeación de proyectos',
                    'Seguimiento de proyectos',
                    'Gestión de riesgos',
                    'Gestión de recursos',
                    'Gestión de cronogramas',
                    'Metodologías ágiles',
                    'Scrum',
                    'Kanban',
                ],
            ],

            // ==========================================================
            // 08. ADMINISTRACIÓN
            // ==========================================================

            'Administración' => [

                'Gestión administrativa' => [
                    'Administración',
                    'Gestión administrativa',
                    'Organización administrativa',
                    'Gestión documental',
                    'Gestión de archivos',
                    'Archivo documental',
                    'Digitación',
                    'Elaboración de informes',
                    'Elaboración de documentos',
                    'Manejo de información',
                    'Gestión de agendas',
                    'Asistencia administrativa',
                ],

                'Recursos humanos' => [
                    'Recursos humanos',
                    'Gestión humana',
                    'Selección de personal',
                    'Reclutamiento',
                    'Entrevistas laborales',
                    'Contratación',
                    'Inducción de personal',
                    'Capacitación empresarial',
                    'Evaluación de desempeño',
                    'Gestión del talento',
                    'Bienestar laboral',
                ],

                'Procesos y calidad' => [
                    'Gestión de procesos',
                    'Levantamiento de procesos',
                    'Documentación de procesos',
                    'Gestión de calidad',
                    'Control de calidad',
                    'Auditoría interna',
                    'Mejora continua',
                    'Indicadores de gestión',
                    'ISO 9001',
                    'Gestión de riesgos',
                ],
            ],

            // ==========================================================
            // 09. FINANZAS Y CONTABILIDAD
            // ==========================================================

            'Finanzas y contabilidad' => [

                'Contabilidad' => [
                    'Contabilidad',
                    'Contabilidad empresarial',
                    'Contabilidad financiera',
                    'Contabilidad de costos',
                    'Contabilidad tributaria',
                    'Elaboración de estados financieros',
                    'Conciliaciones bancarias',
                    'Cuentas por pagar',
                    'Cuentas por cobrar',
                    'Facturación',
                    'Control contable',
                    'Libros contables',
                ],

                'Finanzas' => [
                    'Finanzas personales',
                    'Finanzas empresariales',
                    'Análisis financiero',
                    'Planeación financiera',
                    'Presupuestos',
                    'Flujo de caja',
                    'Proyecciones financieras',
                    'Análisis de costos',
                    'Evaluación financiera',
                    'Gestión de tesorería',
                ],

                'Impuestos' => [
                    'Impuestos',
                    'Gestión tributaria',
                    'Declaraciones tributarias',
                    'Facturación electrónica',
                    'Retenciones',
                    'Información exógena',
                    'Planeación tributaria',
                ],
            ],

            // ==========================================================
            // 10. MARKETING Y PUBLICIDAD
            // ==========================================================

            'Marketing y publicidad' => [

                'Marketing digital' => [
                    'Marketing digital',
                    'Marketing de contenidos',
                    'Marketing en redes sociales',
                    'Inbound marketing',
                    'Email marketing',
                    'Marketing de afiliados',
                    'Marketing de influencers',
                    'Marketing local',
                    'Estrategia digital',
                ],

                'SEO y posicionamiento' => [
                    'SEO',
                    'SEO técnico',
                    'SEO local',
                    'Investigación de palabras clave',
                    'Optimización de contenidos',
                    'Link building',
                    'Posicionamiento web',
                    'Analítica SEO',
                ],

                'Publicidad digital' => [
                    'Publicidad digital',
                    'Google Ads',
                    'Meta Ads',
                    'Facebook Ads',
                    'Instagram Ads',
                    'TikTok Ads',
                    'YouTube Ads',
                    'Creación de campañas',
                    'Segmentación de audiencias',
                    'Optimización de campañas',
                ],

                'Marca y estrategia' => [
                    'Branding',
                    'Estrategia de marca',
                    'Identidad de marca',
                    'Posicionamiento de marca',
                    'Naming',
                    'Estrategia de contenidos',
                    'Investigación de mercados',
                    'Análisis de competencia',
                    'Segmentación de mercados',
                ],
            ],

            // ==========================================================
            // 11. VENTAS Y COMERCIO
            // ==========================================================

            'Ventas y comercio' => [

                'Ventas' => [
                    'Ventas',
                    'Ventas presenciales',
                    'Ventas telefónicas',
                    'Ventas consultivas',
                    'Ventas B2B',
                    'Ventas B2C',
                    'Ventas digitales',
                    'Prospección comercial',
                    'Negociación',
                    'Cierre de ventas',
                    'Seguimiento comercial',
                    'Fidelización de clientes',
                ],

                'Servicio al cliente' => [
                    'Servicio al cliente',
                    'Atención al cliente',
                    'Atención telefónica',
                    'Atención presencial',
                    'Atención por chat',
                    'Gestión de PQRS',
                    'Fidelización',
                    'Experiencia del cliente',
                    'Servicio postventa',
                ],

                'Comercio electrónico' => [
                    'Comercio electrónico',
                    'E-commerce',
                    'Tiendas virtuales',
                    'Administración de tiendas online',
                    'Marketplace',
                    'Catálogos digitales',
                    'Ventas por internet',
                    'Medios de pago digitales',
                    'Gestión de pedidos online',
                ],

                'Compras e inventarios' => [
                    'Compras',
                    'Negociación con proveedores',
                    'Gestión de proveedores',
                    'Inventarios',
                    'Control de inventarios',
                    'Almacenamiento',
                    'Gestión de bodegas',
                    'Abastecimiento',
                ],
            ],

            // ==========================================================
            // 12. CONSTRUCCIÓN
            // ==========================================================

            'Construcción' => [

                'Construcción general' => [
                    'Construcción',
                    'Obra civil',
                    'Construcción de viviendas',
                    'Construcción comercial',
                    'Remodelaciones',
                    'Reformas',
                    'Demoliciones',
                    'Supervisión de obra',
                    'Administración de obra',
                    'Presupuestos de obra',
                    'Lectura de planos',
                ],

                'Albañilería' => [
                    'Albañilería',
                    'Mampostería',
                    'Construcción de muros',
                    'Construcción de pisos',
                    'Fundaciones',
                    'Encofrados',
                    'Mezclas de concreto',
                    'Acabados en concreto',
                    'Reparación de muros',
                ],

                'Drywall y sistemas livianos' => [
                    'Drywall',
                    'Instalación de drywall',
                    'Cielos rasos',
                    'Divisiones en drywall',
                    'Muros livianos',
                    'Superboard',
                    'Paneles decorativos',
                    'Aislamiento acústico',
                    'Aislamiento térmico',
                ],

                'Acabados' => [
                    'Pintura',
                    'Estuco',
                    'Enchapes',
                    'Instalación de pisos',
                    'Cerámica',
                    'Porcelanato',
                    'Laminados',
                    'Vinilo',
                    'Impermeabilización',
                    'Acabados arquitectónicos',
                    'Restauración de superficies',
                ],

                'Topografía y planos' => [
                    'Topografía',
                    'Levantamientos topográficos',
                    'Medición de terrenos',
                    'Lectura de planos',
                    'Dibujo técnico',
                    'AutoCAD',
                    'Diseño arquitectónico',
                    'Modelado 3D',
                    'BIM',
                ],
            ],

            // ==========================================================
            // 13. ELECTRICIDAD Y ENERGÍA
            // ==========================================================

            'Electricidad y energía' => [

                'Electricidad residencial' => [
                    'Electricidad',
                    'Electricidad residencial',
                    'Instalaciones eléctricas',
                    'Mantenimiento eléctrico',
                    'Reparaciones eléctricas',
                    'Instalación de tomacorrientes',
                    'Instalación de interruptores',
                    'Instalación de luminarias',
                    'Tableros eléctricos',
                    'Cableado eléctrico',
                ],

                'Electricidad industrial' => [
                    'Electricidad industrial',
                    'Automatización industrial',
                    'Control eléctrico',
                    'Motores eléctricos',
                    'Tableros de control',
                    'Mantenimiento eléctrico industrial',
                    'Sistemas de control',
                ],

                'Energía solar' => [
                    'Energía solar',
                    'Paneles solares',
                    'Instalación de paneles solares',
                    'Mantenimiento de paneles solares',
                    'Sistemas fotovoltaicos',
                    'Energía renovable',
                    'Eficiencia energética',
                ],
            ],

            // ==========================================================
            // 14. PLOMERÍA Y GAS
            // ==========================================================

            'Plomería y gas' => [

                'Plomería' => [
                    'Plomería',
                    'Instalaciones hidráulicas',
                    'Reparaciones de tuberías',
                    'Instalación de sanitarios',
                    'Instalación de lavamanos',
                    'Instalación de duchas',
                    'Destaponamiento de tuberías',
                    'Detección de fugas',
                    'Mantenimiento hidráulico',
                ],

                'Gas y sistemas relacionados' => [
                    'Instalaciones de gas',
                    'Mantenimiento de gas',
                    'Reparación de redes de gas',
                    'Instalación de calentadores',
                    'Mantenimiento de calentadores',
                    'Instalación de estufas',
                ],
            ],

            // ==========================================================
            // 15. CARPINTERÍA Y MADERA
            // ==========================================================

            'Carpintería y madera' => [

                'Carpintería' => [
                    'Carpintería',
                    'Carpintería residencial',
                    'Carpintería comercial',
                    'Fabricación de muebles',
                    'Reparación de muebles',
                    'Restauración de muebles',
                    'Instalación de muebles',
                    'Muebles a medida',
                    'Armarios',
                    'Cocinas integrales',
                    'Puertas de madera',
                    'Closets',
                    'Bibliotecas',
                ],

                'Ebanistería' => [
                    'Ebanistería',
                    'Tallado de madera',
                    'Torneado de madera',
                    'Restauración de madera',
                    'Acabados de madera',
                    'Barnizado',
                    'Lacado',
                    'Pulido de madera',
                ],
            ],

            // ==========================================================
            // 16. METAL Y SOLDADURA
            // ==========================================================

            'Metal y soldadura' => [

                'Soldadura' => [
                    'Soldadura',
                    'Soldadura eléctrica',
                    'Soldadura MIG',
                    'Soldadura TIG',
                    'Soldadura de aluminio',
                    'Soldadura de acero',
                    'Corte de metal',
                    'Trabajo con metales',
                ],

                'Estructuras metálicas' => [
                    'Estructuras metálicas',
                    'Fabricación de estructuras',
                    'Instalación de estructuras',
                    'Fabricación de rejas',
                    'Fabricación de puertas metálicas',
                    'Fabricación de ventanas metálicas',
                    'Barandas metálicas',
                    'Cerramientos metálicos',
                ],
            ],

            // ==========================================================
            // 17. MANTENIMIENTO DEL HOGAR
            // ==========================================================

            'Mantenimiento del hogar' => [

                'Mantenimiento general' => [
                    'Mantenimiento general',
                    'Mantenimiento locativo',
                    'Mantenimiento preventivo',
                    'Mantenimiento correctivo',
                    'Reparaciones del hogar',
                    'Instalaciones domésticas',
                    'Montaje de muebles',
                    'Instalación de repisas',
                    'Instalación de cuadros',
                    'Instalación de cortinas',
                ],

                'Vidriería y aluminio' => [
                    'Vidriería',
                    'Instalación de vidrios',
                    'Reparación de vidrios',
                    'Ventanas de aluminio',
                    'Puertas de aluminio',
                    'Divisiones de baño',
                    'Espejos',
                    'Instalación de espejos',
                ],

                'Cerrajería' => [
                    'Cerrajería',
                    'Instalación de cerraduras',
                    'Reparación de cerraduras',
                    'Cambio de cerraduras',
                    'Llaves',
                    'Instalación de sistemas de seguridad para puertas',
                ],
            ],

            // ==========================================================
            // 18. ELECTRODOMÉSTICOS Y REFRIGERACIÓN
            // ==========================================================

            'Electrodomésticos y refrigeración' => [

                'Reparación de electrodomésticos' => [
                    'Reparación de electrodomésticos',
                    'Mantenimiento de electrodomésticos',
                    'Reparación de lavadoras',
                    'Reparación de neveras',
                    'Reparación de estufas',
                    'Reparación de hornos',
                    'Reparación de microondas',
                    'Reparación de secadoras',
                    'Reparación de licuadoras',
                ],

                'Refrigeración y climatización' => [
                    'Refrigeración',
                    'Aire acondicionado',
                    'Instalación de aire acondicionado',
                    'Mantenimiento de aire acondicionado',
                    'Reparación de aire acondicionado',
                    'Mantenimiento de neveras',
                    'Sistemas de refrigeración',
                    'Climatización',
                ],
            ],

            // ==========================================================
            // 19. AUTOMOTRIZ
            // ==========================================================

            'Automotriz' => [

                'Mecánica automotriz' => [
                    'Mecánica automotriz',
                    'Mantenimiento de vehículos',
                    'Mantenimiento preventivo de vehículos',
                    'Cambio de aceite',
                    'Cambio de filtros',
                    'Frenos',
                    'Suspensión',
                    'Dirección',
                    'Motor',
                    'Transmisión',
                    'Embrague',
                ],

                'Electricidad automotriz' => [
                    'Electricidad automotriz',
                    'Diagnóstico electrónico automotriz',
                    'Baterías',
                    'Alternadores',
                    'Arranques',
                    'Luces automotrices',
                    'Sistemas eléctricos de vehículos',
                    'Instalación de accesorios automotrices',
                ],

                'Carrocería y estética automotriz' => [
                    'Latonería',
                    'Pintura automotriz',
                    'Pulido automotriz',
                    'Detailing',
                    'Lavado de vehículos',
                    'Limpieza interior de vehículos',
                    'Restauración de vehículos',
                ],

                'Motocicletas' => [
                    'Mecánica de motocicletas',
                    'Mantenimiento de motocicletas',
                    'Electricidad de motocicletas',
                    'Reparación de motocicletas',
                    'Mantenimiento preventivo de motocicletas',
                ],
            ],

            // ==========================================================
            // 20. TRANSPORTE Y LOGÍSTICA
            // ==========================================================

            'Transporte y logística' => [

                'Conducción' => [
                    'Conducción',
                    'Conducción de automóvil',
                    'Conducción de motocicleta',
                    'Conducción de vehículos comerciales',
                    'Transporte de pasajeros',
                    'Transporte de mercancías',
                    'Conducción defensiva',
                ],

                'Mensajería y entregas' => [
                    'Mensajería',
                    'Mensajería urbana',
                    'Entrega de paquetes',
                    'Entrega de documentos',
                    'Domicilios',
                    'Reparto de mercancías',
                    'Logística de última milla',
                ],

                'Logística' => [
                    'Logística',
                    'Gestión logística',
                    'Almacenamiento',
                    'Despacho de mercancías',
                    'Distribución',
                    'Ruteo',
                    'Control de inventarios',
                    'Cadena de suministro',
                    'Gestión de bodegas',
                    'Transporte de carga',
                ],
            ],

            // ==========================================================
            // 21. GASTRONOMÍA
            // ==========================================================

            'Gastronomía' => [

                'Cocina' => [
                    'Cocina',
                    'Cocina colombiana',
                    'Cocina internacional',
                    'Cocina mediterránea',
                    'Cocina mexicana',
                    'Cocina italiana',
                    'Cocina asiática',
                    'Cocina vegetariana',
                    'Cocina vegana',
                    'Cocina saludable',
                    'Preparación de alimentos',
                    'Catering',
                ],

                'Panadería' => [
                    'Panadería',
                    'Pan artesanal',
                    'Panadería integral',
                    'Panadería tradicional',
                    'Elaboración de panes',
                    'Masa madre',
                    'Productos de panadería',
                ],

                'Repostería y pastelería' => [
                    'Repostería',
                    'Pastelería',
                    'Decoración de tortas',
                    'Elaboración de tortas',
                    'Cupcakes',
                    'Galletas',
                    'Postres',
                    'Chocolatería',
                    'Confitería',
                    'Mesas de postres',
                ],

                'Bebidas' => [
                    'Preparación de bebidas',
                    'Café',
                    'Barismo',
                    'Coctelería',
                    'Jugos naturales',
                    'Bebidas artesanales',
                    'Preparación de bebidas frías',
                ],
            ],

            // ==========================================================
            // 22. BELLEZA Y CUIDADO PERSONAL
            // ==========================================================

            'Belleza y cuidado personal' => [

                'Peluquería' => [
                    'Peluquería',
                    'Corte de cabello',
                    'Peinados',
                    'Coloración',
                    'Tinte de cabello',
                    'Mechas',
                    'Alisado',
                    'Tratamientos capilares',
                    'Peinados para eventos',
                ],

                'Barbería' => [
                    'Barbería',
                    'Corte masculino',
                    'Perfilado de barba',
                    'Diseño de barba',
                    'Afeitado',
                    'Corte infantil',
                ],

                'Estética y maquillaje' => [
                    'Maquillaje',
                    'Maquillaje social',
                    'Maquillaje para eventos',
                    'Maquillaje artístico',
                    'Manicure',
                    'Pedicure',
                    'Nail art',
                    'Cuidado facial',
                    'Cuidado de la piel',
                    'Depilación',
                ],
            ],

            // ==========================================================
            // 23. MODA Y CONFECCIÓN
            // ==========================================================

            'Moda y confección' => [

                'Costura y confección' => [
                    'Costura',
                    'Confección',
                    'Arreglos de ropa',
                    'Modificación de prendas',
                    'Diseño de prendas',
                    'Elaboración de prendas',
                    'Patronaje',
                    'Corte de tela',
                    'Confección industrial',
                ],

                'Textiles' => [
                    'Bordado',
                    'Bordado a mano',
                    'Bordado industrial',
                    'Tejido',
                    'Crochet',
                    'Macramé',
                    'Estampado textil',
                    'Sublimación',
                    'Serigrafía',
                ],

                'Imagen y moda' => [
                    'Diseño de moda',
                    'Asesoría de imagen',
                    'Estilismo',
                    'Personal shopper',
                    'Diseño de accesorios',
                    'Diseño de joyería',
                ],
            ],

            // ==========================================================
            // 24. HOGAR Y SERVICIOS DOMÉSTICOS
            // ==========================================================

            'Hogar y servicios domésticos' => [

                'Limpieza' => [
                    'Limpieza doméstica',
                    'Limpieza profunda',
                    'Limpieza de oficinas',
                    'Limpieza de locales',
                    'Limpieza de vidrios',
                    'Limpieza de muebles',
                    'Limpieza de tapicería',
                    'Desinfección',
                    'Organización del hogar',
                ],

                'Jardinería' => [
                    'Jardinería',
                    'Mantenimiento de jardines',
                    'Diseño de jardines',
                    'Poda',
                    'Cuidado de plantas',
                    'Huertas domésticas',
                    'Paisajismo',
                    'Instalación de jardines',
                ],

                'Cuidado de mascotas' => [
                    'Cuidado de mascotas',
                    'Paseo de perros',
                    'Cuidado de perros',
                    'Cuidado de gatos',
                    'Baño de mascotas',
                    'Aseo de mascotas',
                    'Adiestramiento básico de mascotas',
                ],
            ],

            // ==========================================================
            // 25. ARTE Y MÚSICA
            // ==========================================================

            'Arte y música' => [

                'Música' => [
                    'Canto',
                    'Vocalización',
                    'Guitarra',
                    'Guitarra eléctrica',
                    'Guitarra acústica',
                    'Piano',
                    'Teclado',
                    'Batería',
                    'Percusión',
                    'Bajo eléctrico',
                    'Violín',
                    'Viola',
                    'Violonchelo',
                    'Flauta',
                    'Saxofón',
                    'Trompeta',
                    'Acordeón',
                    'Arpa',
                ],

                'Producción musical' => [
                    'Producción musical',
                    'Composición musical',
                    'Arreglos musicales',
                    'Grabación musical',
                    'Mezcla musical',
                    'Masterización',
                    'Edición musical',
                    'Creación de pistas',
                    'Música para eventos',
                ],

                'Artes escénicas' => [
                    'Teatro',
                    'Actuación',
                    'Danza',
                    'Coreografía',
                    'Ballet',
                    'Danza contemporánea',
                    'Danza urbana',
                    'Folclor',
                    'Improvisación teatral',
                    'Títeres',
                ],
            ],

            // ==========================================================
            // 26. DEPORTES Y ACTIVIDAD FÍSICA
            // ==========================================================

            'Deportes y actividad física' => [

                'Entrenamiento físico' => [
                    'Entrenamiento físico',
                    'Preparación física',
                    'Entrenamiento funcional',
                    'Acondicionamiento físico',
                    'Entrenamiento personalizado',
                    'Entrenamiento grupal',
                    'Movilidad',
                    'Flexibilidad',
                    'Calistenia',
                    'Entrenamiento de fuerza',
                    'Entrenamiento cardiovascular',
                ],

                'Deportes' => [
                    'Fútbol',
                    'Baloncesto',
                    'Voleibol',
                    'Béisbol',
                    'Softbol',
                    'Natación',
                    'Atletismo',
                    'Ciclismo',
                    'Tenis',
                    'Tenis de mesa',
                    'Bádminton',
                    'Patinaje',
                    'Skateboarding',
                    'Ajedrez',
                ],

                'Actividades recreativas' => [
                    'Recreación',
                    'Animación deportiva',
                    'Actividades lúdicas',
                    'Juegos infantiles',
                    'Campamentos',
                    'Excursionismo',
                    'Senderismo',
                ],
            ],

            // ==========================================================
            // 27. SALUD Y BIENESTAR
            // ==========================================================

            'Salud y bienestar' => [

                'Bienestar físico' => [
                    'Bienestar físico',
                    'Actividad física',
                    'Acondicionamiento físico',
                    'Estiramiento',
                    'Relajación',
                    'Movilidad corporal',
                    'Hábitos saludables',
                ],

                'Servicios profesionales de salud' => [
                    'Medicina',
                    'Enfermería',
                    'Odontología',
                    'Psicología',
                    'Fisioterapia',
                    'Terapia ocupacional',
                    'Fonoaudiología',
                    'Nutrición',
                    'Optometría',
                    'Terapia respiratoria',
                ],

                'Bienestar y cuidado personal' => [
                    'Masaje',
                    'Masaje relajante',
                    'Masaje deportivo',
                    'Técnicas de relajación',
                    'Meditación',
                    'Yoga',
                    'Respiración consciente',
                ],
            ],

            // ==========================================================
            // 28. AGRICULTURA
            // ==========================================================

            'Agricultura' => [

                'Cultivos' => [
                    'Agricultura',
                    'Cultivo de hortalizas',
                    'Cultivo de frutas',
                    'Cultivo de plantas',
                    'Cultivo de café',
                    'Cultivo de flores',
                    'Cultivo de plantas aromáticas',
                    'Agricultura orgánica',
                    'Agricultura sostenible',
                ],

                'Huertas y jardinería productiva' => [
                    'Huertas',
                    'Huertas urbanas',
                    'Huertas caseras',
                    'Hidroponía',
                    'Compostaje',
                    'Producción de abonos',
                    'Semilleros',
                    'Propagación de plantas',
                ],

                'Ganadería y producción animal' => [
                    'Ganadería',
                    'Avicultura',
                    'Apicultura',
                    'Piscicultura',
                    'Producción de huevos',
                    'Producción de leche',
                    'Cuidado de animales de granja',
                ],
            ],

            // ==========================================================
            // 29. MEDIO AMBIENTE
            // ==========================================================

            'Medio ambiente' => [

                'Reciclaje y residuos' => [
                    'Reciclaje',
                    'Separación de residuos',
                    'Gestión de residuos',
                    'Aprovechamiento de residuos',
                    'Reciclaje de plástico',
                    'Reciclaje de papel',
                    'Reciclaje de vidrio',
                    'Compostaje',
                ],

                'Sostenibilidad' => [
                    'Sostenibilidad',
                    'Educación ambiental',
                    'Conservación ambiental',
                    'Eficiencia energética',
                    'Consumo responsable',
                    'Economía circular',
                    'Producción sostenible',
                    'Gestión ambiental',
                ],
            ],

            // ==========================================================
            // 30. SERVICIOS PROFESIONALES
            // ==========================================================

            'Servicios profesionales' => [

                'Ingeniería' => [
                    'Ingeniería civil',
                    'Ingeniería de sistemas',
                    'Ingeniería de software',
                    'Ingeniería industrial',
                    'Ingeniería eléctrica',
                    'Ingeniería electrónica',
                    'Ingeniería mecánica',
                    'Ingeniería ambiental',
                    'Ingeniería de alimentos',
                    'Ingeniería agronómica',
                ],

                'Arquitectura' => [
                    'Arquitectura',
                    'Diseño arquitectónico',
                    'Diseño de interiores',
                    'Diseño de espacios',
                    'Renderizado arquitectónico',
                    'Modelado arquitectónico',
                    'Visualización arquitectónica',
                    'Urbanismo',
                    'Paisajismo',
                ],

                'Derecho' => [
                    'Derecho',
                    'Asesoría jurídica',
                    'Derecho civil',
                    'Derecho comercial',
                    'Derecho laboral',
                    'Derecho de familia',
                    'Derecho administrativo',
                    'Contratación',
                    'Redacción de documentos jurídicos',
                ],

                'Ciencias económicas' => [
                    'Economía',
                    'Análisis económico',
                    'Administración financiera',
                    'Consultoría económica',
                    'Investigación de mercados',
                    'Análisis estadístico',
                ],
            ],

            // ==========================================================
            // 31. SERVICIOS EMPRESARIALES
            // ==========================================================

            'Servicios empresariales' => [

                'Asesoría empresarial' => [
                    'Asesoría empresarial',
                    'Asesoría administrativa',
                    'Asesoría comercial',
                    'Asesoría financiera',
                    'Asesoría en procesos',
                    'Consultoría empresarial',
                    'Diagnóstico empresarial',
                ],

                'Servicios de oficina' => [
                    'Asistencia virtual',
                    'Asistencia administrativa',
                    'Digitación',
                    'Transcripción',
                    'Elaboración de documentos',
                    'Elaboración de informes',
                    'Presentaciones empresariales',
                    'Gestión de agendas',
                    'Organización documental',
                ],

                'Servicios comerciales' => [
                    'Representación comercial',
                    'Telemercadeo',
                    'Prospección comercial',
                    'Gestión de clientes',
                    'Investigación comercial',
                    'Estudios de mercado',
                ],
            ],

            // ==========================================================
            // 32. EVENTOS
            // ==========================================================

            'Eventos' => [

                'Organización de eventos' => [
                    'Organización de eventos',
                    'Planeación de eventos',
                    'Coordinación de eventos',
                    'Producción de eventos',
                    'Logística de eventos',
                    'Protocolo',
                    'Atención de invitados',
                    'Coordinación de proveedores',
                ],

                'Decoración de eventos' => [
                    'Decoración de eventos',
                    'Decoración de fiestas',
                    'Decoración de bodas',
                    'Decoración de cumpleaños',
                    'Decoración con globos',
                    'Diseño de mesas',
                    'Diseño de escenarios',
                    'Montaje de eventos',
                ],

                'Entretenimiento' => [
                    'Animación de eventos',
                    'Animación infantil',
                    'Recreación infantil',
                    'Magia',
                    'Payasismo',
                    'DJ',
                    'Presentador de eventos',
                    'Maestro de ceremonias',
                ],
            ],

            // ==========================================================
            // 33. TURISMO Y HOSPITALIDAD
            // ==========================================================

            'Turismo y hospitalidad' => [

                'Turismo' => [
                    'Turismo',
                    'Guianza turística',
                    'Guía turístico',
                    'Planificación de viajes',
                    'Diseño de rutas turísticas',
                    'Turismo cultural',
                    'Turismo rural',
                    'Turismo de naturaleza',
                    'Turismo gastronómico',
                ],

                'Hospitalidad' => [
                    'Atención al huésped',
                    'Administración de alojamientos',
                    'Gestión de reservas',
                    'Recepción hotelera',
                    'Servicio de habitaciones',
                    'Organización de experiencias',
                ],

                'Gastronomía turística' => [
                    'Rutas gastronómicas',
                    'Experiencias gastronómicas',
                    'Cocina tradicional',
                    'Cultura gastronómica',
                ],
            ],

            // ==========================================================
            // 34. SEGURIDAD Y PREVENCIÓN
            // ==========================================================

            'Seguridad y prevención' => [

                'Seguridad' => [
                    'Seguridad privada',
                    'Vigilancia',
                    'Control de acceso',
                    'Seguridad física',
                    'Supervisión de seguridad',
                    'Prevención de riesgos',
                    'Protección de instalaciones',
                ],

                'Emergencias y prevención' => [
                    'Primeros auxilios',
                    'Prevención de emergencias',
                    'Planes de emergencia',
                    'Evacuación',
                    'Gestión del riesgo',
                    'Prevención de accidentes',
                    'Seguridad industrial',
                ],
            ],

            // ==========================================================
            // 35. INDUSTRIA Y PRODUCCIÓN
            // ==========================================================

            'Industria y producción' => [

                'Producción' => [
                    'Producción industrial',
                    'Procesos industriales',
                    'Operación de maquinaria',
                    'Control de producción',
                    'Planeación de producción',
                    'Programación de producción',
                    'Control de inventarios',
                ],

                'Mantenimiento industrial' => [
                    'Mantenimiento industrial',
                    'Mantenimiento mecánico',
                    'Mantenimiento eléctrico',
                    'Mantenimiento preventivo',
                    'Mantenimiento predictivo',
                    'Mantenimiento correctivo',
                    'Diagnóstico de maquinaria',
                ],

                'Calidad' => [
                    'Control de calidad',
                    'Inspección de calidad',
                    'Aseguramiento de calidad',
                    'Gestión de calidad',
                    'Mejora continua',
                    'Metrología',
                    'Trazabilidad',
                ],
            ],

            // ==========================================================
            // 36. MANUFACTURA
            // ==========================================================

            'Manufactura' => [

                'Fabricación' => [
                    'Manufactura',
                    'Fabricación de productos',
                    'Producción artesanal',
                    'Producción en serie',
                    'Ensamblaje',
                    'Empaque',
                    'Etiquetado',
                    'Acabados',
                ],

                'Plásticos y materiales' => [
                    'Transformación de plásticos',
                    'Moldeo',
                    'Inyección de plástico',
                    'Trabajo con acrílico',
                    'Trabajo con PVC',
                    'Corte de materiales',
                ],

                'Empaque y presentación' => [
                    'Empaque de productos',
                    'Diseño de empaques',
                    'Embalaje',
                    'Etiquetado de productos',
                    'Presentación de productos',
                ],
            ],

            // ==========================================================
            // 37. COMERCIO Y DISTRIBUCIÓN
            // ==========================================================

            'Comercio y distribución' => [

                'Comercio minorista' => [
                    'Comercio minorista',
                    'Administración de tiendas',
                    'Atención en punto de venta',
                    'Caja',
                    'Exhibición de productos',
                    'Visual merchandising',
                ],

                'Distribución' => [
                    'Distribución comercial',
                    'Distribución de productos',
                    'Representación de marcas',
                    'Distribución mayorista',
                    'Distribución minorista',
                    'Gestión de rutas comerciales',
                ],
            ],

            // ==========================================================
            // 38. HABILIDADES ACADÉMICAS Y PROFESIONALES
            // ==========================================================

            'Habilidades académicas y profesionales' => [

                'Investigación' => [
                    'Investigación',
                    'Investigación académica',
                    'Investigación cualitativa',
                    'Investigación cuantitativa',
                    'Análisis documental',
                    'Revisión bibliográfica',
                    'Recopilación de información',
                    'Análisis estadístico',
                ],

                'Presentaciones y documentación' => [
                    'Elaboración de presentaciones',
                    'PowerPoint',
                    'Google Slides',
                    'Elaboración de informes',
                    'Redacción de informes',
                    'Elaboración de documentos técnicos',
                    'Diagramación de documentos',
                ],

                'Herramientas ofimáticas' => [
                    'Microsoft Word',
                    'Microsoft Excel',
                    'Microsoft PowerPoint',
                    'Microsoft Outlook',
                    'Google Docs',
                    'Google Sheets',
                    'Google Drive',
                    'Herramientas ofimáticas',
                ],
            ],

            // ==========================================================
            // 39. SERVICIOS PERSONALES
            // ==========================================================

            'Servicios personales' => [

                'Asistencia personal' => [
                    'Asistencia personal',
                    'Asistencia para diligencias',
                    'Organización de agendas',
                    'Acompañamiento',
                    'Gestión de compras',
                    'Realización de diligencias',
                ],

                'Cuidado y acompañamiento' => [
                    'Cuidado infantil',
                    'Acompañamiento infantil',
                    'Cuidado de adultos mayores',
                    'Acompañamiento de adultos mayores',
                    'Acompañamiento a citas',
                    'Apoyo doméstico',
                ],
            ],

            // ==========================================================
            // 40. FOTOGRAFÍA COMERCIAL
            // ==========================================================

            'Fotografía comercial' => [

                'Fotografía para negocios' => [
                    'Fotografía de productos',
                    'Fotografía para catálogos',
                    'Fotografía para comercio electrónico',
                    'Fotografía gastronómica',
                    'Fotografía inmobiliaria',
                    'Fotografía empresarial',
                    'Fotografía de servicios',
                ],

                'Contenido para redes' => [
                    'Fotografía para Instagram',
                    'Fotografía para Facebook',
                    'Fotografía para TikTok',
                    'Creación de contenido para redes',
                    'Fotografía de campañas',
                    'Fotografía de marca',
                ],
            ],

            // ==========================================================
            // 41. REPARACIONES
            // ==========================================================

            'Reparaciones' => [

                'Reparaciones generales' => [
                    'Reparaciones del hogar',
                    'Reparaciones locativas',
                    'Reparaciones menores',
                    'Mantenimiento de instalaciones',
                    'Diagnóstico de averías',
                    'Reparación de puertas',
                    'Reparación de ventanas',
                ],

                'Reparación de muebles' => [
                    'Reparación de muebles',
                    'Restauración de muebles',
                    'Tapicería',
                    'Reparación de sillas',
                    'Reparación de mesas',
                    'Restauración de madera',
                    'Restauración de muebles antiguos',
                ],
            ],

            // ==========================================================
            // 42. TAPICERÍA
            // ==========================================================

            'Tapicería' => [

                'Tapicería de muebles' => [
                    'Tapicería',
                    'Tapicería de muebles',
                    'Tapicería de sofás',
                    'Tapicería de sillas',
                    'Tapicería de vehículos',
                    'Restauración de tapicería',
                    'Cambio de espumas',
                    'Cambio de telas',
                ],

                'Decoración textil' => [
                    'Cojines',
                    'Cortinas',
                    'Persianas',
                    'Decoración textil',
                    'Confección de cortinas',
                    'Instalación de cortinas',
                ],
            ],

            // ==========================================================
            // 43. COMUNICACIÓN DIGITAL Y REDES SOCIALES
            // ==========================================================

            'Comunicación digital y redes sociales' => [

                'Redes sociales' => [
                    'Gestión de redes sociales',
                    'Community management',
                    'Administración de Facebook',
                    'Administración de Instagram',
                    'Administración de TikTok',
                    'Administración de YouTube',
                    'Creación de publicaciones',
                    'Programación de publicaciones',
                    'Moderación de comunidades',
                ],

                'Contenido digital' => [
                    'Creación de contenido',
                    'Creación de reels',
                    'Creación de historias',
                    'Creación de videos cortos',
                    'Fotografía para redes',
                    'Diseño para redes sociales',
                    'Calendario de contenidos',
                    'Estrategia de contenidos',
                ],
            ],

            // ==========================================================
            // 44. DISEÑO DE ESPACIOS
            // ==========================================================

            'Diseño de espacios' => [

                'Interiorismo' => [
                    'Diseño de interiores',
                    'Decoración de interiores',
                    'Diseño de habitaciones',
                    'Diseño de salas',
                    'Diseño de cocinas',
                    'Diseño de baños',
                    'Diseño de oficinas',
                    'Distribución de espacios',
                ],

                'Decoración' => [
                    'Decoración',
                    'Decoración residencial',
                    'Decoración comercial',
                    'Decoración de oficinas',
                    'Decoración de habitaciones',
                    'Decoración minimalista',
                    'Decoración de eventos',
                ],
            ],

            // ==========================================================
            // 45. HABILIDADES COMUNITARIAS
            // ==========================================================

            'Habilidades comunitarias' => [

                'Liderazgo comunitario' => [
                    'Liderazgo comunitario',
                    'Organización comunitaria',
                    'Coordinación comunitaria',
                    'Gestión de grupos',
                    'Participación comunitaria',
                    'Voluntariado',
                    'Trabajo comunitario',
                ],

                'Apoyo comunitario' => [
                    'Apoyo comunitario',
                    'Orientación comunitaria',
                    'Acompañamiento social',
                    'Gestión de actividades comunitarias',
                    'Organización de actividades',
                    'Apoyo a eventos comunitarios',
                ],

                'Convivencia' => [
                    'Mediación',
                    'Resolución de conflictos',
                    'Construcción de acuerdos',
                    'Trabajo en equipo',
                    'Comunicación interpersonal',
                    'Escucha activa',
                    'Facilitación de grupos',
                ],
            ],

            // ==========================================================
            // 46. CIENCIA E INVESTIGACIÓN
            // ==========================================================

            'Ciencia e investigación' => [

                'Ciencias naturales' => [
                    'Biología',
                    'Química',
                    'Física',
                    'Geología',
                    'Astronomía',
                    'Ciencias ambientales',
                    'Microbiología',
                ],

                'Investigación aplicada' => [
                    'Investigación científica',
                    'Diseño de experimentos',
                    'Análisis de datos científicos',
                    'Investigación de campo',
                    'Investigación documental',
                    'Análisis estadístico',
                    'Elaboración de informes científicos',
                ],
            ],

            // ==========================================================
            // 47. EDUCACIÓN FINANCIERA
            // ==========================================================

            'Educación financiera' => [

                'Finanzas personales' => [
                    'Educación financiera',
                    'Presupuesto personal',
                    'Organización financiera',
                    'Ahorro',
                    'Planificación financiera personal',
                    'Control de gastos',
                    'Administración del dinero',
                ],

                'Emprendimiento financiero' => [
                    'Finanzas para emprendedores',
                    'Costos de emprendimiento',
                    'Presupuesto empresarial',
                    'Flujo de caja empresarial',
                    'Precio de productos',
                    'Análisis de rentabilidad',
                ],
            ],

            // ==========================================================
            // 48. HABILIDADES DIGITALES
            // ==========================================================

            'Habilidades digitales' => [

                'Productividad digital' => [
                    'Productividad digital',
                    'Organización digital',
                    'Gestión de archivos digitales',
                    'Herramientas colaborativas',
                    'Trabajo remoto',
                    'Teletrabajo',
                    'Gestión de información digital',
                ],

                'Herramientas digitales' => [
                    'Google Workspace',
                    'Microsoft 365',
                    'Canva',
                    'Trello',
                    'Notion',
                    'Slack',
                    'Zoom',
                    'Microsoft Teams',
                    'Google Meet',
                ],

                'Comercio digital' => [
                    'Pagos digitales',
                    'Banca digital',
                    'Facturación digital',
                    'Catálogos digitales',
                    'Ventas digitales',
                    'Atención al cliente digital',
                ],
            ],

            // ==========================================================
            // 49. OFICIOS ARTESANALES
            // ==========================================================

            'Oficios artesanales' => [

                'Artesanía' => [
                    'Artesanía',
                    'Artesanía en madera',
                    'Artesanía en cuero',
                    'Artesanía en metal',
                    'Artesanía textil',
                    'Artesanía en cerámica',
                    'Artesanía decorativa',
                ],

                'Productos artesanales' => [
                    'Elaboración de velas',
                    'Elaboración de jabones',
                    'Elaboración de productos decorativos',
                    'Elaboración de accesorios',
                    'Elaboración de bisutería',
                    'Elaboración de regalos personalizados',
                    'Personalización de productos',
                ],
            ],

            // ==========================================================
            // 50. OTROS SERVICIOS
            // ==========================================================

            'Otros servicios' => [

                'Servicios generales' => [
                    'Servicios generales',
                    'Asistencia general',
                    'Apoyo operativo',
                    'Apoyo logístico',
                    'Organización',
                    'Montaje',
                    'Desmontaje',
                ],

                'Servicios especializados' => [
                    'Consultoría especializada',
                    'Asesoría especializada',
                    'Capacitación especializada',
                    'Soporte especializado',
                    'Servicios técnicos especializados',
                ],
            ],
        ];

        $categoriaOrden = 0;

        foreach ($catalogo as $nombreCategoria => $subcategorias) {

            $categoriaOrden++;

            $categoria = SkillCategory::updateOrCreate(
                [
                    'slug' => Str::slug($nombreCategoria),
                ],
                [
                    'name' => $nombreCategoria,
                    'description' => "Skills relacionadas con {$nombreCategoria}.",
                    'icon' => null,
                    'is_active' => true,
                    'sort_order' => $categoriaOrden,
                ]
            );

            $subcategoriaOrden = 0;

            foreach ($subcategorias as $nombreSubcategoria => $skills) {

                $subcategoriaOrden++;

                $subcategoria = SkillSubcategory::updateOrCreate(
                    [
                        'skill_category_id' => $categoria->id,
                        'slug' => Str::slug($nombreSubcategoria),
                    ],
                    [
                        'name' => $nombreSubcategoria,
                        'description' => "Skills relacionadas con {$nombreSubcategoria}.",
                        'is_active' => true,
                        'sort_order' => $subcategoriaOrden,
                    ]
                );

                $skillOrden = 0;

                foreach ($skills as $nombreSkill) {

                    $skillOrden++;

                    Skill::updateOrCreate(
                        [
                            'skill_subcategory_id' => $subcategoria->id,
                            'slug' => Str::slug($nombreSkill),
                        ],
                        [
                            'name' => $nombreSkill,
                            'description' => null,
                            'is_active' => true,
                            'is_featured' => false,
                            'sort_order' => $skillOrden,
                        ]
                    );
                }
            }
        }

        $this->command?->info('Banco Maestro de Skills cargado correctamente.');
        $this->command?->info('Categorías: ' . SkillCategory::count());
        $this->command?->info('Subcategorías: ' . SkillSubcategory::count());
        $this->command?->info('Skills: ' . Skill::count());
    }
}
