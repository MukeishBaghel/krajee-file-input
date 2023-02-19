<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;


class FilesController extends Controller
{

    public function index()
    {
        $files = File::all();        
        $initialPreview = [];
        $initialPreviewConfig = [];
        foreach ($files as $key => $file) {
            // storage symlinking is required
            // php artisan storage:link
            $imgPath = url(str_replace('public', 'storage', $file->path));        
            $img  = '<img src="' . $imgPath . '" class="file-preview-image" alt="'.$file->name.'" title="'.$file->name.'"  />';            
            $initialPreview[] = $img;
            $initialPreviewConfig[] = [
                'caption' => $file->name,               
                'url' => route('delete'),
                'key' => $file->id,
                'extra' => ['id' =>  $file->id],
                'downloadUrl' => route('download', ['id' => $file->id]),
            ];
        }
        return view('welcome')->with([
            'initialPreview' => $initialPreview,
            'initialPreviewConfig' => $initialPreviewConfig
        ]);
    }

    public function upload(Request $request)
    {   
        $files = $request->input;
        foreach ($files as $key => $value) {
            $fileName = $value->store('public/images');

            File::create([
                'name' => $value->getClientOriginalName(),
                'path' => $fileName,
            ]);
        }
        return response()->json([]);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $file = File::find($id);
        Storage::delete($file->path);
        $file->delete();
        return response()->json([]);
    }

    public function download($id, Request $request)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }

}
