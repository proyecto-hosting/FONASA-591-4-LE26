<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación FONASA</title>
</head>
<body style="background-color: #f3f4f6; font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 30px 15px;">
        <tr>
            <td align="center">
                
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-collapse: collapse;">
                    
                    <tr>
                        <td style="background-color: #004A8F; padding: 25px; text-align: left; border-bottom: 4px solid #E3001B;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">FONASA</h1>
                                        <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Gobierno de Chile</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px; color: #333333;">
                            <h2 style="margin-top: 0; font-size: 20px; color: #004A8F; font-weight: bold;">Notificación Oficial</h2>
                            
                            <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">Estimado/a usuario/a,</p>
                            
                            <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
                                Junto con saludar, le informamos que se ha emitido un documento oficial asociado al RUT <strong>{{ $recipient->rut }}</strong> a través de nuestro sistema de distribución de información.
                            </p>
                            
                            <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
                                Para su revisión, conocimiento y fines pertinentes, hemos adjuntado este documento en formato PDF a este correo electrónico.
                            </p>

                            <div style="background-color: #f8fafc; border-left: 4px solid #004A8F; padding: 15px 20px; margin: 25px 0; border-radius: 0 4px 4px 0;">
                                <p style="margin: 0; font-size: 15px; color: #1e293b;">
                                    <strong style="color: #004A8F;">Documento Seguro:</strong> Podrá encontrar el archivo adjunto al final de este mensaje con el nombre <em>Notificacion_FONASA.pdf</em>.
                                </p>
                            </div>

                            <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
                                Le recordamos que es su responsabilidad revisar el contenido detallado en el documento adjunto.
                            </p>

                            <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-top: 35px;">
                                Atentamente,<br>
                                <strong style="color: #1e293b;">Fondo Nacional de Salud</strong>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #e5e7eb; padding: 25px 30px; text-align: center; border-top: 1px solid #d1d5db;">
                            <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.5;">
                                Este es un mensaje generado automáticamente por el sistema de notificaciones masivas de FONASA.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #E3001B; font-weight: bold;">
                                Por favor, no responda a este correo. Las respuestas a esta dirección no son monitoreadas.
                            </p>
                            <p style="margin: 20px 0 0 0; font-size: 11px; color: #9ca3af;">
                                &copy; {{ date('Y') }} Gobierno de Chile. Todos los derechos reservados.<br>
                                Plataforma operada bajo los estándares de seguridad del Estado.
                            </p>
                        </td>
                    </tr>

                </table>
                </td>
        </tr>
    </table>
    </body>
</html>