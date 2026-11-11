const searchInput = document.querySelector('#asset-search');
const statusFilter = document.querySelector('#status-filter');
const tableBody = document.querySelector('#asset-table-body');
const emptyState = document.querySelector('#empty-state');
const visibleCount = document.querySelector('#visible-count');
const totalCount = document.querySelector('#total-count');
const modal = document.querySelector('#asset-modal');
const toast = document.querySelector('#toast');
const assignmentSearch = document.querySelector('#assignment-search');
const assignmentFilter = document.querySelector('#assignment-filter');
const assignmentTableBody = document.querySelector('#assignment-table-body');
const assignmentEmptyState = document.querySelector('#assignment-empty-state');
const assignmentVisibleCount = document.querySelector('#assignment-visible-count');
const managerModal = document.querySelector('#manager-action-modal');
const managerActionForm = document.querySelector('#manager-action-form');
let activeAssignmentRow = null;
const employeeAssetSearch = document.querySelector('#employee-asset-search');
const employeeAssetTable = document.querySelector('#employee-asset-table-body');
const employeeAssetsEmpty = document.querySelector('#employee-assets-empty');
const employeeVisibleCount = document.querySelector('#employee-visible-count');
const employeeRequestCount = document.querySelector('#employee-request-count');
const employeeAssetModal = document.querySelector('#employee-asset-modal');
const employeeIssueModal = document.querySelector('#employee-issue-modal');

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const passwordInput = document.getElementById(button.getAttribute('aria-controls'));
        const isVisible = passwordInput.type === 'text';

        passwordInput.type = isVisible ? 'password' : 'text';
        button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        button.setAttribute('aria-pressed', String(!isVisible));
        button.querySelector('[data-eye-open]').hidden = !isVisible;
        button.querySelector('[data-eye-closed]').hidden = isVisible;
    });
});

function filterAssets() {
    if (!tableBody) return;

    const query = searchInput.value.trim().toLowerCase();
    const status = statusFilter.value;
    let visible = 0;

    tableBody.querySelectorAll('tr').forEach((row) => {
        const matchesQuery = row.textContent.toLowerCase().includes(query);
        const matchesStatus = !status || row.dataset.status === status;
        const isVisible = matchesQuery && matchesStatus;
        row.hidden = !isVisible;
        visible += Number(isVisible);
    });

    visibleCount.textContent = String(visible);
    emptyState.hidden = visible !== 0;
}

let toastTimeout;

function showToast(message) {
    toast.textContent = message;
    toast.classList.add('is-visible');
    window.clearTimeout(toastTimeout);
    toastTimeout = window.setTimeout(() => toast.classList.remove('is-visible'), 2800);
}

function closeModal() {
    modal.hidden = true;
}

searchInput?.addEventListener('input', filterAssets);
statusFilter?.addEventListener('change', filterAssets);

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        modal.hidden = false;
        modal.querySelector('input')?.focus();
    });
});

document.querySelectorAll('[data-close-modal]').forEach((button) => button.addEventListener('click', closeModal));

modal?.addEventListener('click', (event) => {
    if (event.target === modal) closeModal();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal && !modal.hidden) closeModal();
    if (event.key === 'Escape' && managerModal && !managerModal.hidden) closeManagerModal();
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        (searchInput ?? assignmentSearch)?.focus();
    }
});

document.querySelector('.menu-toggle')?.addEventListener('click', (event) => {
    const button = event.currentTarget;
    const sidebar = document.querySelector('#sidebar');
    const isOpen = sidebar.classList.toggle('is-open');
    button.setAttribute('aria-expanded', String(isOpen));
});

document.querySelectorAll('.nav-link').forEach((link) => {
    link.addEventListener('click', () => {
        if (link.dataset.openEmployeeModal) {
            openEmployeeModal(link.dataset.openEmployeeModal);
        }

        document.querySelector('.nav-link.active')?.classList.remove('active');
        document.querySelector('.nav-link[aria-current="page"]')?.removeAttribute('aria-current');
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
        document.querySelector('#sidebar')?.classList.remove('is-open');
        document.querySelector('.menu-toggle')?.setAttribute('aria-expanded', 'false');

        if (link.dataset.assignmentFilter && assignmentFilter) {
            assignmentFilter.value = link.dataset.assignmentFilter;
            filterAssignments();
        }
    });
});

