<?php

namespace SDU\MFA\Azure;

use Illuminate\Contracts\Auth\Authenticatable;
use SDU\MFA\MFARole;
use SDU\MFA\MFARoleCollection;

class User implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    public string $id;

    private ?string $displayName;

    private ?string $givenName;

    private ?string $surname;

    private ?string $jobTitle;

    private ?string $mail;

    private ?string $officeLocation;

    private ?string $preferredLanguage;

    private ?string $userPrincipalName;

    private $mobilePhone;

    private GroupCollection $groupCollection;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param mixed $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param mixed $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getOfficeLocation()
    {
        return $this->officeLocation;
    }

    /**
     * @param mixed $officeLocation
     */
    public function setOfficeLocation($officeLocation)
    {
        $this->officeLocation = $officeLocation;
    }

    /**
     * @return mixed
     */
    public function getPreferredLanguage()
    {
        return $this->preferredLanguage;
    }

    /**
     * @param mixed $preferredLanguage
     */
    public function setPreferredLanguage($preferredLanguage)
    {
        $this->preferredLanguage = $preferredLanguage;
    }

    /**
     * @return mixed
     */
    public function getUserPrincipalName()
    {
        return $this->userPrincipalName;
    }

    /**
     * @param mixed $userPrincipalName
     */
    public function setUserPrincipalName($userPrincipalName)
    {
        $this->userPrincipalName = $userPrincipalName;
    }

    /**
     * @return mixed
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param mixed $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;
    }


    public function getAuthIdentifierName()
    {
        return "$this->givenName $this->surname";
    }

    public function getAuthIdentifier()
    {
        return "id";
    }

    public function getAuthPassword()
    {
        return null;
    }

    /**
     * @return GroupCollection
     */
    public function getGroupCollection() : GroupCollection
    {
        return $this->groupCollection;
    }

    /**
     * @param GroupCollection $groupCollection
     */
    public function setGroupCollection(GroupCollection $groupCollection) : void
    {
        $this->groupCollection = $groupCollection;
    }

    public function hasAccess(array $groups) :  bool
    {
        $access = true;
        foreach ($groups as $group)
            $access &= $this->groupCollection->contains($group);

        return $access;
    }
}