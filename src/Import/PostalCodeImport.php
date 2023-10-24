<?php

namespace Bordeux\Bundle\GeoNameBundle\Import;

use Bordeux\Bundle\GeoNameBundle\Entity\PostalCode;
use Bordeux\Bundle\GeoNameBundle\Helper\TextFileReader;

class PostalCodeImport extends AbstractImport
{
    protected const BULK_SIZE = 5000;


    /**
     * @return TextFileReader\Header[]
     */
    protected function getHeaders(): array
    {
        return [
            new TextFileReader\Header(0, 'country_code'),
            new TextFileReader\Header(1, 'postal_code'),
            new TextFileReader\Header(2, 'place_name'),
            new TextFileReader\Header(3, 'admin_name1'),
            new TextFileReader\Header(4, 'admin_code1'),
            new TextFileReader\Header(5, 'admin_name2'),
            new TextFileReader\Header(6, 'admin_code2'),
            new TextFileReader\Header(7, 'admin_name3'),
            new TextFileReader\Header(8, 'admin_code3'),
        ];
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     */
    protected function importData(string $filePath, ?callable $progress = null): bool
    {
        $reader = new TextFileReader($filePath, $progress);
        $reader->addHeaders($this->getHeaders());

        $connection = $this->em->getConnection();
        $postal = $this->em->getRepository(PostalCode::class);
        // $connection->beginTransaction();
        foreach ($reader->process(static::BULK_SIZE) as $bulk) {
            foreach ($bulk as $item) {
                // $object = $postal->findOneBy([
                //     'countryCode' => $item['country_code'],
                //     'postalCode' => $item['postal_code'], 'placeName' => $item['place_name']
                // ]);
                // if (!$object)
                $object = new PostalCode();
                $object->setPlaceName($item['place_name'])
                    ->setPostalCode($item['postal_code'])
                    ->setCountryCode($item['country_code'])
                    ->setAdminCode1($item['admin_code1'])
                    ->setAdminName1($item['admin_name1'])
                    ->setAdminCode2($item['admin_code2'])
                    ->setAdminName2($item['admin_name2'])
                    ->setAdminCode3($item['admin_code3'])
                    ->setAdminName3($item['admin_name3']);



                !$object->getId() && $this->em->persist($object);
            }
            $this->em->flush();
            $this->em->clear();
        }
        //   $connection->commit();
        return true;
    }



    public function getName(): string
    {
        return "PostalCodeImport";
    }

    public function getOptionName(): string
    {
        return "postal-codes";
    }

    public function getDescription(): string
    {
        return "Postal codes file URL";
    }

    public function getDefaultValue(): string
    {
        return "https://download.geonames.org/export/zip/allCountries.zip#allCountries.txt";
    }
}
