<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Roboto';
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f4f8; padding: 20px;">
    <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border-collapse: collapse; border-spacing: 0; border: 1px solid #dddddd;">
        <tr>
            <td style="background-color: #0d3a58; color: #ffffff; padding: 16px; text-align: left;">
                <h4 style="margin: 0; font-size: 20px;">Recuperar contraseña - El Nogal, <b><?= $this->contenido->sbe_nomb ?></b>, El Nogal</h4>
            </td>
        </tr>
        <tr>
            <td style="padding: 24px; padding-top: 10px;">
                <p style="font-size: 16px; margin-bottom: 14px; color: #0d3a58;">¡Hola!,</p>
                <table style="width: 100%; background-color: #0d3a58; border-radius: 8px; border-collapse: collapse; padding: 16px;">
                    <tr>
                        <td style="padding: 16px; color: #ffffff; text-align: left; font-size: 15px;">
                            <p style="margin: 0; font-weight: 400;">
                                <span>Has solicitado recuperar tu contraseña.</span><br>
                                <span>Por favor haz clic en el siguiente enlace para restablecer tu contraseña de forma segura.</span>
                            </p>
                            <p style="margin: 10px; text-align: center;">
                                <a href="<?= $this->enlace_completo; ?>" target="_blank" style="display: inline-block; padding: 10px 20px; font-size: 14px; font-weight: 400; line-height: 1.5; text-align: center; text-decoration: none; white-space: nowrap; vertical-align: middle; cursor: pointer; border: 1px solid transparent; border-radius: 4px; color: #ffffff; background-color: #77bc23; border-color: #77bc23;">Restablecer contraseña</a>
                            </p>
                            <div style="margin-top: 20px; color: #ffffff;">
                                Si no solicitaste este cambio, puedes ignorar este mensaje.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
