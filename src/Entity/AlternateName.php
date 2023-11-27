<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity()]
#[ORM\Table(name: 'geo__alternate_name')]
#[ORM\Index(name: 'geoname_name_search_idx', columns: ['geoname_id', 'type'])]

class AlternateName implements Stringable
{
    public const TYPE_NONE = 'none';

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'GeoName')]
    #[ORM\JoinColumn(name: "geoname_id", referencedColumnName: "id", nullable: false)]
    protected GeoName $geoName;

    #[ORM\Column(length: 10, nullable: false)]
    protected string $type;

    #[ORM\Column(type: "text", length: 10, nullable: false)]
    protected string $value;

    #[ORM\Column(length: 1, nullable: true)]
    protected string $prefered;
 
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
     * Get the value of prefered
     */
    public function getPrefered(): string
    {
        return $this->prefered;
    }

    /**
     * Set the value of prefered
     */
    public function setPrefered(string $prefered): self
    {
        $this->prefered = $prefered;
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