document.querySelector('#export-assets')?.addEventListener('click', () => {
    const rows = [...tableBody.querySelectorAll('tr:not([hidden])')];
    const csvRows = [['Asset', 'Asset code', 'Category', 'Serial number', 'Status']];
    rows.forEach((row) => {
        const cells = row.querySelectorAll('td');
        csvRows.push([
            cells[0].querySelector('strong').textContent,
            cells[0].querySelector('small').textContent,
            cells[1].textContent,
            cells[2].textContent,
            cells[4].textContent.trim(),
        ]);
    });
    const csv = csvRows.map((row) => row.map((value) => `"${value.replaceAll('"', '""')}"`).join(',')).join('\r\n');
    const download = document.createElement('a');
    download.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    download.download = 'asset-registry.csv';
    download.click();
    URL.revokeObjectURL(download.href);
    showToast(`Exported ${rows.length} ${rows.length === 1 ? 'asset' : 'assets'} to CSV.`);
});

function filterAssignments() {
    if (!assignmentTableBody) return;

    const query = assignmentSearch.value.trim().toLowerCase();
    const status = assignmentFilter.value;
    let visible = 0;

    assignmentTableBody.querySelectorAll('tr').forEach((row) => {
        const matchesQuery = row.textContent.toLowerCase().includes(query);
        const matchesStatus = !status || row.dataset.status === status;
        const isVisible = matchesQuery && matchesStatus;
        row.hidden = !isVisible;
        visible += Number(isVisible);
    });

    assignmentVisibleCount.textContent = String(visible);
    assignmentEmptyState.hidden = visible !== 0;
}

function closeManagerModal() {
    managerModal.hidden = true;
    activeAssignmentRow = null;
}

function setAssignmentStatus(row, status) {
    const statusClasses = {
        Assigned: 'status-assigned',
        'Pending Approval': 'status-pending',
        'Return Requested': 'status-return',
        Stationed: 'status-stationed',
    };
    const statusLabels = {
        Assigned: 'Assigned',
        'Pending Approval': 'Pending approval',
        'Return Requested': 'Return requested',
        Stationed: 'Stationed',
    };
    const badge = row.querySelector('[data-manager-status]');
    const indicator = document.createElement('span');

    badge.className = `status-badge ${statusClasses[status]}`;
    badge.replaceChildren(indicator, document.createTextNode(statusLabels[status]));
    row.dataset.status = status;
}

function setAssignmentActions(row, actions) {
    const actionsContainer = row.querySelector('.row-actions');
    actionsContainer.replaceChildren();

    actions.forEach(({ action, label, style }) => {
        const button = document.createElement('button');
        button.className = `row-action${style ? ` ${style}` : ''}`;
        button.type = 'button';
        button.dataset.managerAction = action;
        button.textContent = label;
        actionsContainer.append(button);
    });
}

function updateAssignmentOwner(row, assignee, department) {
    const label = row.querySelector('[data-assignee-label]');
    const avatar = row.querySelector('.person-avatar');

    label.textContent = assignee || 'Office Floor 2';
    label.classList.toggle('unassigned', !assignee);
    if (avatar) {
        avatar.textContent = assignee.split(/\s+/).map((part) => part[0]).join('').slice(0, 2).toUpperCase();
    }

    row.dataset.assignee = assignee;
    row.dataset.department = department;
    row.querySelector('[data-department-label]').textContent = department;
}

