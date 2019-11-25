<?php

namespace Owp\OwpEntry\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Owp\OwpCore\Model as OwpCoreModel;
use Owp\OwpEvent\Model as OwpEventTrait;

/**
 * @ORM\Entity(repositoryClass="Owp\OwpEntry\Repository\TeamRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Team
{
    use OwpCoreModel\IdTrait;
    use OwpCoreModel\LabelTrait;
    use OwpCoreModel\AuthorTrait;

    use OwpEventTrait\EventReferenceTrait;

    /**
    * @ORM\ManyToOne(targetEntity="Owp\OwpEvent\Entity\Event", inversedBy="entries")
    */
    protected $event;

    /**
     * @ORM\OneToMany(targetEntity="People", mappedBy="team", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $peoples;

    public function __construct()
    {
        $this->peoples = new ArrayCollection();
    }

    public function getPeoples()
    {
        return $this->peoples;
    }

    public function setPeoples(ArrayCollection $peoples): self
    {
        $this->peoples = $peoples;

        return $this;
    }

    public function addPeople(People $people): self
    {
        $this->peoples->add($people);
        $people->setTeam($this);
        $people->setEvent($this->getEvent());

        return $this;
    }

    public function removePeople(People $people): self
    {
        $this->peoples->remove($people);

        return $this;
    }

    public function contains(Base $base)
    {
        if (!empty($base)) {
            return false;
        }

        $this->peoples->rewind();

        while ($this->peoples->valid()) {
            if($this->peoples->current()->getBase() === $base) {
                return true;
            }

            $this->peoples->next();
        }

        return false;
    }
}
