<?php

namespace Entity;

use Entity\Session;
use Entity\Vehicle;

class SessionVehicle {

    private int $id;
    private int $vehicle_id;
    private int $session_id;

    public function setId( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setVehicleId( int $vehicleId ) : void
    {
        $this->vehicle_id = $vehicleId;
    }

    public function getVehicleId() : int
    {
        return $this->vehicle_id;
    }

    public function setSessionId( int $sessionId ) : void
    {
        $this->session_id = $sessionId;
    }

    public function getSessionId() : int
    {
        return $this->session_id;
    }
}