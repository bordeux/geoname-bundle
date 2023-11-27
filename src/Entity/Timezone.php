<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity()]
#[ORM\Table(name: 'geo__timezone')]
class Timezone implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;



    #[ORM\Column(length: 50, unique: true)]
    protected string $timezone;


    #[ORM\Column(length: 2)]
    protected string $countryCode;


    #[ORM\Column(type: "float", scale: 1)]
    protected float $gmtOffset;


    #[ORM\Column(type: "float", scale: 1)]
    protected float $dstOffset;


    #[ORM\Column(type: "float", scale: 1)]
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
