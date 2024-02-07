<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hierarchy
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Entity
 */
#[ORM\Entity()]
#[ORM\Table(name: 'geo__name_hierarchy')]
#[ORM\Index(name: 'geoname_hierarchy_unique_idx', columns: ['parent_id', 'child_id', 'type'])]
class Hierarchy
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;


    #[ORM\ManyToOne(targetEntity: GeoName::class)]
    #[ORM\JoinColumn(name: "parent_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected GeoName $parent;

    #[ORM\ManyToOne(targetEntity: GeoName::class, inversedBy: "parents")]
    #[ORM\JoinColumn(name: "child_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    protected GeoName $child;

    #[ORM\Column(length: 10, nullable: true)]
    protected ?string $type;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return GeoName|null
     */
    public function getParent(): GeoName
    {
        return $this->parent;
    }

    /**
     * @param GeoName $parent
     * @return $this
     */
    public function setParent(GeoName $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return GeoName
     */
    public function getChild(): GeoName
    {
        return $this->child;
    }

    /**
     * @param GeoName $child
     * @return $this
     */
    public function setChild(GeoName $child): self
    {
        $this->child = $child;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     *
     * @param string|null $type
     * @return Hierarchy
     */
    public function setType(?string $type): Hierarchy
    {
        $this->type = $type;
        return $this;
    }
}
