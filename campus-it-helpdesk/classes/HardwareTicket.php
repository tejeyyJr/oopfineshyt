<?php
/**
 * CHILD CLASS - Hardware problems
 * Uses "extends" to inherit everything from SupportTicket,
 * then OVERRIDES diagnose() and getResolution().
 */
class HardwareTicket extends SupportTicket
{
    public function __construct($ticketId, $requesterName, $requesterType, $location, $description, $priority)
    {
        // call the parent constructor and fix the category
        parent::__construct($ticketId, $requesterName, $requesterType, $location, $description, $priority, 'Hardware');
    }

    // METHOD OVERRIDING
    public function diagnose()
    {
        return 'Hardware check: inspect the power supply, power cable, outlet, monitor cable, '
            . 'keyboard/mouse connections, internal components and indicator lights of the reported device in '
            . $this->location . '.';
    }

    // METHOD OVERRIDING
    public function getResolution()
    {
        return 'Inspect the defective hardware, reseat or replace the faulty cable or component, '
            . 'and if the unit is still not working, tag the equipment for repair or replacement by IT personnel.';
    }

    public function getTeam()
    {
        return 'Hardware Maintenance Team';
    }
}
