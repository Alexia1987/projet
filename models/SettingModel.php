<?php

function getSetting(PDO $pdo, string $key, mixed $default = null): ?string
{
    $query = $pdo->prepare("SELECT `stg_value` FROM setting WHERE `stg_key` = ?");
    $query->execute([$key]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['value'] : $default;
}

function isSchoolHoliday(PDO $pdo): bool
{
    return getSetting($pdo, 'holiday_mode') === '1';
}

function setSetting(PDO $pdo, string $key, string $value): bool
{
    $query = $pdo->prepare(
        "UPDATE setting SET `stg_value` = ? WHERE `stg_key` = ?"
    );
    return $query->execute([$value, $key]);
}
