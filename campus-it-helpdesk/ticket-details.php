<?php
require_once __DIR__ . '/includes/bootstrap.php';

$notice = '';
$error  = '';

$id = isset($_GET['id']) ? trim($_GET['id']) : (isset($_POST['id']) ? trim($_POST['id']) : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!$manager->removeTicket($id)) {
        $error = 'Ticket not found.';
    } else {
        saveManager($manager);
        header('Location: tickets.php?deleted=' . urlencode($id));
        exit;
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!$manager->removeTicket($id)) {
        $error = 'Ticket not found.';
    } else {
        saveManager($manager);
        header('Location: tickets.php?deleted=' . urlencode($id));
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    if ($manager->findTicket($id) === null) {
        $error = 'Ticket not found.';
    } elseif (!$manager->updateTicketStatus($id, $status)) {
        $error = 'Unable to process the request. That status is not valid.';
    } else {
        saveManager($manager);
        $notice = 'Status updated to ' . $status . '.';
    }
}

$ticket = $id === '' ? null : $manager->findTicket($id);

$pageTitle  = 'Ticket Details';
$activePage = 'tickets';
include __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <h1>Ticket Details</h1>
    <p>Full information about the selected support ticket, including the diagnosis and recommended resolution produced by the object itself.</p>
  </div>
</section>

<section class="section">
  <div class="container">

    <?php if ($ticket === null): ?>
      <div class="alert alert-error" role="alert">
        <strong>Ticket not found.</strong> The ticket ID you opened does not exist in the current session.
      </div>
      <p><a class="btn btn-primary" href="tickets.php">Back to All Tickets</a></p>
    <?php else: ?>

      <?php if ($notice): ?><div class="alert alert-success" role="status"><?php echo e($notice); ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error" role="alert"><?php echo e($error); ?></div><?php endif; ?>

      <div class="panel">
        <h2><?php echo e($ticket->getTicketId()); ?>
          <span class="<?php echo statusClass($ticket->getStatus()); ?>"><?php echo e($ticket->getStatus()); ?></span>
          <span class="<?php echo priorityClass($ticket->getPriority()); ?>"><?php echo e($ticket->getPriority()); ?></span>
        </h2>
        <p class="muted">Object class: <code class="inline"><?php echo e($ticket->getClassName()); ?></code> extends <code class="inline">SupportTicket</code></p>

        <div class="detail-list">
          <div class="item"><p class="k">Ticket ID</p><p class="v"><?php echo e($ticket->getTicketId()); ?></p></div>
          <div class="item"><p class="k">Requester</p><p class="v"><?php echo e($ticket->getRequesterName()); ?></p></div>
          <div class="item"><p class="k">Requester Type</p><p class="v"><?php echo e($ticket->getRequesterType()); ?></p></div>
          <div class="item"><p class="k">Location</p><p class="v"><?php echo e($ticket->getLocation()); ?></p></div>
          <div class="item"><p class="k">Category</p><p class="v"><?php echo e($ticket->getCategory()); ?></p></div>
          <div class="item"><p class="k">Priority</p><p class="v"><?php echo e($ticket->getPriority()); ?></p></div>
          <div class="item"><p class="k">Status</p><p class="v"><?php echo e($ticket->getStatus()); ?></p></div>
          <div class="item"><p class="k">Created</p><p class="v"><?php echo e(formatDate($ticket->getCreatedAt())); ?></p></div>
          <div class="item"><p class="k">Assigned Team</p><p class="v"><?php echo e($ticket->getTeam()); ?></p></div>
        </div>

        <h3 style="margin-top:1.4rem">Description</h3>
        <p><?php echo nl2br(e($ticket->getDescription())); ?></p>

        <div class="result-box">
          <h3>Diagnosis</h3>
          <p style="margin:0"><?php echo e($ticket->diagnose()); ?></p>
          <p class="muted" style="margin:.5rem 0 0">Produced by <?php echo e($ticket->getClassName()); ?></p>
        </div>

        <div class="result-box resolution">
          <h3>Recommended Resolution</h3>
          <p style="margin:0"><?php echo e($ticket->getResolution()); ?></p>
          <p class="muted" style="margin:.5rem 0 0">Produced by <?php echo e($ticket->getClassName()); ?></p>
        </div>

        <p class="muted"> <?php echo e($ticket->getSummary()); ?></p>
      </div>

      <div class="panel">
        <h2>Update Status</h2>
        <p class="muted"><code class="inline"></code> 
          <code class="inline"></code><code class="inline"></code>.</p>

        <form method="post" action="ticket-details.php">
          <input type="hidden" name="id" value="<?php echo e($ticket->getTicketId()); ?>">
          <div class="form-actions" style="margin-top:.4rem">
            <button class="btn btn-light"   name="status" value="Open"        <?php echo $ticket->getStatus() === 'Open' ? 'disabled' : ''; ?>>Mark Open</button>
            <button class="btn btn-warning" name="status" value="In Progress" <?php echo $ticket->getStatus() === 'In Progress' ? 'disabled' : ''; ?>>Mark In Progress</button>
            <button class="btn btn-success" name="status" value="Resolved"    <?php echo $ticket->getStatus() === 'Resolved' ? 'disabled' : ''; ?>>Mark Resolved</button>
            <button class="btn btn-danger"  name="status" value="Closed"      <?php echo $ticket->getStatus() === 'Closed' ? 'disabled' : ''; ?>>Close Ticket</button>
          </div>
        </form>

                      <p style="margin-top:1.1rem">
          <a class="btn btn-light" href="tickets.php">Back to All Tickets</a>
        </p>
      </div>

      <div class="panel">
        <h2>Delete Ticket</h2>
        <p class="muted">This permanently removes the ticket from the current session. This cannot be undone.</p>
        <form method="post" action="ticket-details.php"
              onsubmit="return confirm('Delete ticket <?php echo e($ticket->getTicketId()); ?>? This cannot be undone.');">
          <input type="hidden" name="id" value="<?php echo e($ticket->getTicketId()); ?>">
          <input type="hidden" name="action" value="delete">
          <button class="btn btn-danger" type="submit">Delete Ticket</button>
        </form>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
