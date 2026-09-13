<?php
/**
 * CHILD CLASS - Network / connectivity problems
 */
class NetworkTicket extends SupportTicket
{
    public function __construct($ticketId, $requesterName, $requesterType, $location, $description, $priority)
    {
        parent::__construct($ticketId, $requesterName, $requesterType, $location, $description, $priority, 'Network');
    }

    // METHOD OVERRIDING
    public function diagnose()
    {
        return 'Network check: verify Wi-Fi signal and credentials, IP configuration, network adapter status, '
            . 'LAN cable condition, and the availability of the access point or switch serving '
            . $this->location . '.';
    }

    // METHOD OVERRIDING
    public function getResolution()
    {
        return 'Reconnect the device, renew the IP configuration, replace a damaged LAN cable, '
            . 'and escalate to IT personnel when the access point or campus link itself is down.';
    }

    public function getTeam()
    {
        return 'Network Operations Team';
    }
}
