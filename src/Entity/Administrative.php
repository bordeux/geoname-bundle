<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Administrative
 *
 * @ORM\Table(name="geo__administrative")
 * @ORM\Entity()
 */
class Administrative
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, unique=true)
     */
    protected string $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200)
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ascii_name", type="string", length=200, nullable=true)
     */
    protected ?string $asciiName;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="geoname_id", referencedColumnName="id", nullable=true)
     */
    protected ?GeoName $geoName;

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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAsciiName(): ?string
    {
        return $this->asciiName;
    }

    /**
     * @param string|null $asciiName
     * @return $this
     */
    public function setAsciiName(?string $asciiName): self
    {
        $this->asciiName = $asciiName;
        return $this;
    }

    /**
     * @return GeoName|null
     */
    public function getGeoName(): ?GeoName
    {
        return $this->geoName;
    }

    /**
     * @param GeoName|null $geoName
     * @return $this
     */
    public function setGeoName(?GeoName $geoName): self
    {
        $this->geoName = $geoName;
        return $this;
    }
}

