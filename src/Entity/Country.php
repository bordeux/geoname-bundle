<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;

/**
 * GeoName
 *
 * @ORM\Table(name="geo__country" ,indexes={
 *     @ORM\Index(name="geoname_country_search_idx", columns={"name", "iso"})
 * })
 * @ORM\Entity()
 */
class Country implements Stringable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="iso", type="string", length=2, nullable=false)
     */
    protected string $iso;

    /**
     * @var string
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=false)
     */
    protected string $iso3;

    /**
     * @var integer
     *
     * @ORM\Column(name="iso_numeric", type="integer", length=3, nullable=false)
     */
    protected int $isoNumeric;


    /**
     * @var string
     *
     * @ORM\Column(name="fips", type="string", length=2, nullable=true)
     */
    protected ?string $fips;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=255, nullable=true)
     */
    protected ?string $capital;


    /**
     * @var integer
     *
     * @ORM\Column(name="area", type="bigint", nullable=false)
     */
    protected int $area;

    /**
     * @var int
     *
     * @ORM\Column(name="population", type="bigint", nullable=false)
     */
    protected int $population;

    /**
     * @var string
     *
     * @ORM\Column(name="tld", type="string", length=15, nullable=true)
     */
    protected ?string $tld;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    protected ?string $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_name", type="string", length=50, nullable=true)
     */
    protected ?string $currencyName;


    /**
     * @var int
     *
     * @ORM\Column(name="phone_prefix", type="integer", nullable=true)
     */
    protected ?int $phonePrefix;


    /**
     * @var string
     *
     * @ORM\Column(name="postal_format", type="text", nullable=true)
     */
    protected ?string $postalFormat;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_regex", type="text", nullable=true)
     */
    protected ?string $postalRegex;


    /**
     * @var array
     *
     * @ORM\Column(name="languages", type="json", nullable=true)
     */
    protected ?array $languages;

    /**
     * @var GeoName
     *
     * @ORM\ManyToOne(targetEntity="Bordeux\Bundle\GeoNameBundle\Entity\GeoName")
     * @ORM\JoinColumn(name="geoname_id", referencedColumnName="id", nullable=true)
     */
    protected ?GeoName $geoName;

    /**
     * Country constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIso(): string
    {
        return $this->iso;
    }

    /**
     * @param string $iso
     * @return $this
     */
    public function setIso(string $iso): self
    {
        $this->iso = $iso;
        return $this;
    }

    /**
     * @return string
     */
    public function getIso3(): string
    {
        return $this->iso3;
    }

    /**
     * @param string $iso3
     * @return $this
     */
    public function setIso3(string $iso3): self
    {
        $this->iso3 = $iso3;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsoNumeric(): int
    {
        return $this->isoNumeric;
    }

    /**
     * @param int $isoNumeric
     * @return $this
     */
    public function setIsoNumeric(int $isoNumeric): self
    {
        $this->isoNumeric = $isoNumeric;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFips(): ?string
    {
        return $this->fips;
    }

    /**
     * @param string|null $fips
     * @return $this
     */
    public function setFips(?string $fips): self
    {
        $this->fips = $fips;
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
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getArea(): int
    {
        return $this->area;
    }

    /**
     * @param int $area
     * @return $this
     */
    public function setArea(int $area): self
    {
        $this->area = (int)$area;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getCapital(): ?string
    {
        return $this->capital;
    }

    /**
     * @param string|null $capital
     * @return $this
     */
    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;
        return $this;
    }


    /**
     * @return int
     */
    public function getPopulation(): int
    {
        return $this->population;
    }

    /**
     * @param int $population
     * @return $this
     */
    public function setPopulation(int $population): self
    {
        $this->population = (int)$population;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTld(): ?string
    {
        return $this->tld;
    }

    /**
     * @param string|null $tld
     * @return $this
     */
    public function setTld(?string $tld): self
    {
        $this->tld = $tld;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     * @return $this
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    /**
     * @param string|null $currencyName
     * @return $this
     */
    public function setCurrencyName(?string $currencyName): self
    {
        $this->currencyName = $currencyName;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPhonePrefix(): ?int
    {
        return $this->phonePrefix;
    }

    /**
     * @param int|null $phonePrefix
     * @return $this
     */
    public function setPhonePrefix(?int $phonePrefix): self
    {
        $this->phonePrefix = $phonePrefix;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalFormat(): ?string
    {
        return $this->postalFormat;
    }

    /**
     * @param $postalFormat
     * @return $this
     */
    public function setPostalFormat(?string $postalFormat): self
    {
        $this->postalFormat = $postalFormat;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalRegex(): ?string
    {
        return $this->postalRegex;
    }

    /**
     * @param string|null $postalRegex
     * @return $this
     */
    public function setPostalRegex(?string $postalRegex): self
    {
        $this->postalRegex = $postalRegex;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * @param array|null $languages
     * @return $this
     */
    public function setLanguages(?array $languages): self
    {
        $this->languages = $languages;
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
