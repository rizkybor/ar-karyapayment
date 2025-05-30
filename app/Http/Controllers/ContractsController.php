<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Contracts;
use App\Models\ContractCategory;
use App\Models\MasterType;
use App\Models\MasterBillType;
use App\Models\MasterWorkUnit;
use App\Services\AccurateMasterOptionService;

use Carbon\Carbon;

class ContractsController extends Controller
{
    protected $accurate;

    public function __construct(AccurateMasterOptionService $accurate)
    {
        $this->accurate = $accurate;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $contracts = Contracts::query()
            ->when($search, function ($query) use ($search) {
                $query->where('contract_number', 'like', "%{$search}%")
                    ->orWhere('employee_name', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('work_unit', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        return view('pages/settings/contracts/index', compact('contracts', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();

        $category = ContractCategory::pluck('name');

        try {
            // Department List
            $getResponseDepartmentsAccurate = $this->accurate->getDepartmentList();
            $departmentList = $getResponseDepartmentsAccurate['d'];

            // Project List
            $dataProjectList = $this->accurate->getProjectList();

            // Data Classification  / Segmen Usaha
            $getResponsedataClassificationAccurate = $this->accurate->getDataClassificationList();
            $dataClassificationList = $getResponsedataClassificationAccurate['d'];
        } catch (\Exception $e) {
            return back()->withErrors('Gagal memuat data Accurate: ' . $e->getMessage());
        }

        return view('pages/settings/contracts/create', compact('mstType', 'mstWorkUnit', 'category',  'departmentList', 'dataProjectList', 'dataClassificationList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'contract_number' => 'required',
            'contract_initial' => 'required',
            'title' => 'required',
            'category' => 'required',
            'employee_name' => 'required',
            'value' => 'required',
            'contract_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'path' => 'required|file|mimes:pdf|max:10240',
            'bill_type' => 'nullable|array',
            'address' => 'required',
            'work_unit' => 'required',
            'status' => 'nullable|in:0,1',
            'departmentId' => 'nullable',
            'projectId' => 'nullable',
            'segmenUsahaId' => 'nullable',
        ]);

        $input = $request->all();

        $input['contract_initial']   = $request->input('contract_initial') ?: null;
        $input['departmentId']   = $request->input('departmentId') ?: null;
        $input['projectId']      = $request->input('projectId') ?: null;
        $input['segmenUsahaId']  = $request->input('segmenUsahaId') ?: null;

        // Format Tanggal
        $input['contract_date'] = Carbon::parse($request->contract_date)->format('Y-m-d');
        $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
        $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');

        $input['status'] = isset($request->status) ? (int) $request->status : null;

        // **📂 Ambil File dari Request**
        $file = $request->file('path');
        $fileName = $file->getClientOriginalName();
        $dropboxFolderName = '/contracts/';

        // 🚀 **Panggil fungsi uploadAttachment dari DropboxController**
        $dropboxController = new DropboxController();
        $dropboxPath = $dropboxController->uploadAttachment($file, $fileName, $dropboxFolderName);

        // ❌ Cek apakah upload ke Dropbox gagal
        if (!$dropboxPath) {
            return redirect()->back()->with('error', 'Gagal mengunggah file.');
        }

        $input['path'] = $dropboxPath;

        // Simpan data kontrak ke database
        $contract = Contracts::create($input);

        // Simpan data bill_type hanya jika tipe kontrak adalah management_fee
        if ($input['type'] === 'management_fee' && !empty($request->bill_type)) {
            foreach ($request->bill_type as $billType) {
                MasterBillType::create([
                    'contract_id' => $contract->id,
                    'bill_type' => $billType,
                ]);
            }
        }

        return redirect()->route('contracts.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contracts $contract)
    {
        $mstBillType = MasterBillType::where('contract_id', $contract->id)->get();
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();

        // Gunakan DropboxController untuk mendapatkan URL file
        $dropboxController = new DropboxController();
        $dropboxFolderName = '/contracts/';
        $contract->path = $dropboxController->getAttachmentUrl($contract->path, $dropboxFolderName);


        $contract->load(['manfeeDocuments', 'nonManfeeDocuments']);
        $manfeeDocuments = $contract->manfeeDocuments;
        $nonManfeeDocuments = $contract->nonManfeeDocuments;

        return view('pages/settings/contracts/show', compact(
            'contract',
            'mstType',
            'mstBillType',
            'mstWorkUnit',
            'manfeeDocuments',
            'nonManfeeDocuments'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contracts $contract)
    {
        $mstBillType = MasterBillType::where('contract_id', $contract->id)->get();
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();
        // data dummy category
        $category = [
            'Surat Perintah Kerja (SPK)',
            'Perjanjian',
            'Purchase Order',
            'Berita Acara Kesepakatan',
        ];

        try {
            // Department List
            $getResponseDepartmentsAccurate = $this->accurate->getDepartmentList();
            $departmentList = $getResponseDepartmentsAccurate['d'];

            // Project List
            $dataProjectList = $this->accurate->getProjectList();

            // Data Classification  / Segmen Usaha
            $getResponsedataClassificationAccurate = $this->accurate->getDataClassificationList();
            $dataClassificationList = $getResponsedataClassificationAccurate['d'];
        } catch (\Exception $e) {
            return back()->withErrors('Gagal memuat data Accurate: ' . $e->getMessage());
        }

        return view('pages/settings/contracts/edit', compact('contract', 'mstType', 'mstBillType', 'mstWorkUnit', 'category', 'departmentList', 'dataProjectList', 'dataClassificationList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contracts $contract)
    {
        $request->validate([
            'contract_number' => 'required',
            'contract_initial' => 'required',
            'title' => 'required',
            'category' => 'required',
            'employee_name' => 'required',
            'value' => 'required',
            'contract_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'nullable',
            'path' => 'nullable|file|mimes:pdf|max:10240', // Sama seperti store
            'bill_type' => 'nullable|array',
            'address' => 'required',
            'work_unit' => 'required',
            'status' => 'nullable|in:0,1',
            'departmentId' => 'nullable',
            'projectId' => 'nullable',
            'segmenUsahaId' => 'nullable',
        ]);

        $input = $request->except('path');

        $input['contract_initial']   = $request->input('contract_initial');
        $input['departmentId']   = $request->input('departmentId') ?: null;
        $input['projectId']      = $request->input('projectId') ?: null;
        $input['segmenUsahaId']  = $request->input('segmenUsahaId') ?: null;

        $input['contract_date'] = Carbon::parse($request->contract_date)->format('Y-m-d');
        $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
        $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
        $input['status'] = isset($request->status) ? (int) $request->status : null;

        // Jika ada file baru di-upload
        if ($request->hasFile('path')) {
            // Tidak perlu unlink karena file tersimpan di Dropbox

            $file = $request->file('path');
            $fileName = $file->getClientOriginalName();
            $dropboxFolderName = '/contracts/';

            $dropboxController = new DropboxController();
            $dropboxPath = $dropboxController->uploadAttachment($file, $fileName, $dropboxFolderName);

            if (!$dropboxPath) {
                return redirect()->back()->with('error', 'Gagal mengunggah file.');
            }

            $input['path'] = $dropboxPath;
        }

        $contract->update($input);

        // Hapus & update ulang bill_type jika kontrak bertipe management_fee
        MasterBillType::where('contract_id', $contract->id)->delete();

        if ($request->type === 'management_fee' && !empty($request->bill_type)) {
            foreach ($request->bill_type as $billType) {
                MasterBillType::create([
                    'contract_id' => $contract->id,
                    'bill_type' => $billType,
                ]);
            }
        }

        return redirect()->route('contracts.index')->with('success', 'Data berhasil diperbaharui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contracts $contract)
    {
        $dropboxPath = $contract->path;

        // 🔥 **Panggil fungsi `delete()` dari `DropboxController` untuk hapus di Dropbox**
        $dropboxController = app(DropboxController::class);
        $dropboxController->deleteAttachment($dropboxPath);

        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Data berhasil dihapus!');
    }
}
