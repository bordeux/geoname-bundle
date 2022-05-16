<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

class Administrative2Import extends AdministrativeImport
{
    public function getName(): string
    {
        return "Administrative 2";
    }

    public function getOptionName(): string
    {
        return "admin2-codes";
    }

    public function getDescription(): string
    {
        return "Admin 2 codes file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/admin2Codes.txt";
    }
}
