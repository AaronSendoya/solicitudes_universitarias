<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

/**
 * Neon routes connections by endpoint ID via SNI. Older libpq builds (e.g. the
 * one bundled with XAMPP's PHP on Windows) don't send SNI, so Neon can't tell
 * which endpoint to route to. Its documented workaround is to pass the
 * endpoint ID through the libpq "options" keyword instead.
 * See https://neon.tech/docs/connect/connection-errors#sni-support
 */
class NeonPostgresConnector extends PostgresConnector
{
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);

        if (! empty($config['endpoint'])) {
            $dsn .= ";options=endpoint={$config['endpoint']}";
        }

        return $dsn;
    }
}