function openManagerModal(row, action) {
    activeAssignmentRow = row;
    managerActionForm.reset();
    managerActionForm.elements.asset.value = row.dataset.asset;
    managerActionForm.elements.action.value = action;

    const title = document.querySelector('#manager-modal-title');
    const description = document.querySelector('#manager-modal-description');
    const submit = document.querySelector('#manager-modal-submit');
    const transferFields = document.querySelector('.manager-transfer-fields');
    const returnField = document.querySelector('.manager-return-field');
    const assigneeInput = managerActionForm.elements.assignee;
    const departmentInput = managerActionForm.elements.department;
    const isReturn = action === 'return';
    const isReassign = action === 'reassign';

    title.textContent = isReturn ? 'Request asset return' : isReassign ? 'Reassign asset' : 'Request transfer';
    description.textContent = isReturn
        ? `Send a return request for ${row.dataset.asset}.`
        : isReassign
            ? `Choose the new owner and department for ${row.dataset.asset}.`
            : `Choose the new owner and department for ${row.dataset.asset}.`;
    submit.textContent = isReturn ? 'Request return' : isReassign ? 'Reassign asset' : 'Submit transfer';
    transferFields.hidden = isReturn;
    returnField.hidden = !isReturn;
    assigneeInput.required = !isReturn;
    departmentInput.required = !isReturn;
    managerModal.hidden = false;
    (isReturn ? managerActionForm.elements.return_note : assigneeInput).focus();
}

assignmentSearch?.addEventListener('input', filterAssignments);
assignmentFilter?.addEventListener('change', filterAssignments);

assignmentTableBody?.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) return;

    const button = event.target.closest('[data-manager-action]');
    if (!button) return;

    const row = button.closest('tr');
    const action = button.dataset.managerAction;

    if (action === 'approve') {
        const assignee = row.dataset.requestedAssignee || row.dataset.assignee;
        const department = row.dataset.requestedDepartment || row.dataset.department;
        updateAssignmentOwner(row, assignee, department);
        delete row.dataset.requestedAssignee;
        delete row.dataset.requestedDepartment;
        setAssignmentStatus(row, 'Assigned');
        setAssignmentActions(row, [
            { action: 'transfer', label: 'Transfer' },
            { action: 'return', label: 'Return', style: 'row-action-muted' },
        ]);
        showToast(`Transfer approved for ${row.dataset.asset}.`);
        filterAssignments();
        return;
    }

    if (action === 'accept-return') {
        updateAssignmentOwner(row, '', row.dataset.department);
        setAssignmentStatus(row, 'Stationed');
        setAssignmentActions(row, [{ action: 'reassign', label: 'Reassign' }]);
        showToast(`Return accepted for ${row.dataset.asset}.`);
        filterAssignments();
        return;
    }

    openManagerModal(row, action);
});

document.querySelectorAll('[data-close-manager-modal]').forEach((button) => {
    button.addEventListener('click', closeManagerModal);
});

managerModal?.addEventListener('click', (event) => {
    if (event.target === managerModal) closeManagerModal();
});

managerActionForm?.addEventListener('submit', (event) => {
    event.preventDefault();

    const formData = new FormData(managerActionForm);
    const action = String(formData.get('action'));
    const row = activeAssignmentRow;

    if (action === 'return') {
        setAssignmentStatus(row, 'Return Requested');
        setAssignmentActions(row, [{ action: 'accept-return', label: 'Accept return', style: 'row-action-approve' }]);
    } else if (action === 'transfer') {
        row.dataset.requestedAssignee = String(formData.get('assignee')).trim();
        row.dataset.requestedDepartment = String(formData.get('department'));
        setAssignmentStatus(row, 'Pending Approval');
        setAssignmentActions(row, [{ action: 'approve', label: 'Approve transfer', style: 'row-action-approve' }]);
    } else {
        updateAssignmentOwner(row, String(formData.get('assignee')).trim(), String(formData.get('department')));
        setAssignmentStatus(row, 'Assigned');
        setAssignmentActions(row, [
            { action: 'transfer', label: 'Transfer' },
            { action: 'return', label: 'Return', style: 'row-action-muted' },
        ]);
    }

    const assetName = row.dataset.asset;
    const confirmation = action === 'return'
        ? `Return requested for ${assetName}.`
        : action === 'reassign'
            ? `${assetName} has been reassigned.`
            : `Transfer request submitted for ${assetName}.`;

    closeManagerModal();
    filterAssignments();
    showToast(confirmation);
});

