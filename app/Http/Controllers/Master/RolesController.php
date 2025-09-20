<?php

namespace App\Http\Controllers\Master;

use App\Helpers\AllInOneHelper;
use App\Helpers\DataTableHelper;
use App\Http\Controllers\Controller;
use App\Models\Own\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RolesController extends Controller
{
    public function getHeaderCss()
    {
        return array(
            'js-1' => asset('assets/assets/js/controllers/master/roles.js'),
        );
    }

    public function index()
    {
        $datatableConfig = [
            'id'          => 'rolesTable',
            'routeFetch' => route('roles.fetch'),
            'routeExport' => route('roles.export'),
            'columns' => [
                ['field' => 'id', 'title' => 'ID'],
                ['field' => 'name', 'title' => 'Roles'],
                ['field' => 'description', 'title' => 'Keterangan'],
                ['field' => 'action', 'title' => 'Action'],
            ],
            'defaultOrder' => [
                ['col' => 'id', 'dir' => 'desc'],
                ['col' => 'name', 'dir' => 'asc']
            ],
            'perPage' => 10
        ];

        $data['datatableConfig'] = $datatableConfig;
        $view = view('master.roles.index', $data);
        $put['view_file'] = $view;
        $put['header_data'] = $this->getHeaderCss();
        return view('template.app', $put);
    }

    public function add(){
        $data['test'] ="eefefe";
        $view = view('master.roles.formadd', $data);
        $put['view_file'] = $view;
        $put['header_data'] = $this->getHeaderCss();
        return view('template.app', $put);
    }

    public function saveData(Request $request){
        $params = $request->all();

        $saveData = multiDbTransaction(['mysql'],function($dbs) use($params){
            $insert = new Role();
            $insert->name = AllInOneHelper::sanitize($params['roles']);
            $insert->description = AllInOneHelper::sanitize($params['keterangan']);
            $insert->save();
        });

        return response()->json($saveData);
    }

    public function updateData(Request $request){
        $params = $request->all();

    }

    public function fetch(Request $request)
    {
        $query = Role::query()
            ->select([
                'roles.*'
            ]);
        // filter search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // contoh filter custom
        // if ($request->role) {
        //     $query->where('role', $request->role);
        // }

        // // ordering
        // if ($request->orderCol) {
        //     $query->orderBy($request->orderCol, $request->orderDir ?? 'asc');
        // }

        // multi order
        if ($request->orderBy) {
            foreach ($request->orderBy as $order) {
                $query->orderBy($order['col'], $order['dir']);
            }
        }
        $perPage = $request->perPage ?? 10;
        $data = $query->paginate($perPage);
        $data->getCollection()->transform(function ($item) {
            $item->action = '<button type="button" class="btn btn-icon  btn-fab demo waves-effect"><span class="icon-base ri ri-delete-bin-4-fill icon-22px"></span></button>&nbsp;&nbsp;<button type="button" class="btn btn-icon  btn-fab demo waves-effect"><span class="icon-base ri ri-edit-box-fill icon-22px"></span></button>';
            return $item;
        });
        return response()->json($data);
    }

    public function export(Request $request){
        if ($request->type == 'csv') {
            $this->exportCsv($request);
        }else{
            $this->exportExcel($request);
        }
    }

    function exportCsv(Request $request): StreamedResponse
    {
        $query = Role::query()
            ->select([
                'roles.*'
            ]);

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama', 'Email', 'Role']); // header

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->role
                    ]);
                }
            });
            fclose($handle);
        };

        return response()->streamDownload($callback, 'users.csv');
    }

    function exportExcel(Request $request)
    {
        $query = Role::query()
            ->select([
                'roles.*'
            ]);

        // bikin Excel simple pakai PhpSpreadsheet atau Laravel Excel
        // contoh basic pake CSV-style untuk Excel
        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama', 'Description'], "\t"); // tab separated

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $row->description,
                    ], "\t");
                }
            });
            fclose($handle);
        };

        return response()->streamDownload($callback, 'users.xls');
    }


}
