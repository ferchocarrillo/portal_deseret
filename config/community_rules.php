<?php

return [
    'rules' => [
        1 => [
            'code' => 'R1',
            'title' => 'Respeto y no discriminación',
            'description' => 'No se tolera el acoso, las ofensas, el discurso de odio ni la discriminación.',
        ],
        2 => [
            'code' => 'R2',
            'title' => 'Contenido prohibido o ilegal',
            'description' => 'Prohibido compartir material para adultos, violencia explícita o actividades ilícitas.',
        ],
        3 => [
            'code' => 'R3',
            'title' => 'Cero spam y enlaces no autorizados',
            'description' => 'No se permiten mensajes masivos, autopromoción no autorizada ni software malicioso.',
        ],
        4 => [
            'code' => 'R4',
            'title' => 'Privacidad y datos personales',
            'description' => 'Prohibido difundir información privada o de contacto de terceros sin consentimiento.',
        ],
        5 => [
            'code' => 'R5',
            'title' => 'Suplantación de identidad y fraude',
            'description' => 'No está permitido fingir ser otro usuario, líder o entidad ni realizar estafas.',
        ],
        6 => [
            'code' => 'R6',
            'title' => 'Uso correcto de canales y categorías',
            'description' => 'Las publicaciones deben respetar la temática del espacio correspondiente.',
        ],
        7 => [
            'code' => 'R7',
            'title' => 'Respeto a la fe, doctrina, símbolos sagrados y liderazgo',
            'description' => 'Estrictamente prohibida la publicación de imágenes o textos blasfemos, mofa de símbolos religiosos, ataques a líderes o descalificación deliberada de la doctrina.',
        ],
    ],
    'consequences' => [
        1 => 'Primera advertencia: Notificación formal y registro en el perfil.',
        2 => 'Segunda advertencia: Suspensión temporal por 7 días.',
        3 => 'Tercera advertencia: Cancelación definitiva de la cuenta.',
    ],
];
