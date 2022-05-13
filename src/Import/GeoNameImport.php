<?php


namespace Bordeux\Bundle\GeoNameBundle\Import;


use Bordeux\Bundle\GeoNameBundle\Entity\Timezone;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Promise\Promise;
use SplFileObject;

/**
 * Class GeoNameImport
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Import
 */
class GeoNameImport implements ImportInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TimeZoneImport constructor.
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @param  string $filePath
     * @param callable|null $progress
     * @return Promise|\GuzzleHttp\Promise\PromiseInterface
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function import($filePath, callable $progress = null)
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

        $avrOneLineSize = 29.4;
        $batchSize = 10000;

        $connection = $this->em->getConnection();

        $fileInside = basename($filePath, ".zip") . '.txt';
        $handler = fopen("zip://{$filePath}#{$fileInside}", 'r');
        $max = (int) (filesize($filePath) / $avrOneLineSize);

        $fieldsNames = $this->getFieldNames();

        $geoNameTableName = $this->em
            ->getClassMetadata("BordeuxGeoNameBundle:GeoName")
            ->getTableName();

        $timezoneTableName = $this->em
            ->getClassMetadata("BordeuxGeoNameBundle:Timezone")
            ->getTableName();

        $administrativeTableName = $this->em
            ->getClassMetadata("BordeuxGeoNameBundle:Administrative")
            ->getTableName();


        $dbType = $connection->getDatabasePlatform()->getName();

        $connection->exec("START TRANSACTION");

        $pos = 0;

        $buffer = [];

        $queryBuilder = $connection->createQueryBuilder()
            ->insert($geoNameTableName);

        while (!feof($handler)) {
            $csv = fgetcsv($handler, null, "\t");
            if (!is_array($csv)) {
                continue;
            }
            if (!isset($csv[0]) || !is_numeric($csv[0])) {
                continue;
            }

            $row = array_map('trim', $csv);

            list(
                $geoNameId,
                $name,
                $asciiName,
                $alternateNames,
                $latitude,
                $longitude,
                $featureClass,
                $featureCode,
                $countryCode,
                $cc2,
                $admin1Code,
                $admin2Code,
                $admin3Code,
                $admin4Code,
                $population,
                $elevation,
                $dem,
                $timezone,
                $modificationDate
                ) = $row;


            if (!preg_match('/^\d{4}\-\d{2}-\d{2}$/', $modificationDate)) {
                continue;
            }


            $data = [
                $fieldsNames['id'] => (int)$geoNameId, //must be as first!
                $fieldsNames['name'] => $this->e($name),
                $fieldsNames['asciiName'] => $this->e($asciiName),
                $fieldsNames['latitude'] => $this->e($latitude),
                $fieldsNames['longitude'] => $this->e($longitude),
                $fieldsNames['featureClass'] => $this->e($featureClass),
                $fieldsNames['featureCode'] => $this->e($featureCode),
                $fieldsNames['countryCode'] => $this->e($countryCode),
                $fieldsNames['cc2'] => $this->e($cc2),
                $fieldsNames['population'] => $this->e($population),
                $fieldsNames['elevation'] => $this->e($elevation),
                $fieldsNames['dem'] => $this->e($dem),
                $fieldsNames['modificationDate'] => $this->e($modificationDate),
                $fieldsNames['timezone'] => $timezone ? "(SELECT id FROM {$timezoneTableName} WHERE timezone  =  " . $this->e($timezone) . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin1'] => $admin1Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->e("{$countryCode}.{$admin1Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin2'] => $admin2Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->e("{$countryCode}.{$admin1Code}.{$admin2Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin3'] => $admin3Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->e("{$countryCode}.{$admin1Code}.{$admin3Code}") . " LIMIT 1)" : 'NULL',
                $fieldsNames['admin4'] => $admin4Code ? "(SELECT id FROM {$administrativeTableName} WHERE code  =  " . $this->e("{$countryCode}.{$admin1Code}.{$admin4Code}") . " LIMIT 1)" : 'NULL',
            ];




            $query = $queryBuilder->values($data);


            $buffer[] = $this->insertToReplace($query, $dbType);

            $pos++;

            if ($pos % $batchSize) {
                $this->save($buffer);
                $buffer = [];
                is_callable($progress) && $progress(($pos) / $max);
            }

        }

        !empty($buffer) && $this->save($buffer);;
        $connection->exec('COMMIT');

        return true;
    }


    /**
     * @param QueryBuilder $insertSQL
     * @return mixed
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function insertToReplace(QueryBuilder $insertSQL, $dbType)
    {
        if ($dbType == "mysql") {
            $sql = $insertSQL->getSQL();
            return preg_replace('/' . preg_quote('INSERT ', '/') . '/', 'REPLACE ', $sql, 1);
        }

        if ($dbType == "postgresql") {
            $vals = $insertSQL->getQueryPart("values");
            $sql = $insertSQL->getSQL();
            reset($vals);
            $index = key($vals);
            array_shift($vals);

            $parts = [];
            foreach ($vals as $column => $val) {
                $parts[] = "{$column} = {$val}";
            }

            $sql .= " ON CONFLICT ({$index}) DO UPDATE  SET " . implode(", ", $parts);

            return $sql;
        }

        throw new \Exception("Unsupported database type");
    }

    /**
     * @param $queries
     * @return bool
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function save($queries)
    {
        $queries = implode("; \n", $queries);
        $this->em->getConnection()->exec($queries);

        return true;
    }


    /**
     * @return string[]
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    public function getFieldNames()
    {
        $metaData = $this->em->getClassMetadata("BordeuxGeoNameBundle:GeoName");

        $result = [];

        foreach ($metaData->getFieldNames() as $name) {
            $result[$name] = $metaData->getColumnName($name);
        }

        foreach ($metaData->getAssociationNames() as $name) {
            if ($metaData->isSingleValuedAssociation($name)) {
                $result[$name] = $metaData->getSingleAssociationJoinColumnName($name);
            }
        }

        return $result;
    }

    /**
     * @param string $val
     * @return string
     * @author Chris Bednarczyk <chris@tourradar.com>
     */
    protected function e($val)
    {
        if ($val === null || strlen($val) === 0) {
            return 'NULL';
        }
        return $this->em->getConnection()->quote($val);
    }

}
