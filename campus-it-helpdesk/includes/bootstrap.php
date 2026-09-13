<?php
/**
 * bootstrap.php
 * Loaded by every page. It:
 *  1. loads the classes,
 *  2. starts the session (so the ticket objects survive page changes),
 *  3. creates the TicketManager and the sample tickets on first visit.
 *
 * IMPORTANT: classes must be required BEFORE session_start() so that PHP can
 * rebuild the stored objects correctly.
 */

// Friendly errors: never dump raw warnings to the user.
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../classes/SupportTicket.php';
require_once __DIR__ . '/../classes/HardwareTicket.php';
require_once __DIR__ . '/../classes/SoftwareTicket.php';
require_once __DIR__ . '/../classes/NetworkTicket.php';
require_once __DIR__ . '/../classes/AccountAccessTicket.php';
require_once __DIR__ . '/../classes/TicketManager.php';
require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** Build the sample data (array of objects) used for the demonstration. */
function seedSampleTickets(TicketManager $manager)
{
    // ARRAY OF DIFFERENT CHILD OBJECTS -> polymorphism in action
    $samples = [
        new HardwareTicket($manager->generateTicketId(), 'Tommy Candia Jr.', 'Student', 'Computer Laboratory 1',
            'The computer in seat 12 does not turn on. No lights and no fan sound when the power button is pressed.', 'High'),
        new SoftwareTicket($manager->generateTicketId(), 'Maria Santos', 'Student', 'Computer Laboratory 2',
            'XAMPP Apache server is not starting. It shows a port 80 error every time I click start.', 'Medium'),
        new NetworkTicket($manager->generateTicketId(), 'John Dela Cruz', 'Instructor', 'Faculty Room',
            'Unable to connect to the campus Wi-Fi. The laptop keeps asking for the password again and again.', 'High'),
        new AccountAccessTicket($manager->generateTicketId(), 'Ana Reyes', 'Staff', 'Registrar',
            'Unable to access the school portal. It says the account is locked after several login attempts.', 'Critical'),
    ];

    foreach ($samples as $ticket) {
        $manager->addTicket($ticket);
    }

    // demonstrate the status workflow on the sample data
    $manager->updateTicketStatus('IT-0002', 'In Progress');
    $manager->updateTicketStatus('IT-0003', 'Resolved');
}

if (!isset($_SESSION['ticketManager']) || !($_SESSION['ticketManager'] instanceof TicketManager)) {
    $manager = new TicketManager();
    seedSampleTickets($manager);
    $_SESSION['ticketManager'] = $manager;
}

/** @var TicketManager $manager */
$manager = $_SESSION['ticketManager'];

/** Saves the manager back into the session after any change. */
function saveManager(TicketManager $manager)
{
    $_SESSION['ticketManager'] = $manager;
}

/** Reset everything back to the sample data. */
function resetHelpDesk()
{
    $fresh = new TicketManager();
    seedSampleTickets($fresh);
    $_SESSION['ticketManager'] = $fresh;
    return $fresh;
}
