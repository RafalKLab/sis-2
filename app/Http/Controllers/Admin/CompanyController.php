<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use shared\ConfigDefaultInterface;

class CompanyController extends MainController
{
    public function index()
    {
        $companies = Company::all();

        return view('main.admin.company.index', compact('companies'));
    }

    public function create()
    {
        return view('main.admin.company.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'string|required',
        ]);

        $company = Company::create($validated);

        return redirect()->route('admin-companies.index')
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('New company %s (id: %s) created', $company->name, $company->id));
    }

    public function edit(int $id) {
        $company = Company::find($id);
        if (!$company) {
            return redirect()->route('admin-companies.index');
        }

        return view('main.admin.company.edit', compact('company'));
    }

    public function update(Request $request, int $id) {
        $validated = $request->validate([
            'name' => 'string|required',
        ]);

        $company = Company::find($id);
        if (!$company) {
            return redirect()->route('admin-companies.index');
        }

        $company->name = $validated['name'];
        $company->save();

        return redirect()->route('admin-companies.index')
            ->with(ConfigDefaultInterface::FLASH_SUCCESS, sprintf('Company %s (id: %s) updated#79', $company->name, $company->id));
    }
}
