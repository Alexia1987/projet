<?php

namespace Entity;

class Payment {

    private int $id;
    private int $booking_id;
    private float $total_paid;
    private string $payment_status;
    private string $paid_at;

    public function setId( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setBookingId( int $bookingId ) : void
    {
        $this->booking_id = $bookingId;
    }

    public function getBookingId() : int
    {
        return $this->booking_id;
    }

    public function setTotalPaid( int $totalPaid ) : void
    {
        $this->total_paid = $totalPaid;
    }

    public function getTotalPaid() : float
    {
        return $this->total_paid;
    }

    public function setPaymentStatus( int $paymentStatus ) : void
    {
        $this->payment_status = $paymentStatus;
    }

    public function getPaymentStatus() : string
    {
        return $this->payment_status;
    }

    public function setPaidAt( int $paidAt ) : void
    {
        $this->paid_at = $paidAt;
    }

    public function getPaidAt() : string
    {
        return $this->paid_at;
    }

}