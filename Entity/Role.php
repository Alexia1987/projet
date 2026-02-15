<?php

namespace Entity;

class Role {

    private int $id;
    private string $name;
    private string $description;
    private string $created_at;

    public function setId ( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId () : int 
    {
        return $this->id;
    }

    public function setName ( string $name ) : void
    {
        $this->name = $name;
    }

    public function getName () : string
    {
        return $this->name;
    }

    public function setDescription ( string $description ) : void
    {
        $this->description = $description;
    }

    public function getDescription () : string
    {
        return $this->description;
    }

    public function setCreatedAt( string $createdAt ) : void
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt() : string
    {
        return $this->created_at;
    }
}