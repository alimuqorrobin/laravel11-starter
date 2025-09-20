<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;
use Exception;

class AllInOneHelper
{
    /*========================
     * SECURITY FUNCTIONS
     ========================*/

    /**
     * Escape output untuk XSS
     */
    public static function escape($input)
    {
        return e($input);
    }

    /**
     * Clean HTML input menggunakan Purifier
     */
    public static function sanitize($input)
    {
        return Purifier::clean($input);
    }

    /*========================
     * FILE UPLOAD FUNCTIONS
     ========================*/

    /**
     * Upload satu file aman dengan status
     */
    public static function uploadFile(
        $file,
        string $baseFolder = 'uploads',
        array $allowedMime = ['jpg','png','jpeg','pdf'],
        int $maxSizeKB = 2048,
        bool $randomName = true
    ): array
    {
        try {
            // Validasi file
            $rules = [
                'file' => 'required|file|mimes:' . implode(',', $allowedMime) . '|max:' . $maxSizeKB
            ];
            validator(['file' => $file], $rules)->validate();

            // Buat folder otomatis berdasarkan tanggal
            $folder = $baseFolder . '/' . date('Y/m/d');

            $originalName = $file->getClientOriginalName();
            $storedName = $randomName
                ? time() . '_' . preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $originalName)
                : $originalName;

            $path = $file->storeAs($folder, $storedName);

            return [
                'success' => true,
                'message' => 'File berhasil diupload',
                'file' => [
                    'original_name' => $originalName,
                    'stored_name' => $storedName,
                    'path' => $path,
                    'url' => Storage::url($path)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload gagal: ' . $e->getMessage(),
                'file' => null
            ];
        }
    }

    /**
     * Upload multiple files sekaligus
     */
    public static function uploadMultipleFiles(
        array $files,
        string $baseFolder = 'uploads',
        array $allowedMime = ['jpg','png','jpeg','pdf'],
        int $maxSizeKB = 2048
    ): array
    {
        $results = [];
        foreach ($files as $file) {
            $results[] = self::uploadFile($file, $baseFolder, $allowedMime, $maxSizeKB);
        }
        return $results;
    }

    /**
     * Generate URL file untuk diakses
     */
    public static function fileUrl($path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    /*========================
     * GET PARAMETER FUNCTIONS
     ========================*/

    /**
     * Ambil GET parameter sebagai integer
     */
    public static function getInt($request, string $key, $default = null): ?int
    {
        $value = $request->query($key, $default);
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Ambil GET parameter sebagai string aman
     */
    public static function getString($request, string $key, $default = null, int $maxLength = 255): ?string
    {
        $value = $request->query($key, $default);
        if ($value === null) return null;

        // Hapus tag HTML
        $value = strip_tags($value);

        // Hapus karakter non-printable / kontrol
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);

        // Batasi panjang string
        return substr($value, 0, $maxLength);
    }

    /**
     * Ambil GET parameter sebagai float
     */
    public static function getFloat($request, string $key, $default = null): ?float
    {
        $value = $request->query($key, $default);
        return filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * Ambil GET parameter sebagai email
     */
    public static function getEmail($request, string $key, $default = null): ?string
    {
        $value = $request->query($key, $default);
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
    }
}


/* contoh penggunaan 
use App\Helpers\AllInOneHelper;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // Sanitasi input
        $comment = AllInOneHelper::sanitize($request->input('comment'));

        $fileResult = null;

        // Upload file
        if ($request->hasFile('file')) {
            $fileResult = AllInOneHelper::uploadFile(
                $request->file('file'),
                'uploads',
                ['pdf','jpg','png'],
                2048
            );

            if (!$fileResult['success']) {
                return back()->with('error', $fileResult['message']);
            }
        }

        // Simpan ke DB
        Post::create([
            'comment' => $comment,
            'file_path' => $fileResult['file']['path'] ?? null
        ]);

        return back()->with('success', 'Post berhasil disimpan');
    }

    public function show(Request $request, Post $post)
    {
        // GET parameter aman
        $highlightId = AllInOneHelper::getInt($request, 'highlight');

        $fileUrl = AllInOneHelper::fileUrl($post->file_path);

        return view('post.show', compact('post', 'fileUrl', 'highlightId'));
    }
}
<p>{{ AllInOneHelper::escape($post->comment) }}</p>

@if($fileUrl)
    <a href="{{ $fileUrl }}" target="_blank">Download File</a>
@endif

@if($highlightId)
    <p>Highlight post ID: {{ $highlightId }}</p>
@endif

*/