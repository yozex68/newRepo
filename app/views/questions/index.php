<?php use App\Helpers\Helper; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">Help Desk & Q&A Board</h1>
        <p class="text-secondary small mb-0">Ask administrators academic questions regarding learning materials and study programmes.</p>
    </div>
    
    <?php if ($session->get('user_role') !== 'admin'): ?>
        <!-- Button trigger to open new ticket -->
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal">
            <i class="fas fa-plus-circle me-1"></i> Open Support Ticket
        </button>
    <?php endif; ?>
</div>

<!-- Ticket Directory Roster -->
<div class="card shadow-sm border-0 bg-white">
    <div class="card-header bg-white border-bottom py-3">
        <h2 class="h5 fw-bold mb-0 text-dark"><i class="fas fa-list-ul text-primary me-2"></i> Support Tickets Catalog</h2>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($questions)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Ticket ID</th>
                            <th>Subject / Inquiry</th>
                            <?php if ($session->get('user_role') === 'admin'): ?>
                                <th>Student Sender</th>
                            <?php endif; ?>
                            <th>Replies Count</th>
                            <th>Status</th>
                            <th>Date Opened</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $q): ?>
                            <?php 
                            $statusColor = ($q['status'] === 'pending') ? 'badge-blue' : 'badge-green';
                            ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-secondary">#<?= $q['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark mb-0"><?= Helper::esc($q['title']) ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 300px;"><?= Helper::esc($q['content']) ?></small>
                                </td>
                                <?php if ($session->get('user_role') === 'admin'): ?>
                                    <td>
                                        <div class="fw-semibold small"><?= Helper::esc($q['user_name']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.75rem;"><?= Helper::esc($q['user_email']) ?></div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <span class="badge bg-secondary"><?= $q['replies_count'] ?> replies</span>
                                </td>
                                <td>
                                    <span class="badge <?= $statusColor ?>"><?= ucfirst($q['status']) ?></span>
                                </td>
                                <td class="text-secondary small"><?= date('Y-m-d H:i', strtotime($q['created_at'])) ?></td>
                                <td class="pe-4 text-end">
                                    <a href="<?= Helper::url('/questions/' . $q['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-comments me-1"></i> Open Thread
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-secondary">
                <i class="fas fa-question-circle fs-1 mb-3"></i>
                <h3 class="h5 fw-bold">No Active Tickets</h3>
                <p class="mb-0">There are no queued inquiries under this account. Open a ticket to ask for help.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Open Support Ticket Modal (Student Only) -->
<?php if ($session->get('user_role') !== 'admin'): ?>
    <div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="newTicketModalLabel"><i class="fas fa-plus-circle me-2"></i> Open Support Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= Helper::url('/questions/create') ?>" method="POST">
                    <?= Helper::csrf_field() ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="title" class="form-label small fw-semibold text-secondary">Inquiry Subject</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Broken links in BIT-2101 notes" required minlength="5" maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label small fw-semibold text-secondary">Explain your Question</label>
                            <textarea class="form-control" id="content" name="content" rows="5" placeholder="Detail the issue, including course code, module title, and what you need help with." required minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
