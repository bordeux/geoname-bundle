<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

/**
 * Class AdministrativeImport
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class AdministrativeImport extends AbstractImport
{
    protected const BULK_SIZE = 10000;

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders([
            new TextFileReader\Header(0, 'code'),
            new TextFileReader\Header(1, 'name'),
            new TextFileReader\Header(2, 'asci_name')
        ]);

        $connection = $this->em->getConnection();
        $administrative = $this->em->getRepository(Administrative::class);
        $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            foreach ($bulk as $item) {
                $object = $administrative->findOneBy(['code' => $item['code']]) ?: (new Administrative())->setCode($item['code']);
                $object->setName($item['name'])
                    ->setAsciiName($item['asci_name']);
                !$object->getId() && $this->em->persist($object);
            }
            $this->em->flush();
            $this->em->clear();
        }
        $connection->commit();
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
