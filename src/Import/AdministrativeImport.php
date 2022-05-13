<?php


namespace Bordeux\Bundle\GeoNameBundle\Import;


use Bordeux\Bundle\GeoNameBundle\Entity\Administrative;
use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use SplFileObject;

/**
 * Class AdministrativeImport
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class AdministrativeImport implements ImportInterface
{

    /**
     * @var EntityManagerInterface|EntityManager
     */
    protected EntityManagerInterface $em;

    /**
     * TimeZoneImport constructor.
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return PromiseInterface
     */
    public function import(string $filePath, ?callable $progress = null): PromiseInterface
    {
        $self = $this;
        /** @var Promise $promise */
        $promise = (new Promise(function () use ($filePath, $progress, $self, &$promise) {
            $promise->resolve(
                $self->_import($filePath, $progress)
            );
        }));

        return $promise;
    }

    /**
     * @param string $filePath
     * @param callable|null $progress
     * @return bool
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    protected function _import($filePath, callable $progress = null)
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl("\t");
        $file->seek(PHP_INT_MAX);
        $max = $file->key();
        $file->seek(1); //skip header

        $administrative = $this->em->getRepository("BordeuxGeoNameBundle:Administrative");

        $pos = 0;

        foreach ($file as $row) {
            $row = array_map('trim',$row);
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

            if($pos % 10000){
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }

}
