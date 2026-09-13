<?php
require_once __DIR__ . '/includes/bootstrap.php';

$notice = '';
$error  = '';

// Quick status update straight from the list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'status') {
    $id     = isset($_POST['id']) ? $_POST['id'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    if ($manager->findTicket($id) === null) {
        $error = 'Ticket not found.';
    } elseif (!$manager->updateTicketStatus($id, $status)) {
        $error = 'Unable to process the request. That status is not valid.';
    } else {
        saveManager($manager);
        $notice = 'Ticket ' . $id . ' is now marked as ' . $status . '.';
    }
}

// Delete a ticket straight from the list
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if (!$manager->removeTicket($id)) {
        $error = 'Ticket not found.';
    } else {
        saveManager($manager);
        $notice = 'Ticket ' . $id . ' was deleted.';
    }
}

if (isset($_GET['deleted'])) {
    $notice = 'Ticket ' . $_GET['deleted'] . ' was deleted.';
}

$search        = isset($_GET['search']) ? trim($_GET['search']) : '';
$fCategory     = isset($_GET['category']) ? $_GET['category'] : 'All';
$fPriority     = isset($_GET['priority']) ? $_GET['priority'] : 'All';
$fStatus       = isset($_GET['status']) ? $_GET['status'] : 'All';
$fRequesterType= isset($_GET['requesterType']) ? $_GET['requesterType'] : 'All';

$results = $manager->filterTickets($search, $fCategory, $fPriority, $fStatus, $fRequesterType);

$pageTitle  = 'All Support Tickets';
$activePage = 'tickets';
include __DIR__ . '/includes/header.php';
?>

<section class="page-head">
  <div class="container">
    <h1>All Support Tickets</h1>
    <p></p>
  </div>
</section>

<section class="section">
  <div class="container">

    <?php if ($notice): ?><div class="alert alert-success" role="status"><?php echo e($notice); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error" role="alert"><?php echo e($error); ?></div><?php endif; ?>

    <form class="panel" method="get" action="tickets.php">
      <h2>Search &amp; Filter</h2>
      <div class="form-grid">
        <div class="field full">
          <label for="search">Search tickets</label>
          <input type="search" id="search" name="search" value="<?php echo e($search); ?>"
                 placeholder="Search by ticket ID, requester name or description...">
        </div>
        <div class="field">
          <label for="fcategory">Category</label>
          <select id="fcategory" name="category">
            <option value="All">All</option>
            <?php foreach (categories() as $c): ?>
              <option value="<?php echo e($c); ?>" <?php echo $fCategory === $c ? 'selected' : ''; ?>><?php echo e($c); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="fpriority">Priority</label>
          <select id="fpriority" name="priority">
            <option value="All">All</option>
            <?php foreach (priorities() as $p): ?>
              <option value="<?php echo e($p); ?>" <?php echo $fPriority === $p ? 'selected' : ''; ?>><?php echo e($p); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="fstatus">Status</label>
          <select id="fstatus" name="status">
            <option value="All">All</option>
            <?php foreach (statuses() as $s): ?>
              <option value="<?php echo e($s); ?>" <?php echo $fStatus === $s ? 'selected' : ''; ?>><?php echo e($s); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="ftype">Requester Type</label>
          <select id="ftype" name="requesterType">
            <option value="All">All</option>
            <?php foreach (requesterTypes() as $t): ?>
              <option value="<?php echo e($t); ?>" <?php echo $fRequesterType === $t ? 'selected' : ''; ?>><?php echo e($t); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Search</button>
        <a class="btn btn-light" href="tickets.php">Reset</a>
        <a class="btn btn-light" href="submit-ticket.php">New Ticket</a>
      </div>
    </form>

    <div class="panel">
      <h2><?php echo count($results); ?> ticket<?php echo count($results) === 1 ? '' : 's'; ?> found</h2>

      <?php if (empty($results)): ?>
        <p class="muted">No tickets match your search. Try resetting the filters.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Ticket ID</th><th>Requester</th><th>Type</th><th>Category</th>
                <th>Location</th><th>Priority</th><th>Status</th><th>Created</th><th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $t): ?>
                <tr>
                  <td><strong><?php echo e($t->getTicketId()); ?></strong><br><span class="muted"><?php echo e($t->getClassName()); ?></span></td>
                  <td><?php echo e($t->getRequesterName()); ?></td>
                  <td><span class="badge badge-type"><?php echo e($t->getRequesterType()); ?></span></td>
                  <td><?php echo e($t->getCategory()); ?></td>
                  <td><?php echo e($t->getLocation()); ?></td>
                  <td><span class="<?php echo priorityClass($t->getPriority()); ?>"><?php echo e($t->getPriority()); ?></span></td>
                  <td><span class="<?php echo statusClass($t->getStatus()); ?>"><?php echo e($t->getStatus()); ?></span></td>
                  <td><?php echo e(date('M j, Y', strtotime($t->getCreatedAt()))); ?></td>
                  <td>
                    <div class="actions">
                      <a class="btn btn-sm btn-light" href="ticket-details.php?id=<?php echo urlencode($t->getTicketId()); ?>">View</a>
                      <?php if ($t->getStatus() !== 'Closed'): ?>
                        <form method="post" action="tickets.php" style="display:inline">
                          <input type="hidden" name="action" value="status">
                          <input type="hidden" name="id" value="<?php echo e($t->getTicketId()); ?>">
                          <?php if ($t->getStatus() === 'Open'): ?>
                            <button class="btn btn-sm btn-warning" name="status" value="In Progress">Process</button>
                          <?php elseif ($t->getStatus() === 'In Progress'): ?>
                            <button class="btn btn-sm btn-success" name="status" value="Resolved">Resolve</button>
                          <?php else: ?>
                            <button class="btn btn-sm btn-danger" name="status" value="Closed">Close</button>
                          <?php endif; ?>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
