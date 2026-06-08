<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: none;
        }
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f4f7;
        }
        .email-content {
            width: 100%;
            max-width: 570px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-masthead {
            padding: 25px 0;
            text-align: center;
            background-color: #b7102a;
            border: 4px solid #1e293b;
            border-bottom: 8px solid #ffcc00;
        }
        .email-masthead_name {
            font-size: 24px;
            font-weight: 900;
            color: #ffffff;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            border: 4px solid #1e293b;
            margin-top: 15px;
        }
        .email-body_inner {
            width: 100%;
            max-width: 570px;
            margin: 0 auto;
            padding: 35px;
        }
        h1 {
            margin-top: 0;
            color: #1e293b;
            font-size: 20px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        p {
            margin-top: 0;
            color: #51545e;
            font-size: 15px;
            line-height: 1.6;
            text-align: left;
        }
        .button {
            display: inline-block;
            background-color: #ffcc00;
            color: #1e293b !important;
            text-decoration: none;
            border: 2px solid #1e293b;
            box-shadow: 4px 4px 0px 0px rgba(0, 0, 0, 1);
            padding: 12px 24px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin: 25px 0;
            border-radius: 8px;
        }
        .button:hover {
            box-shadow: none;
            transform: translate(2px, 2px);
        }
        .email-footer {
            width: 100%;
            max-width: 570px;
            margin: 0 auto;
            padding: 30px 0;
            text-align: center;
        }
        .email-footer p {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="email-masthead">
                            <a href="{{ url('/') }}" class="email-masthead_name">
                                QuickBite Express
                            </a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <h1>{{ $title }}</h1>
                                        <p>Hola <strong>{{ $userName }}</strong>,</p>
                                        <p>{!! nl2br(e($messageBody)) !!}</p>
                                        
                                        @if($actionUrl)
                                            <div style="text-align: center;">
                                                <a href="{{ $actionUrl }}" class="button" target="_blank">
                                                    {{ $actionText ?? 'Ver Detalles' }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <p>¡Gracias por elegir QuickBite Express!<br>El equipo de QuickBite</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <p>&copy; {{ date('Y') }} QuickBite Express. Todos los derechos reservados.</p>
                            <p>Calle Principal, Centro Financiero, San Salvador, El Salvador</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
