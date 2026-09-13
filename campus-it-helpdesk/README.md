# CAMPUS IT HELP DESK
### An Object-Oriented IT Support Ticket Management System
**Report. Diagnose. Resolve.**

A PHP OOP midterm project. **No database is used** — ticket data lives in PHP objects
stored in the session for the duration of the run.

## How to run (XAMPP)

1. Copy the whole `campus-it-helpdesk` folder into `C:\xampp\htdocs\`.
2. Start **Apache** in the XAMPP Control Panel (MySQL is NOT needed).
3. Open `http://localhost/campus-it-helpdesk/` in your browser.

Requires PHP 5.6 or newer (PHP 7/8 recommended).

## Pages

| File | Purpose |
|------|---------|
| `index.php` | Dashboard: hero, statistics cards, status/category charts, recent tickets |
| `submit-ticket.php` | Ticket form, validation, object creation, diagnosis + resolution result |
| `tickets.php` | All tickets table with search, filters and quick status updates |
| `ticket-details.php` | Full ticket details, diagnosis, resolution, status buttons |
| `oop-demo.php` | Inheritance, polymorphism, encapsulation, constructors, overriding |
| `uml.php` | UML / class diagram (visual + text version) |
| `testing.php` | 10 live test cases with expected vs actual results |
| `about.php` | Overview, objectives, scope, limitations, checklist, file structure |

## OOP structure

```
SupportTicket  (parent)
 ├── HardwareTicket
 ├── SoftwareTicket
 ├── NetworkTicket
 └── AccountAccessTicket

TicketManager  (holds the array of ticket objects)
```

- **Inheritance** — every child uses `extends SupportTicket`.
- **Polymorphism** — one array of mixed child objects, one `diagnose()` call, four different answers.
- **Method overriding** — `diagnose()`, `getResolution()`, `getTeam()`.
- **Encapsulation** — `protected` ticket details, `private $status` / `$createdAt`, `public` methods.
- **Constructors** — `__construct()` in the parent, `parent::__construct()` in every child.

## Resetting the demo data

On the Dashboard click **Reset Demo Data** to go back to the four sample tickets
(IT-0001 to IT-0004).
