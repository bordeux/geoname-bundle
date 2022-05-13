<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

/**
 * Timezone
 *
 * @ORM\Table(name="geo__timezone")
 * @ORM\Entity()
 */
class Timezone implements Stringable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;


    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=50, unique=true)
     */
    protected string $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=2)
     */
    protected string $countryCode;

    /**
     * @var float
     *
     * @ORM\Column(name="gmt_offset", type="float", scale=1)
     */
    protected float $gmtOffset;

    /**
     * @var float
     *
     * @ORM\Column(name="dst_offset", type="float", scale=1)
     */
    protected float $dstOffset;

    /**
     * @var float
     *
     * @ORM\Column(name="raw_offset", type="float", scale=1)
     */
    protected float $rawOffset;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return $this
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return float
     */
    public function getGmtOffset(): float
    {
        return $this->gmtOffset;
    }

    /**
     * @param float $gmtOffset
     * @return $this
     */
    public function setGmtOffset(float $gmtOffset): self
    {
        $this->gmtOffset = $gmtOffset;
        return $this;
    }

    /**
     * @return float
     */
    public function getDstOffset(): float
    {
        return $this->dstOffset;
    }

    /**
     * @param float $dstOffset
     * @return $this
     */
    public function setDstOffset(float $dstOffset): self
    {
        $this->dstOffset = $dstOffset;
        return $this;
    }

    /**
     * @return float
     */
    public function getRawOffset(): float
    {
        return $this->rawOffset;
    }

    /**
     * @param float $rawOffset
     * @return $this
     */
    public function setRawOffset(float $rawOffset): self
    {
        $this->rawOffset = $rawOffset;
        return $this;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTimezone();
    }
}
