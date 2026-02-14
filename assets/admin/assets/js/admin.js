// Confirmation avant actions importantes
function confirmAction(message, callback) {
    if(confirm(message)) {
        callback();
    }
}

// Gestionnaire de notifications toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast show position-fixed bottom-0 right-0 m-3 bg-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="mr-auto">Notification</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body text-white">
            ${message}
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Export des données en CSV
function exportToCSV(data, filename) {
    const csv = data.map(row => 
        row.map(cell => 
            typeof cell === 'string' ? `"${cell.replace(/"/g, '""')}"` : cell
        ).join(',')
    ).join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${filename}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Formatage de dates
function formatDate(date) {
    const d = new Date(date);
    return `${d.getDate().toString().padStart(2, '0')}/${
        (d.getMonth() + 1).toString().padStart(2, '0')}/${
        d.getFullYear()}`;
}

// Validation de formulaire
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const required = this.querySelectorAll('[required]');
        let valid = true;
        
        required.forEach(field => {
            if(!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if(!valid) {
            e.preventDefault();
            showToast('Veuillez remplir tous les champs obligatoires', 'danger');
        }
    });
});

// Initialisation des tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});