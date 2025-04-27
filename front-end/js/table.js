// Table Service
const TableService = {
    tableBody: null,
    emptyState: null,

    init() {
        this.tableBody = document.getElementById('companyTableBody');
        this.emptyState = document.getElementById('emptyState');
        this.loadCompanies();
    },

    loadCompanies() {
        const companies = StorageService.getCompanies();
        
        if (companies.length === 0) {
            this.emptyState.style.display = 'block';
            return;
        }

        this.emptyState.style.display = 'none';
        companies.forEach(company => this.addCompanyToTable(company));
    },

    addCompanyToTable(company) {
        const row = document.createElement('tr');
        row.setAttribute('data-id', company.id);
        
        row.innerHTML = `
            <td>${company.name}</td>
            <td>${company.location}</td>
            <td>${company.date}</td>
            <td>${company.description}</td>
            <td>
                <button class="btn btn-secondary delete-btn" onclick="TableService.deleteCompany('${company.id}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                    Delete
                </button>
            </td>
        `;

        this.tableBody.appendChild(row);
    },

    deleteCompany(id) {
        if (confirm('Are you sure you want to delete this company?')) {
            // Remove from storage
            StorageService.deleteCompany(id);

            // Remove from table
            const row = this.tableBody.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                row.remove();
            }

            // Show empty state if no companies left
            if (this.tableBody.children.length === 0) {
                this.emptyState.style.display = 'block';
            }
        }
    }
};
