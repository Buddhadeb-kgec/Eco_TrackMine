// Modal Service
const ModalService = {
    modal: null,
    addBtn: null,
    closeBtn: null,
    cancelBtn: null,
    form: null,

    init() {
        // Get DOM elements
        this.modal = document.getElementById('modal');
        this.addBtn = document.getElementById('addCompanyBtn');
        this.closeBtn = document.getElementById('closeModal');
        this.cancelBtn = document.getElementById('cancelBtn');
        this.form = document.getElementById('companyForm');

        // Add event listeners
        this.addBtn.addEventListener('click', () => this.openModal());
        this.closeBtn.addEventListener('click', () => this.closeModal());
        this.cancelBtn.addEventListener('click', () => this.closeModal());
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        // Handle form submission
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
    },

    openModal() {
        this.modal.classList.add('show');
        this.form.reset(); // Clear form
    },

    closeModal() {
        this.modal.classList.remove('show');
    },

    handleSubmit() {
        // Get form data
        const formData = {
            name: document.getElementById('name').value,
            location: document.getElementById('location').value,
            date: document.getElementById('date').value,
            description: document.getElementById('description').value
        };

        // Add company to storage
        const newCompany = StorageService.addCompany(formData);

        // Update table
        TableService.addCompanyToTable(newCompany);

        // Close modal
        this.closeModal();

        // Hide empty state if visible
        document.getElementById('emptyState').style.display = 'none';
    }
};
