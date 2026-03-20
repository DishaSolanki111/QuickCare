<?php

function qc_notification_store_init($conn)
{
    static $initialized = false;
    if ($initialized) {
        return;
    }

    @mysqli_query(
        $conn,
        "CREATE TABLE IF NOT EXISTS notification_seen_tbl (
            SEEN_ID INT AUTO_INCREMENT PRIMARY KEY,
            USER_TYPE VARCHAR(30) NOT NULL,
            USER_ID INT NOT NULL,
            NOTIF_KEY VARCHAR(100) NOT NULL,
            SEEN_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_user_notif (USER_TYPE, USER_ID, NOTIF_KEY)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $initialized = true;
}

function qc_notification_make_key($type, $raw)
{
    return sha1($type . '|' . (string)$raw);
}

function qc_notification_seen_map($conn, $userType, $userId, $keys)
{
    qc_notification_store_init($conn);

    $map = [];
    $keys = array_values(array_unique(array_filter($keys, function ($k) {
        return $k !== null && $k !== '';
    })));

    if (empty($keys)) {
        return $map;
    }

    $escapedType = mysqli_real_escape_string($conn, (string)$userType);
    $safeUserId = (int)$userId;
    $escapedKeys = array_map(function ($k) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, (string)$k) . "'";
    }, $keys);

    $sql = "SELECT NOTIF_KEY FROM notification_seen_tbl
            WHERE USER_TYPE = '{$escapedType}'
              AND USER_ID = {$safeUserId}
              AND NOTIF_KEY IN (" . implode(',', $escapedKeys) . ")";
    $res = @mysqli_query($conn, $sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $map[$row['NOTIF_KEY']] = true;
        }
    }

    return $map;
}

function qc_notification_mark_seen($conn, $userType, $userId, $keys)
{
    qc_notification_store_init($conn);

    $keys = array_values(array_unique(array_filter($keys, function ($k) {
        return $k !== null && $k !== '';
    })));
    if (empty($keys)) {
        return;
    }

    $escapedType = mysqli_real_escape_string($conn, (string)$userType);
    $safeUserId = (int)$userId;
    $values = [];

    foreach ($keys as $key) {
        $values[] = "('{$escapedType}', {$safeUserId}, '" . mysqli_real_escape_string($conn, (string)$key) . "', NOW())";
    }

    $sql = "INSERT IGNORE INTO notification_seen_tbl (USER_TYPE, USER_ID, NOTIF_KEY, SEEN_AT) VALUES " . implode(',', $values);
    @mysqli_query($conn, $sql);
}

