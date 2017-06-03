<?php

namespace Jet\Modules\Team\Models;

use Doctrine\Common\Collections\ArrayCollection;
use JetFire\Db\Model;
use Doctrine\ORM\Mapping;

/**
 * Class TeamRole
 * @package Jet\Models
 * @Entity(repositoryClass="Jet\Modules\Team\Models\TeamRoleRepository")
 * @Table(name="team_roles")
 * @HasLifecycleCallbacks
 */
class TeamRole extends Model implements \JsonSerializable
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="string")
     */
    protected $name;
    /**
     * @Column(type="string")
     */
    protected $slug;
    /**
     * @ManyToOne(targetEntity="Jet\Models\Website")
     * @JoinColumn(name="website_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $website;
    /**
     * @ManyToMany(targetEntity="Team", mappedBy="roles")
     */
    protected $teams;
    /**
     * @Column(type="datetime")
     */
    public $created_at;
    /**
     * @Column(type="datetime", nullable=true)
     */
    public $updated_at;

    /**
     * TeamRole constructor.
     */
    public function __construct() {
        $this->teams = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param  $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param $teams
     */
    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    /**
     * @param Team $team
     */
    public function addTeam(Team $team)
    {
        $team->addRole($this);
        $this->teams[] = $team;
    }


    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @PrePersist
     */
    public function onPrePersist(){
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
    }
    /**
     * @PreUpdate
     */
    public function onPreUpdate(){
        $this->setUpdatedAt(new \DateTime('now'));
    }



    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return  [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'teams' => $this->getTeams(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}