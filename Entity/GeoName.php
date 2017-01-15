<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoName
 *
 * @ORM\Table(name="geo__name")
 * @ORM\Entity(repositoryClass="Bordeux\Bundle\GeoNameBundle\Repository\GeoNameRepository")
 */
class GeoName
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ascii_name", type="string", length=200, nullable=true)
     */
    protected $asciiName;


    /**
     * @var float
     * @ORM\Column(name="latitude", type="float", scale=6, precision=9, nullable=true)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", scale=6, precision=9, nullable=true)
     */
    protected $longitude;


    /**
     * @var float
     *
     * @ORM\Column(name="feature_class", type="string", length=1, nullable=true)
     */
    protected $featureClass;

    /**
     * @var float
     *
     * @ORM\Column(name="feature_code", type="string", length=10, nullable=true)
     */
    protected $featureCode;

    /**
     * @var float
     *
     * @ORM\Column(name="country_code", type="string", length=2, nullable=true)
     */
    protected $countryCode;

    /**
     * @var float
     *
     * @ORM\Column(name="cc2", type="string", length=200, nullable=true)
     */
    protected $cc2;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin1_id", referencedColumnName="id", nullable=true)
     */
    protected $admin1;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin2_id", referencedColumnName="id", nullable=true)
     */
    protected $admin2;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin3_id", referencedColumnName="id", nullable=true)
     */
    protected $admin3;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Administrative")
     * @ORM\JoinColumn(name="admin4_id", referencedColumnName="id", nullable=true)
     */
    protected $admin4;


    /**
     * @var int
     *
     * @ORM\Column(name="population", type="bigint", nullable=true)
     */
    protected $population;

    /**
     * @var int
     *
     * @ORM\Column(name="elevation", type="integer", nullable=true)
     */
    protected $elevation;

    /**
     * @var integer
     *
     * @ORM\Column(name="dem", type="integer", nullable=true)
     */
    protected $dem;

    /**
     * @var Administrative
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\Timezone")
     * @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=true)
     */
    protected $timezone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modification_date", type="date", nullable=true)
     */
    protected $modificationDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

