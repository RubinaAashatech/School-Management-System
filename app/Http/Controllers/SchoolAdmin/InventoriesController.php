<?php

namespace App\Http\Controllers\SchoolAdmin;

namespace App\Http\Controllers\SchoolAdmin;

use Alert;
use App\Models\InventoryHead;
use Validator;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InventoriesController extends Controller
{
    public function index()
    {
        $page_title = 'Inventory Listing';
        $inventorieshead = InventoryHead::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.school_admin.inventory.index', compact('page_title', 'inventorieshead'));
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            
            'inventory_head_id' => 'required|string',
            'name' => 'required|string',
            'unit' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        if ($validatedData->fails()) {
            return back()->withToastError($validatedData->messages()->all()[0])->withInput();
        }

        try {
            $inventoryData = $request->all();

            // Ensure the user is authenticated and has a school_id
            if (!Auth::check()) {
                return back()->withToastError('User not authenticated.');
            }

            $school_id = Auth::user()->school_id; 
            if (is_null($school_id)) {
                return back()->withToastError('School ID is not set for the user.');
            }

            $inventoryData['school_id'] = $school_id;

            Inventory::create($inventoryData);

            return redirect()->back()->withToastSuccess('Inventory Saved Successfully!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $inventory = Inventory::find($id);
        return view('backend.school_admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = Validator::make($request->all(), [
            'inventory_head_id' => 'required|string',
            'name' => 'required|string',
            'unit' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required',
        ]);

        if ($validatedData->fails()) {
            return back()->withToastError($validatedData->messages()->all()[0])->withInput();
        }

        $inventory = Inventory::findOrFail($id);

        try {
            $data = $request->all();

            // Ensure the user is authenticated and has a school_id
            if (!Auth::check()) {
                return back()->withToastError('User not authenticated.');
            }

            $school_id = Auth::user()->school_id; // Assuming the user is authenticated and has a school_id
            if (is_null($school_id)) {
                return back()->withToastError('School ID is not set for the user.');
            }

            $data['school_id'] = $school_id;

            $inventory->update($data);

            return redirect()->back()->withToastSuccess('Successfully Updated Inventory!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $inventory = Inventory::find($id);

        try {
            $inventory->delete();
            return redirect()->back()->withToastSuccess('Inventory has been Successfully Deleted!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    public function getAllInventories(Request $request)
    {
        $inventories = $this->getForDataTable($request->all());

        return Datatables::of($inventories)
            ->escapeColumns([])
            ->addColumn('inventory_head_id', function ($inventory) {
                return $inventory->inventoryHead->name;
            })
            ->addColumn('name', function ($inventory) {
                return $inventory->name;
            })
            ->addColumn('unit', function ($inventory) {
                return $inventory->unit;
            })
            ->addColumn('description', function ($inventory) {
                return $inventory->description;
            })
            ->addColumn('created_at', function ($inventory) {
                return $inventory->created_at->diffForHumans();
            })
            ->addColumn('status', function ($attendanceType) {
                return $attendanceType->is_active == 1 ? '<span class="btn-sm btn-success">Active</span>' : '<span class="btn-sm btn-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($inventory) {
                return view('backend.school_admin.inventory.partials.controller_action', ['inventory' => $inventory])->render();
            })
            ->make(true);
    }

    public function getForDataTable($request)
    {
        $dataTableQuery = Inventory::where(function ($query) use ($request) {
            if (isset($request->id)) {
                $query->where('id', $request->id);
            }
        })
        ->get();

        return $dataTableQuery;
    }
}
