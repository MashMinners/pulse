<?php
return [
    'keysStorage' => $_SERVER['DOCUMENT_ROOT'].'/../storage/auth/keys',
    'secretKey' => 'baseSecretKey', //если авторизация не усиленная, то будет использоваться один на всех этот ключ
    'algorithm' => 'configs/auth/algorithm',
    'permittedChars' => '0123456789abcdefghijklmnopqrstuvwxyz',
    'jwtHeaderAlgorithm' => 'HS256', //RS256
    'jwtPayload' => [
        'iss' => $_SERVER['HTTP_HOST'], //Тот, кто выпустил токен (например, домен)
        'sub' => '', //Основной субъект (пользователь), к которому относится токен (например, идентификатор пользователя)
        'aud' => $_SERVER['HTTP_HOST'], //Предполагаемые получатели/пользователи токена
        'exp' =>time() + 84600, //Метка времени Unix, когда истекает срок действия токена
        'nbf' => time(), //Метка времени Unix, до которой токен недействителен
        'iat' => time(), //Метка времени Unix, когда был создан токен
        'jti'=> '' //Уникальный идентификатор токена, полезный для аннулирования
    ],
    'passwordHashParams' => [
        'algo' => 'PASSWORD_BCRYPT',
        'options' => [
            'cost' => 6
        ]
    ],
    'refreshParams' => [
        'expires' => time() + 84600
    ]
];