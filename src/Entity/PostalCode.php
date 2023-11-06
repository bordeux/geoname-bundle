<?php

namespace Bordeux\Bundle\GeoNameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostalCode
 *
 * @ORM\Table(name="geo__postal")
 * @ORM\Entity()
 */
class PostalCode
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
     * @var string iso country code, 2 characters
     *
     * @ORM\Column(name="country_code", type="string", length=2, nullable=true)
     */
    protected ?string $countryCode;

    /**
     * @var string iso country code, 2 characters
     *
     * @ORM\Column(name="postal_code", type="string", length=20, nullable=true)
     */
    protected ?string $postalCode;

    /**
     * @var string iso country code, 2 characters
     *
     * @ORM\Column(name="place_name", type="string", length=180, nullable=true)
     */
    protected ?string $placeName;


    /**
     * @var string 1. order subdivision (state)
     *
     * @ORM\Column(name="admin_name1", type="string", length=100, nullable=true)
     */
    protected ?string $adminName1;

    /**
     * @var string 1. order subdivision (state)
     *
     * @ORM\Column(name="admin_code1", type="string", length=20, nullable=true)
     */
    protected ?string $adminCode1;




    /**
     * @var string 1. order subdivision (county/province)
     *
     * @ORM\Column(name="admin_name2", type="string", length=100, nullable=true)
     */
    protected ?string $adminName2;

    /**
     * @var string 1. order subdivision (county/province)
     *
     * @ORM\Column(name="admin_code2", type="string", length=20, nullable=true)
     */
    protected ?string $adminCode2;

    /**
     * @var string 1. order subdivision (community)
     *
     * @ORM\Column(name="admin_name3", type="string", length=100, nullable=true)
     */
    protected ?string $adminName3;

    /**
     * @var string 1. order subdivision (community)
     *
     * @ORM\Column(name="admin_code3", type="string", length=20, nullable=true)
     */
    protected ?string $adminCode3;



    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of countryCode
     *
     * @return ?string
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * Set the value of countryCode
     *
     * @param ?string $countryCode
     *
     * @return self
     */
    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Get the value of postalCode
     *
     * @return ?string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Set the value of postalCode
     *
     * @param ?string $postalCode
     *
     * @return self
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }


    /**
     * Get the value of placeName
     *
     * @return ?string
     */
    public function getPlaceName(): ?string
    {
        return $this->placeName;
    }

    /**
     * Set the value of placeName
     *
     * @param ?string $placeName
     *
     * @return self
     */
    public function setPlaceName(?string $placeName): self
    {
        $this->placeName = $placeName;
        return $this;
    }

    /**
     * Get the value of adminName1
     *
     * @return ?string
     */
    public function getAdminName1(): ?string
    {
        return $this->adminName1;
    }

    /**
     * Set the value of adminName1
     *
     * @param ?string $adminName1
     *
     * @return self
     */
    public function setAdminName1(?string $adminName1): self
    {
        $this->adminName1 = $adminName1;
        return $this;
    }

    /**
     * Get the value of adminCode1
     *
     * @return ?string
     */
    public function getAdminCode1(): ?string
    {
        return $this->adminCode1;
    }

    /**
     * Set the value of adminCode1
     *
     * @param ?string $adminCode1
     *
     * @return self
     */
    public function setAdminCode1(?string $adminCode1): self
    {
        $this->adminCode1 = $adminCode1;
        return $this;
    }

    /**
     * Get the value of adminName2
     *
     * @return ?string
     */
    public function getAdminName2(): ?string
    {
        return $this->adminName2;
    }

    /**
     * Set the value of adminName2
     *
     * @param ?string $adminName2
     *
     * @return self
     */
    public function setAdminName2(?string $adminName2): self
    {
        $this->adminName2 = $adminName2;
        return $this;
    }

    /**
     * Get the value of adminCode2
     *
     * @return ?string
     */
    public function getAdminCode2(): ?string
    {
        return $this->adminCode2;
    }

    /**
     * Set the value of adminCode2
     *
     * @param ?string $adminCode2
     *
     * @return self
     */
    public function setAdminCode2(?string $adminCode2): self
    {
        $this->adminCode2 = $adminCode2;
        return $this;
    }

    /**
     * Get the value of adminName3
     *
     * @return ?string
     */
    public function getAdminName3(): ?string
    {
        return $this->adminName3;
    }

    /**
     * Set the value of adminName3
     *
     * @param ?string $adminName3
     *
     * @return self
     */
    public function setAdminName3(?string $adminName3): self
    {
        $this->adminName3 = $adminName3;
        return $this;
    }

    /**
     * Get the value of adminCode3
     *
     * @return ?string
     */
    public function getAdminCode3(): ?string
    {
        return $this->adminCode3;
    }

    /**
     * Set the value of adminCode3
     *
     * @param ?string $adminCode3
     *
     * @return self
     */
    public function setAdminCode3(?string $adminCode3): self
    {
        $this->adminCode3 = $adminCode3;
        return $this;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->placeName  . ' ( ' . $this->postalCode  . ' )';
    }
}
