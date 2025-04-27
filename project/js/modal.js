class Modal {
    constructor() {
        this.modal = document.getElementById('modal');
        this.form = document.getElementById('companyForm');
        this.closeBtn = document.getElementById('closeModal');
        this.cancelBtn = document.getElementById('cancelBtn');
        
        this.setupListeners();
    }

    setupListeners() {
        this.closeBtn.addEventListener('click', () => this.close());
        this.cancelBtn.addEventListener('click', () => this.close());
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });
    }

    open() {
        this.modal.style.display = 'flex';
        this.form.reset();
    }

    close() {
        this.modal.style.display = 'none';
        this.form.reset();
    }
}