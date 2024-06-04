<?php

namespace App\Http\Controllers\SchoolAdmin;

use Alert;
use App\Models\InventoryHead;
use Validator;
use Carbon\Carbon;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


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
            'school_id' => 'required',
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
            $inventoryData['school_id'] = session('school_id');
            
            $savedData = Inventory::create($inventoryData);

            return redirect()->back()->withToastSuccess('Inventory Saved Successfully!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $inventory= Inventory::find($id);
        return view('backend.school_admin.inventory.index', compact('inventory'));
    }
    public function update(Request $request, string $id)
    {
        $validatedData = Validator::make($request->all(), [
            'school_id' => 'required',
            'inventory_head_id' => 'required|string',
            'name' => 'required|string',
            'invoice_number' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|string',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:jpeg,pdf',
            'is_active' => 'required',
        ]);

        if ($validatedData->fails()) {
            return back()->withToastError($validatedData->messages()->all()[0])->withInput();
        }

        $inventory = Inventory::findOrFail($id);

        try {
            $data = $request->all();
            $data['school_id'] = session('school_id');
            
            $updateNow = $inventory->update($data);

            return redirect()->back()->withToastSuccess('Successfully Updated Inventory!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage())->withInput();
        }

        return back()->withToastError('Cannot Update Inventory. Please try again')->withInput();
    }

    public function destroy(string $id)
    {
        $inventory = Inventory::find($id);

        try {
            $updateNow = $inventory->delete();
            return redirect()->back()->withToastSuccess('Inventory has been Successfully Deleted!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }

        return back()->withToastError('Something went wrong. Please try again');
    }
    public function getAllInventories(Request $request)
    {
        $inventories = $this->getForDataTable($request->all());

        return Datatables::of($inventories)
            ->escapeColumns([])
            ->addColumn('inventory_head_id', function ($inventory) {
                return $inventory->inventoryHead->name; //incomeHead is  public function incomeHead(){ } relationship from Income.php Model
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
