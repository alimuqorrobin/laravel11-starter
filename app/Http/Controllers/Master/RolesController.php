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
        $datatableConfig = [
            'id'          => 'rolesTable',
            'routeFetch' => route('roles.fetch'),
            'routeExport' => route('roles.export.csv'),
            'columns' => [
                ['field' => 'id', 'label' => 'ID'],
                ['field' => 'name', 'label' => 'Roles'],
                ['field' => 'description', 'label' => 'Keterangan'],
                ['field' => 'action', 'label' => 'Action'],
            ],
            'defaultOrder' => ['col' => 'id', 'dir' => 'desc'],
            'perPage' => 10
        ];

        $data['datatableConfig'] = $datatableConfig;
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
        $page    = $request->page ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data'        => $paginator->items(),
            'total'       => $paginator->total(),
            'currentPage' => $paginator->currentPage(),
            'lastPage'    => $paginator->lastPage(),
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
