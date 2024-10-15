<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\files;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = files::orderBy('id', 'desc')->get();
        return view('download', compact('files'));
    }

    public function store(Request $request)
    {
        $downloadLinks = [];       
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $uploadedFile) {
                $existingFile = files::where('file_name', $uploadedFile->getClientOriginalName())->first();
                
                if (!$existingFile) {
                    $fileName = $uploadedFile->getClientOriginalName();
                    $filePath = 'public/img/' . $fileName;
                    $file = new files();
                    $file->file_name = $fileName;
                    $file->file_path = $filePath;
                    $file->file_type = $uploadedFile->getClientMimeType();
                    $file->file_size = $uploadedFile->getSize();
                    $file->save();
                    $uploadedFile->move(public_path('img'), $fileName);
                    $downloadLinks[] = url('http://127.0.0.1:8000/download/' . $file->id);
                } else {
                    continue;
                }
            }
        }

        return response()->json([
            'message' => 'Tải danh sách Files thành công',
            'downloadLinks' => $downloadLinks
        ]);
    }

    public function download($id)
    {
        $file = files::find($id);
        if ($file) {
            $path = public_path('img/'. $file->file_name);
            return response()->download($path);
        }
        return redirect()->back()->with('error', 'File không tồn tại.');
    }






    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        
    }

    public function destroy(string $id)
    {
        
    }
}
