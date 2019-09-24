<?php

declare(strict_types=1);

namespace Arxy\GdprDumpBundle;

use Ifsnop\GdprDumpBundle\Mysqldump;

class MysqldumpFactory
{
    protected static function getDsn($parsed)
    {
        $dsn = $parsed['scheme'];
        $dsn .= ':dbname='.$parsed['path'];

        if (isset($parsed['host'])) {
            $dsn .= ";host=".$parsed['host'];
        }
        if (isset($parsed['port'])) {
            $dsn .= ";port=".$parsed['port'];
        }

        return $dsn;
    }

    public static function createMysqldump(
        ValueTransformer $transformValue,
        string $dsn,
        array $dumpSettings = [],
        array $pdoSettings = []
    ) {
        $parsed = parse_url($dsn);
        $parsed = array_map('rawurldecode', $parsed);
        $parsed['path'] = substr($parsed['path'], 1);

        $mysqldump = new Mysqldump(
            self::getDsn($parsed),
            $parsed['user'] ?? null,
            $parsed['pass'] ?? null,
            $dumpSettings,
            $pdoSettings
        );
        $mysqldump->setTransformColumnValueHook([$transformValue, 'transform']);

        return $mysqldump;
    }
}
