
const header = document.querySelector("header.header");

document.addEventListener("scroll", () => {
    if (header) {
        header.classList.toggle("shadow-sm", document.documentElement.scrollTop > 0);
    }
});


// this js is for edit form 
document.querySelectorAll('.edit-extinguisher-btn').forEach(button => {

    button.addEventListener('click', function () {

        const id = this.dataset.id;

        fetch(`backend/controller/FireExtinguisherController.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(result => {

                if (!result.success) {
                    alert('Failed to load fire extinguisher.');
                    return;
                }

                const data = result.data;

                document.getElementById('editExtinguisherId').value =
                    data.extinguisher_id;

                document.getElementById('editExtinguisherCode').value =
                    data.extinguisher_code;

                document.getElementById('editType').value =
                    data.type;

                document.getElementById('editCapacity').value =
                    data.capacity;

                document.getElementById('editClass').value =
                    data.class;

                document.getElementById('editPlacement').value =
                    data.placement;

                document.getElementById('editLocation').value =
                    data.location;

                document.getElementById('editConditionStatus').value =
                    data.condition_status;

                document.getElementById('editManufacturedDate').value =
                    data.manufactured_date;

                document.getElementById('editExpirationDate').value =
                    data.expiration_date;

                document.getElementById('editRemarks').value =
                    data.remarks ?? '';

            })
            .catch(error => {
                console.error(error);
                alert('Something went wrong.');
            });

    });

});


// ========================================
// FIRE EXTINGUISHER CODE
// ========================================

const codeInput = document.getElementById('extinguisherCode');
const codeFeedback = document.getElementById('extinguisherCodeFeedback');
const addFireExtinguisherBtn = document.getElementById('addFireExtinguisherBtn');

let codeCheckTimeout;
let isCodeDuplicate = false;


// ========================================
// CHECK IF CODE EXISTS
// ========================================

function checkFireExtinguisherCode(code) {

    clearTimeout(codeCheckTimeout);

    codeFeedback.textContent = '';
    codeFeedback.className = 'small mt-1';

    // Default: allow submit
    isCodeDuplicate = false;
    addFireExtinguisherBtn.disabled = false;

    // Empty code
    if (code === '') {

        codeInput.classList.remove(
            'is-valid',
            'is-invalid'
        );

        return;
    }


    // Delay checking
    codeCheckTimeout = setTimeout(() => {

        const branch = document.getElementById('branch').value;

        fetch(
            `backend/controller/FireExtinguisherController.php?action=checkCode&code=${encodeURIComponent(code)}&branch=${encodeURIComponent(branch)}`
        )
            .then(response => {

                if (!response.ok) {
                    throw new Error(
                        `HTTP error: ${response.status}`
                    );
                }

                return response.json();

            })
            .then(result => {

                console.log('Code check result:', result);


                // ========================================
                // DUPLICATE
                // ========================================

                if (result.exists === true) {

                    isCodeDuplicate = true;

                    codeFeedback.textContent =
                        'This fire extinguisher code already exists.';

                    codeFeedback.className =
                        'small mt-1 text-danger';

                    codeInput.classList.add(
                        'is-invalid'
                    );

                    codeInput.classList.remove(
                        'is-valid'
                    );

                    // DISABLE ADD BUTTON
                    addFireExtinguisherBtn.disabled = true;

                }


                // ========================================
                // AVAILABLE
                // ========================================
                else {

                    isCodeDuplicate = false;

                    codeFeedback.textContent =
                        'Fire extinguisher code is available.';

                    codeFeedback.className =
                        'small mt-1 text-success';

                    codeInput.classList.remove(
                        'is-invalid'
                    );

                    codeInput.classList.add(
                        'is-valid'
                    );

                    // ENABLE ADD BUTTON
                    addFireExtinguisherBtn.disabled = false;

                }

            })
            .catch(error => {

                console.error(
                    'Code check error:',
                    error
                );

                isCodeDuplicate = false;

                codeFeedback.textContent =
                    'Unable to check fire extinguisher code.';

                codeFeedback.className =
                    'small mt-1 text-warning';

                codeInput.classList.remove(
                    'is-valid',
                    'is-invalid'
                );

                // Disable while checking has failed
                addFireExtinguisherBtn.disabled = true;

            });

    }, 400);
}


// ========================================
// MANUAL CODE INPUT
// ========================================

codeInput.addEventListener('input', function () {

    const code = this.value.trim();

    checkFireExtinguisherCode(code);

});


// ========================================
// AUTO-GENERATE FIRE EXTINGUISHER CODE
// ========================================

function generateFireExtinguisherCode() {

    const branch = document.getElementById('branch').value;

    if (!branch) {

        codeInput.value = '';
        addFireExtinguisherBtn.disabled = true;

        Swal.fire({
            icon: 'warning',
            title: 'Branch Required',
            text: 'Please select a branch to auto-generate an available code.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0d6efd',
            width: '320px',
            padding: '1rem',
            customClass: {
                popup: 'small-swal-popup'
            }
        });


        return;
    }

    fetch(
        `backend/controller/FireExtinguisherController.php?action=getNextCode&branch=${encodeURIComponent(branch)}`
    )
        .then(response => {

            if (!response.ok) {
                throw new Error(
                    `HTTP error: ${response.status}`
                );
            }

            return response.json();
        })
        .then(result => {

            console.log('Generated code:', result);

            if (result.success) {

                codeInput.value = result.code;

                // Check if generated code is available
                checkFireExtinguisherCode(
                    result.code
                );

            } else {

                codeInput.value = '';
                addFireExtinguisherBtn.disabled = true;
            }
        })
        .catch(error => {

            console.error(
                'Generate code error:',
                error
            );

            codeInput.value = '';
            addFireExtinguisherBtn.disabled = true;
        });
}


// ========================================
// AUTO-GENERATE WHEN ADD MODAL OPENS
// ========================================

const addFireExtinguisherModal =
    document.getElementById(
        'addFireExtinguisherModal'
    );


if (addFireExtinguisherModal) {

    addFireExtinguisherModal.addEventListener(
        'shown.bs.modal',
        function () {

            // Generate only if empty
            if (codeInput.value.trim() === '') {

                // Disable while generating/checking
                addFireExtinguisherBtn.disabled = true;

                generateFireExtinguisherCode();

            } else {

                checkFireExtinguisherCode(
                    codeInput.value.trim()
                );

            }

        }
    );

}

// ========================================
// PREVENT SUBMIT IF DUPLICATE
// ========================================

const addForm = addFireExtinguisherBtn.closest('form');

if (addForm) {

    addForm.addEventListener('submit', function (event) {

        const code = codeInput.value.trim();


        // Empty code
        if (code === '') {

            event.preventDefault();

            codeFeedback.textContent =
                'Fire extinguisher code is required.';

            codeFeedback.className =
                'small mt-1 text-danger';

            codeInput.classList.add(
                'is-invalid'
            );

            return;
        }


        // for Duplicate validation ng fe code 
        if (isCodeDuplicate) {

            event.preventDefault();

            codeFeedback.textContent =
                'This fire extinguisher code already exists.';

            codeFeedback.className =
                'small mt-1 text-danger';

            codeInput.classList.add(
                'is-invalid'
            );

            addFireExtinguisherBtn.disabled = true;

            return;
        }

    });

}

// Delegate view clicks so records added by AJAX search/pagination remain interactive.
document.addEventListener('click', function (event) {
    const button = event.target.closest('.view-extinguisher-btn');
    if (!button) return;

    const id = button.dataset.id;
    fetch(`backend/controller/FireExtinguisherController.php?action=get&id=${encodeURIComponent(id)}`)
        .then(response => {
            if (!response.ok) throw new Error('Failed to fetch fire extinguisher.');
            return response.json();
        })
        .then(result => {
            if (!result.success) {
                alert(result.message || 'Failed to load fire extinguisher.');
                return;
            }

            const data = result.data || {};
            document.getElementById('viewExtinguisherCode').value = data.extinguisher_code ?? '';
            document.getElementById('viewType').value = data.type ?? '';
            document.getElementById('viewCapacity').value = data.capacity ?? '';
            document.getElementById('viewClass').value = data.class ?? '';
            document.getElementById('viewPlacement').value = data.placement ?? '';
            document.getElementById('viewLocation').value = data.location ?? '';
            document.getElementById('viewConditionStatus').value = data.condition_status ?? '';
            document.getElementById('viewManufacturedDate').value = data.manufactured_date ?? '';
            document.getElementById('viewLastRefilledDate').value = data.refilled_date ?? '';
            document.getElementById('viewExpirationDate').value = data.expiration_date ?? '';
            document.getElementById('viewRemarks').value = data.remarks ?? '';

            const editBtn = document.getElementById('viewEditFireExtinguisherBtn');
            const archiveBtn = document.getElementById('viewDeleteFireExtinguisherBtn');
            if (editBtn) editBtn.dataset.id = data.extinguisher_id;
            if (archiveBtn) {
                archiveBtn.href = `backend/controller/FireExtinguisherController.php?action=delete&id=${encodeURIComponent(data.extinguisher_id)}`;
                archiveBtn.onclick = function () {
                    return confirm('Archive this fire extinguisher?');
                };
            }
        })
        .catch(error => {
            console.error(error);
            alert('Something went wrong while loading fire extinguisher.');
        });
});

// ========================================// FILTER BY PLACEMENT TYPE
// ========================================

const placementTypeFilter =
    document.getElementById(
        'placementTypeFilter'
    );

if (placementTypeFilter) {

    placementTypeFilter.addEventListener(
        'change',
        function () {

            const url =
                new URL(window.location.href);

            // Set selected placement.
            url.searchParams.set(
                'placement',
                this.value
            );

            // Reset to page 1 after changing filter.
            url.searchParams.set(
                'page',
                '1'
            );

            window.location.href =
                url.toString();
        }
    );
}
//==================================================
// CONDITION TYPE FILTER
// ============================================================

const conditionFilter = document.getElementById(
    'conditionFilter'
);

if (conditionFilter) {

    conditionFilter.addEventListener(
        'change',
        function () {

            const url = new URL(
                window.location.href
            );

            // Set selected condition
            url.searchParams.set(
                'condition',
                this.value
            );

            // Reset pagination to page 1
            url.searchParams.set(
                'page',
                '1'
            );

            // Reload with the selected filter
            window.location.href =
                url.toString();
        }
    );
}

// this script is for generate inventory report button
const generateInventoryReportBtn = document.getElementById(
    'generateInventoryReportBtn'
);

if (generateInventoryReportBtn) {

    const extinguisherCards = document.querySelectorAll(
        '.extinguisher-card'
    );

    // Disable report button when there are no fire extinguishers
    generateInventoryReportBtn.disabled =
        extinguisherCards.length === 0;

    generateInventoryReportBtn.addEventListener(
        'click',
        function (event) {

            const branchFilter =
                document.getElementById('branchFilter');

            const branch = branchFilter ?
                branchFilter.value :
                'all';

            const reportUrl = new URL(
                'backend/controller/GenerateInventoryReportController.php',
                window.location.href
            );

            reportUrl.searchParams.set(
                'branch',
                branch
            );

            generateReport(
                event,
                reportUrl.toString()
            );
        }
    );
}
