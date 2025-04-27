class CompanyTable {
    constructor() {
        this.tableBody = document.getElementById('companyTableBody');
        this.emptyState = document.getElementById('emptyState');
        this.table = document.getElementById('companyTable');
    }

    createRow(company) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${company.name}</td>
            <td>${company.location}</td>
            <td>${new Date(company.date).toLocaleDateString()}</td>
            <td>${company.description}</td>
            <td>
                <button class="delete-btn" data-id="${company.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
            </td>
        `;
        return row;
    }

    render(companies) {
        this.tableBody.innerHTML = '';
        
        if (companies.length === 0) {
            this.emptyState.style.display = 'block';
            this.table.style.display = 'none';
        } else {
            this.emptyState.style.display = 'none';
            this.table.style.display = 'table';
            
            companies.forEach(company => {
                this.tableBody.appendChild(this.createRow(company));
            });
        }
    }
}