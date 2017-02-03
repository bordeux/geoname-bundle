<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoName
 *
 * @ORM\Table(name="geo__country" ,indexes={
 *     @ORM\Index(name="geoname_country_search_idx", columns={"name", "iso"})
 * })
 * @ORM\Entity(repositoryClass="Bordeux\Bundle\GeoNameBundle\Repository\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="iso", type="string", length=2, nullable=false)
     */
    protected $iso;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=false)
     */
    protected $iso3;

    /**
     * @var integer
     *
     * @ORM\Column(name="iso_numeric", type="integer", length=3, nullable=false)
     */
    protected $isoNumeric;


    /**
     * @var string
     *
     * @ORM\Column(name="fips", type="string", length=2, nullable=true)
     */
    protected $fips;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=255, nullable=true)
     */
    protected $capital;


    /**
     * @var integer
     *
     * @ORM\Column(name="area", type="bigint", nullable=false)
     */
    protected $area;

    /**
     * @var integer
     *
     * @ORM\Column(name="population", type="bigint", nullable=false)
     */
    protected $population;


    /**
     * @var string
     *
     * @ORM\Column(name="tld", type="string", length=15, nullable=true)
     */
    protected $tld;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    protected $currency;


    /**
     * @var string
     *
     * @ORM\Column(name="currency_name", type="string", length=50, nullable=true)
     */
    protected $currencyName;


    /**
     * @var integer
     *
     * @ORM\Column(name="phone_prefix", type="integer", nullable=true)
     */
    protected $phonePrefix;


    /**
     * @var string
     *
     * @ORM\Column(name="postal_format", type="text", nullable=true)
     */
    protected $postalFormat;


    /**
     * @var string
     *
     * @ORM\Column(name="postal_regex", type="text", nullable=true)
     */
    protected $postalRegex;


    /**
     * @var array
     *
     * @ORM\Column(name="languages", type="json_array", nullable=true)
     */
    protected $languages;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="geoname_id", referencedColumnName="id", nullable=true)
     */
    protected $geoName;

    /**
     * Country constructor.
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $id
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $iso
     * @return Country
     */
    public function setIso($iso)
    {
        $this->iso = $iso;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getIso3()
    {
        return $this->iso3;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $iso3
     * @return Country
     */
    public function setIso3($iso3)
    {
        $this->iso3 = $iso3;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getIsoNumeric()
    {
        return $this->isoNumeric;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $isoNumeric
     * @return Country
     */
    public function setIsoNumeric($isoNumeric)
    {
        $this->isoNumeric = $isoNumeric;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getFips()
    {
        return $this->fips;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $fips
     * @return Country
     */
    public function setFips($fips)
    {
        $this->fips = $fips;
        return $this;
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
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $capital
     * @return Country
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;
        return $this;
    }



    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $area
     * @return Country
     */
    public function setArea($area)
    {
        $this->area =  (int) $area;
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
     * @return Country
     */
    public function setPopulation($population)
    {
        $this->population = (int)  $population;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getTld()
    {
        return $this->tld;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $tld
     * @return Country
     */
    public function setTld($tld)
    {
        $this->tld = $tld;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $currency
     * @return Country
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $currencyName
     * @return Country
     */
    public function setCurrencyName($currencyName)
    {
        $this->currencyName = $currencyName;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return int
     */
    public function getPhonePrefix()
    {
        return $this->phonePrefix;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param int $phonePrefix
     * @return Country
     */
    public function setPhonePrefix($phonePrefix)
    {
        $this->phonePrefix = $phonePrefix;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getPostalFormat()
    {
        return $this->postalFormat;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $postalFormat
     * @return Country
     */
    public function setPostalFormat($postalFormat)
    {
        $this->postalFormat = $postalFormat;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getPostalRegex()
    {
        return $this->postalRegex;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $postalRegex
     * @return Country
     */
    public function setPostalRegex($postalRegex)
    {
        $this->postalRegex = $postalRegex;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param array $languages
     * @return Country
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return GeoName
     */
    public function getGeoName()
    {
        return $this->geoName;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param GeoName $geoName
     * @return Country
     */
    public function setGeoName($geoName)
    {
        $this->geoName = $geoName;
        return $this;
    }







}

