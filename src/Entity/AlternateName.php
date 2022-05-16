<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

/**
 * AlternateNames
 *
 * @ORM\Table(name="geo__alternate_name", indexes={
 *     @ORM\Index(name="geoname_name_search_idx", columns={"geoname_id", "type"}),
 * })
 * @ORM\Entity()
 */
class AlternateName implements Stringable
{
    public const TYPE_NONE = 'none';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected ?int $id;


    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="geoname_id", referencedColumnName="id", nullable=false)
     */
    protected GeoName $geoName;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    protected string $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    protected $value;

    /**
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return GeoName
     */
    public function getGeoName(): GeoName
    {
        return $this->geoName;
    }

    /**
     * @param GeoName $geoName
     * @return $this
     */
    public function setGeoName(GeoName $geoName): self
    {
        $this->geoName = $geoName;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
