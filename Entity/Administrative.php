<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Administrative
 *
 * @ORM\Table(name="geo__administrative")
 * @ORM\Entity(repositoryClass="Bordeux\Bundle\GeoNameBundle\Repository\AdministrativeRepository")
 */
class Administrative
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
     * @ORM\Column(name="code", type="string", length=30, unique=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ascii_name", type="string", length=200, nullable=true)
     */
    protected $asciiName;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="geoname_id", referencedColumnName="id", nullable=true)
     */
    protected $geoName;


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $code
     * @return Administrative
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return Administrative
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @return string
     */
    public function getAsciiName()
    {
        return $this->asciiName;
    }

    /**
     * @author Chris Bednarczyk <chris@tourradar.com>
     * @param string $asciiName
     * @return Administrative
     */
    public function setAsciiName($asciiName)
    {
        $this->asciiName = $asciiName;
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
     * @return Administrative
     */
    public function setGeoName($geoName)
    {
        $this->geoName = $geoName;
        return $this;
    }


}

