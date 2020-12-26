<?php


namespace AmberCore\Helper;


use Doctrine\ORM\Mapping as ORM;

trait EntityDateTimeTrait
{
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(property_exists(static::class, 'created_at'))
        {
            $this->created_at = time();
        }
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAtValue()
    {
        if(property_exists(static::class, 'updated_at'))
        {
            $this->updated_at = time();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function setDeletedAtValue()
    {
        if(property_exists(static::class, 'deleted_at'))
        {
            $this->deleted_at = 0;
        }
    }
}