document.querySelector('#export-assignments')?.addEventListener('click', () => {
    const rows = [...assignmentTableBody.querySelectorAll('tr:not([hidden])')];
    const csvRows = [['Asset', 'Assigned to', 'Department', 'Status']];

    rows.forEach((row) => {
        csvRows.push([
            row.dataset.asset,
            row.dataset.assignee || 'Office Floor 2',
            row.dataset.department,
            row.dataset.status,
        ]);
    });

    const csv = csvRows.map((row) => row.map((value) => `"${value.replaceAll('"', '""')}"`).join(',')).join('\r\n');
    const download = document.createElement('a');
    download.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    download.download = 'asset-assignments.csv';
    download.click();
    URL.revokeObjectURL(download.href);
    showToast(`Exported ${rows.length} ${rows.length === 1 ? 'assignment' : 'assignments'} to CSV.`);
});

function filterEmployeeAssets() {
    if (!employeeAssetTable) return;

    const query = employeeAssetSearch.value.trim().toLowerCase();
    let visible = 0;

    employeeAssetTable.querySelectorAll('tr').forEach((row) => {
        const isVisible = row.textContent.toLowerCase().includes(query);
        row.hidden = !isVisible;
        visible += Number(isVisible);
    });

    employeeVisibleCount.textContent = String(visible);
    employeeAssetsEmpty.hidden = visible !== 0;
}

function openEmployeeModal(type) {
    const isAssetRequest = type === 'asset';
    const targetModal = isAssetRequest ? employeeAssetModal : employeeIssueModal;

    targetModal.hidden = false;
    targetModal.querySelector('input, select, textarea')?.focus();
}

function closeEmployeeModal(modalElement) {
    modalElement.hidden = true;
}

function handleEmployeeRequest(form, message) {
    employeeRequestCount.textContent = String(Number(employeeRequestCount.textContent) + 1);
    form.reset();
    closeEmployeeModal(form.closest('.modal-backdrop'));
    showToast(message);
}

employeeAssetSearch?.addEventListener('input', filterEmployeeAssets);

document.querySelectorAll('[data-open-employee-modal]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        openEmployeeModal(button.dataset.openEmployeeModal);
    });
});

document.querySelectorAll('[data-close-employee-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        const backdrop = button.closest('.modal-backdrop');
        closeEmployeeModal(backdrop);
    });
});

[employeeAssetModal, employeeIssueModal].forEach((modalElement) => {
    modalElement?.addEventListener('click', (event) => {
        if (event.target === modalElement) closeEmployeeModal(modalElement);
    });
});

employeeAssetModal?.querySelector('form')?.addEventListener('submit', (event) => {
    event.preventDefault();
    handleEmployeeRequest(event.currentTarget, 'Your asset request has been submitted for approval.');
});

employeeIssueModal?.querySelector('form')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const assetCode = new FormData(event.currentTarget).get('asset_code');
    handleEmployeeRequest(event.currentTarget, `Your repair ticket for ${assetCode} has been submitted.`);
});

document.querySelector('#export-employee-assets')?.addEventListener('click', () => {
    const rows = [...employeeAssetTable.querySelectorAll('tr:not([hidden])')];
    const csvRows = [['Item code', 'Item name', 'Category', 'Assigned date', 'Status']];

    rows.forEach((row) => {
        const cells = row.querySelectorAll('td');
        csvRows.push([...cells].map((cell) => cell.textContent.trim().replace(/\s+/g, ' ')));
    });

    const csv = csvRows.map((row) => row.map((value) => `"${value.replaceAll('"', '""')}"`).join(',')).join('\r\n');
    const download = document.createElement('a');
    download.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    download.download = 'my-assigned-assets.csv';
    download.click();
    URL.revokeObjectURL(download.href);
    showToast(`Exported ${rows.length} ${rows.length === 1 ? 'asset' : 'assets'} to CSV.`);
});
