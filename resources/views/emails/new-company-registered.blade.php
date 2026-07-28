<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo registro pendiente de aprobación</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:640px; margin:0 auto; padding:24px;">
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:18px; overflow:hidden;">
            <div style="background:#101a30; color:#ffffff; padding:24px;">
                <div style="font-size:13px; text-transform:uppercase; letter-spacing:.08em; color:#79dab8;">
                    Cash by Invenza — Panel de plataforma
                </div>
                <h1 style="margin:8px 0 0 0; font-size:22px; line-height:1.25;">
                    Nuevo registro pendiente de aprobación
                </h1>
            </div>

            <div style="padding:24px;">
                <p style="font-size:15px; line-height:1.6; margin:0 0 20px 0;">
                    Una empresa nueva acaba de registrarse y está esperando tu aprobación para poder usar el sistema.
                </p>

                <table style="width:100%; border-collapse:collapse; margin-bottom:22px;">
                    <tr>
                        <td style="padding:10px; border:1px solid #e2e8f0; background:#f8fafc; width:40%;">
                            <div style="font-size:12px; color:#64748b; font-weight:bold;">Empresa</div>
                        </td>
                        <td style="padding:10px; border:1px solid #e2e8f0;">
                            {{ $company->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px; border:1px solid #e2e8f0; background:#f8fafc;">
                            <div style="font-size:12px; color:#64748b; font-weight:bold;">Usuario</div>
                        </td>
                        <td style="padding:10px; border:1px solid #e2e8f0;">
                            {{ $user->name }} &lt;{{ $user->email }}&gt;
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px; border:1px solid #e2e8f0; background:#f8fafc;">
                            <div style="font-size:12px; color:#64748b; font-weight:bold;">Fecha</div>
                        </td>
                        <td style="padding:10px; border:1px solid #e2e8f0;">
                            {{ now()->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </table>

                <a href="{{ url('/platform/empresas') }}" style="display:inline-block; background:#158066; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:10px; font-size:14px; font-weight:bold;">
                    Revisar y aprobar
                </a>
            </div>

            <div style="background:#f1f5f9; padding:16px 24px; text-align:center;">
                <p style="font-size:12px; color:#64748b; margin:0;">
                    Este correo fue generado automáticamente por Cash by Invenza.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
