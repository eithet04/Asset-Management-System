const searchInput = document.querySelector('#asset-search');
const statusFilter = document.querySelector('#status-filter');
const tableBody = document.querySelector('#asset-table-body');
const emptyState = document.querySelector('#empty-state');
const visibleCount = document.querySelector('#visible-count');
const totalCount = document.querySelector('#total-count');
const modal = document.querySelector('#asset-modal');
const assetForm = document.querySelector('#asset-form');
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
const resendForm = document.querySelector('[data-resend-form]');
const mainNav = document.querySelector('.main-nav');
const sidebarScrollStorageKey = 'axora-sidebar-scroll-position';
const themeToggle = document.querySelector('[data-theme-toggle]');

if (themeToggle) {
    const themeStorageKey = 'axora-theme';
    const savedTheme = localStorage.getItem(themeStorageKey);
    const activeTheme = savedTheme === 'dark' ? 'dark' : 'light';

    function setTheme(theme) {
        const isDark = theme === 'dark';

        document.documentElement.dataset.theme = theme;
        themeToggle.setAttribute('aria-label', `Switch to ${isDark ? 'light' : 'dark'} mode`);
        themeToggle.setAttribute('aria-pressed', String(isDark));
        themeToggle.title = `Switch to ${isDark ? 'light' : 'dark'} mode`;
    }

    setTheme(activeTheme);
    themeToggle.addEventListener('click', () => {
        const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';

        localStorage.setItem(themeStorageKey, nextTheme);
        setTheme(nextTheme);
    });
}

if (mainNav) {
    const savedScrollPosition = sessionStorage.getItem(sidebarScrollStorageKey);
    if (savedScrollPosition !== null) {
        mainNav.scrollTop = Number(savedScrollPosition);
        sessionStorage.removeItem(sidebarScrollStorageKey);
    }
}

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

if (resendForm) {
    let remaining = Number(resendForm.dataset.cooldown);
    const button = resendForm.querySelector('[data-resend-button]');
    const countdown = resendForm.querySelector('[data-countdown]');
    button.disabled = remaining > 0;
    const timer = window.setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            button.disabled = false;
            countdown.hidden = true;
            window.clearInterval(timer);
            return;
        }
        countdown.textContent = ` (${remaining}s)`;
    }, 1000);
}

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

function openAssetEditor(button) {
    const editing = Boolean(button);
    const method = assetForm.elements.namedItem('_method');
    const assetId = assetForm.elements.namedItem('asset_id');

    document.querySelector('[data-asset-form-success]')?.setAttribute('hidden', '');
    assetForm.querySelectorAll('option[data-edit-option]').forEach((option) => option.remove());
    assetForm.reset();
    assetForm.action = editing
        ? modal.dataset.updateUrl.replace('__ASSET__', button.dataset.id)
        : assetForm.dataset.createUrl;
    method.disabled = !editing;
    assetId.value = editing ? button.dataset.id : '';
    document.querySelector('#modal-title').textContent = editing ? 'Update asset details' : 'Add a new asset';
    document.querySelector('[data-modal-description]').textContent = editing
        ? 'Update the asset information below. To change its status, use the Status & condition section.'
        : 'Enter the details below to add an item to your registry.';
    document.querySelector('[data-submit-label]').textContent = editing ? 'Save changes' : 'Add asset';
    document.querySelector('[data-status-note]').hidden = !editing;
    document.querySelector('[data-status-field]').hidden = editing;

    if (editing) {
        ['category', 'location'].forEach((fieldName) => {
            const id = button.dataset[`${fieldName}Id`];
            const select = assetForm.elements.namedItem(`${fieldName}_id`);
            if (id && !select.querySelector(`option[value="${CSS.escape(id)}"]`)) {
                const option = new Option(
                    `${button.dataset[`${fieldName}Name`]}${button.dataset[`${fieldName}Active`] === 'true' ? '' : ' (inactive)'}`,
                    id,
                    true,
                    true,
                );
                option.dataset.editOption = 'true';
                select.add(option);
            }
        });
        Object.entries(button.dataset).forEach(([key, value]) => {
            const fieldName = key.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`);
            const field = assetForm.elements.namedItem(fieldName);
            if (field && key !== 'id') field.value = value;
        });
        assetForm.elements.namedItem('_method').disabled = false;
    } else {
        document.querySelector('[data-status-field]').hidden = false;
    }

    modal.hidden = false;
    assetForm.querySelector('input[name="name"]')?.focus();
}

if (assetForm && !assetForm.elements.namedItem('_method').disabled) {
    const assetId = assetForm.elements.namedItem('asset_id').value;
    assetForm.action = modal.dataset.updateUrl.replace('__ASSET__', assetId);
    document.querySelector('[data-status-field]').hidden = true;
    document.querySelector('#modal-title').textContent = 'Update asset details';
    document.querySelector('[data-modal-description]').textContent =
        'Update the asset information below. To change its status, use the Status & condition section.';
    document.querySelector('[data-submit-label]').textContent = 'Save changes';
    document.querySelector('[data-status-note]').hidden = false;
}

searchInput?.addEventListener('input', filterAssets);
statusFilter?.addEventListener('change', filterAssets);

document.querySelectorAll('[data-open-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        openAssetEditor();
    });
});

document.querySelectorAll('[data-edit-asset]').forEach((button) => {
    button.addEventListener('click', () => openAssetEditor(button));
});

document.querySelectorAll('[data-close-modal]').forEach((button) => button.addEventListener('click', closeModal));

document.querySelectorAll('[data-location-edit]').forEach((button) => {
    button.addEventListener('click', () => {
        const row = button.closest('tr');
        const editRow = row.nextElementSibling;
        const shouldOpen = editRow.hidden;

        document.querySelectorAll('[data-location-edit-row]').forEach((openRow) => {
            openRow.hidden = true;
            openRow.previousElementSibling.querySelector('[data-location-edit]')?.setAttribute('aria-expanded', 'false');
        });

        editRow.hidden = !shouldOpen;
        button.setAttribute('aria-expanded', String(shouldOpen));
    });
});

document.querySelectorAll('[data-location-edit-cancel]').forEach((button) => {
    button.addEventListener('click', () => {
        const editRow = button.closest('[data-location-edit-row]');
        editRow.hidden = true;
        editRow.previousElementSibling.querySelector('[data-location-edit]')?.setAttribute('aria-expanded', 'false');
    });
});

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
    link.addEventListener('click', (event) => {
        if (link.dataset.openEmployeeModal) {
            openEmployeeModal(link.dataset.openEmployeeModal);
        }

        const destination = new URL(link.href, window.location.href);
        const isSamePage = destination.origin === window.location.origin
            && destination.pathname === window.location.pathname
            && destination.search === window.location.search;
        if (mainNav && !isSamePage && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) {
            sessionStorage.setItem(sidebarScrollStorageKey, String(mainNav.scrollTop));
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
