
<?php

function mostrarNotificacion($titulo, $mensaje, $tipo = 'exito') {

    // TIPOS DE NOTIFICACIÓN
    $tipos = [
        'exito' => [
            'icono' => '✓',
            'fondo' => '#dcfce7',
            'color' => '#166534',
            'borde' => '#22c55e'
        ],
        'error' => [
            'icono' => '✕',
            'fondo' => '#fee2e2',
            'color' => '#991b1b',
            'borde' => '#ef4444'
        ],
        'info' => [
            'icono' => 'ℹ',
            'fondo' => '#dbeafe',
            'color' => '#1e40af',
            'borde' => '#3b82f6'
        ]
    ];

    // CONFIGURACIÓN
    $config = $tipos[$tipo] ?? $tipos['info'];

    // PROTEGER CONTENIDO
    $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

    // IDENTIFICADOR ÚNICO
    $id = 'notificacion_' . bin2hex(random_bytes(6));

    echo '
    <style>
        #' . $id . ' {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translate(-50%, -150px);

            display: flex;
            align-items: center;
            gap: 14px;

            width: 450px;
            max-width: calc(100vw - 30px);
            padding: 20px 28px;
            box-sizing: border-box;

            background: ' . $config['fondo'] . ';
            color: ' . $config['color'] . ';
            border-left: 5px solid ' . $config['borde'] . ';
            border-radius: 12px;

            box-shadow: 0 8px 30px rgba(0,0,0,0.15);

            font-family: Arial, sans-serif;
            z-index: 99999;
            opacity: 0;

            transition:
                transform 0.5s ease,
                opacity 0.5s ease;

            pointer-events: none;
        }

        #' . $id . '.visible {
            transform: translate(-50%, 0);
            opacity: 1;
        }

        #' . $id . ' .noti-icono {
            font-size: 25px;
            font-weight: bold;
        }

        #' . $id . ' .noti-contenido strong {
            display: block;
            font-size: 15px;
            margin-bottom: 4px;
        }

        #' . $id . ' .noti-contenido p {
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        @media (max-width: 480px) {
            #' . $id . ' {
                width: calc(100vw - 30px);
                top: 15px;
            }
        }
    </style>

    <div id="' . $id . '" role="status" aria-live="polite">

        <span class="noti-icono">
            ' . $config['icono'] . '
        </span>

        <div class="noti-contenido">
            <strong>' . $titulo . '</strong>
            <p>' . nl2br($mensaje) . '</p>
        </div>

    </div>

    <script>
        (function() {

            const notificacion = document.getElementById("' . $id . '");

            if (!notificacion) return;

            // MOSTRAR NOTIFICACIÓN
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    notificacion.classList.add("visible");
                });
            });

            // OCULTAR DESPUÉS DE 4 SEGUNDOS
            setTimeout(() => {

                notificacion.classList.remove("visible");

                // ELIMINAR DEL DOM
                setTimeout(() => {
                    notificacion.remove();
                }, 500);

            }, 4000);

        })();
    </script>
    ';
}

?>