<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use SplFileObject;

/**
 * Class AdministrativeImport
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class AdministrativeImport extends AbstractImport
{
    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl("\t");
        $file->seek(PHP_INT_MAX);
        $max = $file->key();
        $file->seek(1); //skip header

        $administrative = $this->em->getRepository(Administrative::class);

        $pos = 0;

        foreach ($file as $row) {
            $row = array_map('trim', $row);
            list(
                $code,
                $name,
                $asciiName,
                $geoNameId
                ) = $row;


            $object = $administrative->findOneBy(['code' => $code]) ?: new Administrative();
            $object->setCode($code);
            $object->setName($name);
            $object->setAsciiName($asciiName);

            !$object->getId() && $this->em->persist($object);

            is_callable($progress) && $progress(($pos++) / $max);

            if ($pos % 10000) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }

    public function getName(): string
    {
        return "Administrative";
    }

    public function getOptionName(): string
    {
        return "admin1-codes";
    }

    public function getDescription(): string
    {
        return "Admin 1 codes file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/dump/admin1CodesASCII.txt";
    }
}
