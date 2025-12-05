<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['department', 'roles'])
            ->where('is_active', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('department_id') && $request->input('department_id') != '') {
            $query->where('department_id', $request->input('department_id'));
        }

        $employees = $query->paginate(12);
        $departments = Department::all();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function show(User $employee)
    {
        return view('employees.show', compact('employee'));
    }
}
