// Local Storage Keys
const COMPANIES_KEY = 'companies';

// Storage Service
const StorageService = {
    // Get all companies
    getCompanies() {
        const companies = localStorage.getItem(COMPANIES_KEY);
        return companies ? JSON.parse(companies) : [];
    },

    // Add a new company
    addCompany(company) {
        const companies = this.getCompanies();
        company.id = Date.now().toString(); // Generate unique ID
        companies.push(company);
        localStorage.setItem(COMPANIES_KEY, JSON.stringify(companies));
        return company;
    },

    // Delete a company
    deleteCompany(id) {
        const companies = this.getCompanies();
        const updatedCompanies = companies.filter(company => company.id !== id);
        localStorage.setItem(COMPANIES_KEY, JSON.stringify(updatedCompanies));
    },

    // Clear all companies
    clearCompanies() {
        localStorage.removeItem(COMPANIES_KEY);
    }
};
