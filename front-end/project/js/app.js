class App {
    constructor() {
        this.modal = new Modal();
        this.table = new CompanyTable();
        this.addBtn = document.getElementById('addCompanyBtn');
        this.form = document.getElementById('companyForm');
        
        this.setupListeners();
        this.loadCompanies();
    }

    setupListeners() {
        this.addBtn.addEventListener('click', () => this.modal.open());
        
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit();
        });
        
        this.table.tableBody.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                this.handleDelete(id);
            }
        });
    }

    loadCompanies() {
        const companies = Storage.getCompanies();
        this.table.render(companies);
    }

    handleFormSubmit() {
        const formData = new FormData(this.form);
        const company = {
            name: formData.get('name'),
            location: formData.get('location'),
            date: formData.get('date'),
            description: formData.get('description')
        };

        Storage.addCompany(company);
        this.loadCompanies();
        this.modal.close();
    }

    handleDelete(id) {
        if (confirm('Are you sure you want to delete this company?')) {
            Storage.deleteCompany(id);
            this.loadCompanies();
        }
    }
}

// Initialize the application
new App();