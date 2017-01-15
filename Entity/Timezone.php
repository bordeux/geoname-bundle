<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timezone
 *
 * @ORM\Table(name="geo__timezone")
 * @ORM\Entity(repositoryClass="Bordeux\Bundle\GeoNameBundle\Repository\TimezoneRepository")
 */
class Timezone
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=50, unique=true)
     */
    protected $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=2)
     */
    protected $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="gmt_offset", type="float", scale=1)
     */
    protected $gmtOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="dst_offset", type="float", scale=1)
     */
    protected $dstOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="raw_offset", type="float", scale=1)
     */
    protected $rawOffset;


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
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $timezone
     * @return Timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $countryCode
     * @return Timezone
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getGmtOffset()
    {
        return $this->gmtOffset;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $gmtOffset
     * @return Timezone
     */
    public function setGmtOffset($gmtOffset)
    {
        $this->gmtOffset = $gmtOffset;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getDstOffset()
    {
        return $this->dstOffset;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $dstOffset
     * @return Timezone
     */
    public function setDstOffset($dstOffset)
    {
        $this->dstOffset = $dstOffset;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getRawOffset()
    {
        return $this->rawOffset;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $rawOffset
     * @return Timezone
     */
    public function setRawOffset($rawOffset)
    {
        $this->rawOffset = $rawOffset;
        return $this;
    }


}

