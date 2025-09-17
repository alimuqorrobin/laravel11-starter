<?php

namespace App\Http\Controllers\Master;

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
        $datatableConfig = DataTableHelper::render([
            'route' => route('roles.fetch'),
            'columns' => [
                ['field' => 'id', 'label' => 'ID'],
                ['field' => 'name', 'label' => 'Roles'],
                ['field' => 'description', 'label' => 'Keterangan'],
                ['field' => 'action', 'label' => 'Action'],
            ],
            'searchable' => ['name', 'description'],
            'defaultOrder' => ['id' => 'desc'],
            'customColumns' => [
                // Bisa pakai view Blade
                // 'action' => view('partials.datatable_action')->render(),
                // Bisa langsung HTML
                'action' => '<button class="btn btn-sm btn-warning" data-id="{id}">Edit</button> 
                             <button class="btn btn-sm btn-danger" data-id="{id}">Delete</button>',
                'id' => '<input type="checkbox">'
            ],
            'perPage' => 10 
        ]);
        $data['datatable'] = $datatableConfig;
        $view = view('master.roles.index', $data);
        $put['view_file'] = $view;
        $put['header_data'] = $this->getHeaderCss();
        return view('template.app', $put);
    }

    public function fetch(Request $request)
    {
        $query = Role::query()
            ->select([
                'roles.*'
            ]);

        // search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('roles.name', 'like', "%$search%")
                    ->orWhere('roles.description', 'like', "%$search%");
                //   ->orWhere('roles.name', 'like', "%$search%");
            });
        }

        // order
        if ($order = $request->input('order')) {
            foreach ($order as $field => $dir) {
                $query->orderBy($field, $dir);
            }
        }

        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $total = $query->count();
        $data = $query->forPage($page, $perPage)->get();

        return response()->json([
            'data' => $data,
            'total' => $total,
            'page' => $page
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
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

    public function exportExcel(Request $request)
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
