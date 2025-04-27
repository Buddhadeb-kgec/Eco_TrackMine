import React from 'react';
import { Trash2 } from 'lucide-react';
import type { Company } from '../types/Company';

interface CompanyTableProps {
  companies: Company[];
  onDelete: (id: string) => void;
}

export function CompanyTable({ companies, onDelete }: CompanyTableProps) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full divide-y divide-gray-200">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Company Name
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Location
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody className="bg-white divide-y divide-gray-200">
          {companies.map((company) => (
            <tr key={company.id}>
              <td className="px-6 py-4 whitespace-nowrap">{company.name}</td>
              <td className="px-6 py-4 whitespace-nowrap">{company.location}</td>
              <td className="px-6 py-4 whitespace-nowrap">
                {new Date(company.date).toLocaleDateString()}
              </td>
              <td className="px-6 py-4">{company.description}</td>
              <td className="px-6 py-4 whitespace-nowrap">
                <button
                  onClick={() => onDelete(company.id)}
                  className="text-red-600 hover:text-red-900"
                >
                  <Trash2 className="h-5 w-5" />
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}