const Storage = {
    getCompanies() {
        const companies = localStorage.getItem('companies');
        return companies ? JSON.parse(companies) : [];
    },

    saveCompanies(companies) {
        localStorage.setItem('companies', JSON.stringify(companies));
    },

    addCompany(company) {
        const companies = this.getCompanies();
        const newCompany = {
            ...company,
            id: crypto.randomUUID()
        };
        companies.push(newCompany);
        this.saveCompanies(companies);
        return newCompany;
    },

    deleteCompany(id) {
        const companies = this.getCompanies();
        const filteredCompanies = companies.filter(company => company.id !== id);
        this.saveCompanies(filteredCompanies);
    }
};