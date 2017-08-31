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
    protected $id;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName", inversedBy="parents")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $child;

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
     *
     * @return GeoName
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param GeoName $parent
     * @return Hierarchy
     */
    public function setParent(GeoName $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     *
     * @return GeoName
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     *
     * @param GeoName $child
     * @return Hierarchy
     */
    public function setChild(GeoName $child)
    {
        $this->child = $child;
        return $this;
    }



}

