<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoName
 *
 * @ORM\Table(name="geo__name" ,indexes={
 *     @ORM\Index(name="geoname_geoname_search_idx", columns={"name", "country_code"})
 * })
 * @ORM\Entity(repositoryClass="Bordeux\Bundle\GeoNameBundle\Repository\GeoNameRepository")
 */
class GeoName
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ascii_name", type="string", length=200, nullable=true)
     */
    protected $asciiName;


    /**
     * @var float
     * @ORM\Column(name="latitude", type="float", scale=6, precision=9, nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", scale=6, precision=9, nullable=true)
     */
    protected $longitude;


    /**
     * @var float
     *
     * @ORM\Column(name="feature_class", type="string", length=1, nullable=true)
     */
    protected $featureClass;

    /**
     * @var float
     *
     * @ORM\Column(name="feature_code", type="string", length=10, nullable=true)
     */
    protected $featureCode;

    /**
     * @var float
     *
     * @ORM\Column(name="country_code", type="string", length=2, nullable=true)
     */
    protected $countryCode;


    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=true)
     */
    protected $country;

    /**
     * @var float
     *
     * @ORM\Column(name="cc2", type="string", length=200, nullable=true)
     */
    protected $cc2;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin1_id", referencedColumnName="id", nullable=true)
     */
    protected $admin1;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin2_id", referencedColumnName="id", nullable=true)
     */
    protected $admin2;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin3_id", referencedColumnName="id", nullable=true)
     */
    protected $admin3;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin4_id", referencedColumnName="id", nullable=true)
     */
    protected $admin4;


    /**
     * @var int
     *
     * @ORM\Column(name="population", type="bigint", nullable=true)
     */
    protected $population;

    /**
     * @var int
     *
     * @ORM\Column(name="elevation", type="integer", nullable=true)
     */
    protected $elevation;

    /**
     * @var integer
     *
     * @ORM\Column(name="dem", type="integer", nullable=true)
     */
    protected $dem;

    /**
     * @var Timezone
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Timezone")
     * @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=true)
     */
    protected $timezone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modification_date", type="date", nullable=true)
     */
    protected $modificationDate;




    /**
     * @var Hierarchy[]
     *
     * @ORM\OneToMany(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Hierarchy", mappedBy="child")
     */
    protected $parents;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $name
     * @return GeoName
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getAsciiName()
    {
        return $this->asciiName;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $asciiName
     * @return GeoName
     */
    public function setAsciiName($asciiName)
    {
        $this->asciiName = $asciiName;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $latitude
     * @return GeoName
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $longitude
     * @return GeoName
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getFeatureClass()
    {
        return $this->featureClass;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $featureClass
     * @return GeoName
     */
    public function setFeatureClass($featureClass)
    {
        $this->featureClass = $featureClass;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getFeatureCode()
    {
        return $this->featureCode;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $featureCode
     * @return GeoName
     */
    public function setFeatureCode($featureCode)
    {
        $this->featureCode = $featureCode;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $countryCode
     * @return GeoName
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return float
     */
    public function getCc2()
    {
        return $this->cc2;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param float $cc2
     * @return GeoName
     */
    public function setCc2($cc2)
    {
        $this->cc2 = $cc2;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Administrative
     */
    public function getAdmin1()
    {
        return $this->admin1;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param Administrative $admin1
     * @return GeoName
     */
    public function setAdmin1($admin1)
    {
        $this->admin1 = $admin1;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Administrative
     */
    public function getAdmin2()
    {
        return $this->admin2;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param Administrative $admin2
     * @return GeoName
     */
    public function setAdmin2($admin2)
    {
        $this->admin2 = $admin2;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Administrative
     */
    public function getAdmin3()
    {
        return $this->admin3;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param Administrative $admin3
     * @return GeoName
     */
    public function setAdmin3($admin3)
    {
        $this->admin3 = $admin3;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Administrative
     */
    public function getAdmin4()
    {
        return $this->admin4;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param Administrative $admin4
     * @return GeoName
     */
    public function setAdmin4($admin4)
    {
        $this->admin4 = $admin4;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $population
     * @return GeoName
     */
    public function setPopulation($population)
    {
        $this->population = $population;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $elevation
     * @return GeoName
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getDem()
    {
        return $this->dem;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $dem
     * @return GeoName
     */
    public function setDem($dem)
    {
        $this->dem = $dem;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param Timezone $timezone
     * @return GeoName
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return \DateTime
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param \DateTime $modificationDate
     * @return GeoName
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }




}

