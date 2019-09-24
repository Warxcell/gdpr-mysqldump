<?php

namespace Arxy\GdprDumpBundle;

interface ValueConverter
{
    public function convertToDatabaseValue($tableName, $colName, $colValue): ?string;

    public function convertToPHPValue($tableName, $colName, $colValue);
}
