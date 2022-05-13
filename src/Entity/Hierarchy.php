<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hierarchy
 * @author Chris Bednarczyk <chris@tourradar.com>
 * @package Bordeux\Bundle\GeoNameBundle\Entity
 *
 * @ORM\Table(name="geo__name_hierarchy")
 * @ORM\Entity()
 */
class Hierarchy
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @var GeoName|null
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected ?GeoName $parent;

    /**
     * @var GeoName|null
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName", inversedBy="parents")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected ?GeoName $child;

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
    public function getParent(): ?GeoName
    {
        return $this->parent;
    }

    /**
     * @param GeoName|null $parent
     * @return $this
     */
    public function setParent(?GeoName $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return GeoName|null
     */
    public function getChild(): ?GeoName
    {
        return $this->child;
    }

    /**
     * @param GeoName|null $child
     * @return $this
     */
    public function setChild(?GeoName $child): self
    {
        $this->child = $child;
        return $this;
    }
}
