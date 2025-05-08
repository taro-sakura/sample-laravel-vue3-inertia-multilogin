<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LogController extends Controller
{
    public function index()
    {
        $logs = [];
        $files = Storage::files('logs');

        foreach ($files as $file) {
            $logs[] = [
                'name' => basename($file),
                'size' => Storage::size($file),
                'last_modified' => Storage::lastModified($file),
            ];
        }

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $logs,
        ]);
    }

    public function show($filename)
    {
        $path = 'logs/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404);
        }

        $content = Storage::get($path);

        return Inertia::render('Admin/Logs/Show', [
            'filename' => $filename,
            'content' => $content,
        ]);
    }

    public function download($filename)
    {
        $path = 'logs/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }

    public function destroy($filename)
    {
        $path = 'logs/' . $filename;
        
        if (!Storage::exists($path)) {
            abort(404);
        }

        Storage::delete($path);

        return redirect()->route('admin.logs.index')
            ->with('success', 'ログファイルを削除しました。');
    }
} 