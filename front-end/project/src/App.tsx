import React, { useState } from 'react';
import { Plus } from 'lucide-react';
import { CompanyForm } from './components/CompanyForm';
import { CompanyTable } from './components/CompanyTable';
import type { Company } from './types/Company';

function App() {
  const [companies, setCompanies] = useState<Company[]>([]);
  const [isFormOpen, setIsFormOpen] = useState(false);

  const handleAddCompany = (companyData: Omit<Company, 'id'>) => {
    const newCompany = {
      ...companyData,
      id: crypto.randomUUID(),
    };
    setCompanies([...companies, newCompany]);
    setIsFormOpen(false);
  };

  const handleDeleteCompany = (id: string) => {
    setCompanies(companies.filter(company => company.id !== id));
  };

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="px-4 py-6 sm:px-0">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">
              Coal Company Community Hub
            </h1>
            <button
              onClick={() => setIsFormOpen(true)}
              className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <Plus className="h-5 w-5 mr-2" />
              Add Company
            </button>
          </div>

          {companies.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-500">No companies added yet. Click the button above to add one.</p>
            </div>
          ) : (
            <div className="bg-white shadow overflow-hidden sm:rounded-lg">
              <CompanyTable companies={companies} onDelete={handleDeleteCompany} />
            </div>
          )}

          {isFormOpen && (
            <CompanyForm
              onSubmit={handleAddCompany}
              onClose={() => setIsFormOpen(false)}
            />
          )}
        </div>
      </div>
    </div>
  );
}

export default App;