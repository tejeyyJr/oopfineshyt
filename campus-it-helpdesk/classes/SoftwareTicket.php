<?php
/**
 * CHILD CLASS - Software / application problems
 */
class SoftwareTicket extends SupportTicket
{
    public function __construct($ticketId, $requesterName, $requesterType, $location, $description, $priority)
    {
        parent::__construct($ticketId, $requesterName, $requesterType, $location, $description, $priority, 'Software');
    }

    // METHOD OVERRIDING
    public function diagnose()
    {
        return 'Software check: review the installed software version, configuration files, error messages, '
            . 'required dependencies (such as ports used by XAMPP/Apache/MySQL), and compatibility with the '
            . 'operating system of the unit in ' . $this->location . '.';
    }

    // METHOD OVERRIDING
    public function getResolution()
    {
        return 'Reconfigure the application settings, free any conflicting port, update or repair the installation, '
            . 'and reinstall the software if the error persists.';
    }

    public function getTeam()
    {
        return 'Software & Systems Team';
    }
}
