<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function index()
    {
        $user_id=2;
        $basePath = "uploads/{$user_id}";
        $folders = Storage::disk('public')->directories($basePath);

        $data = [];
        foreach ($folders as $folder) {
            $images = Storage::disk('public')->files($folder);
            $data[] = [
                'folder' => basename($folder),
                'path' => $folder,
                'images' => $images
            ];
        }

        return view('admin.file-manager.file-manager', compact('data', 'user_id'));
    }

    public function upload1(Request $request)
    {
        $request->validate([
           
            'folder' => 'required',
            'images.*' => 'required|image|max:2048'
        ]);

        foreach ($request->file('images') as $file) {
            $file->store("uploads/2/{$request->folder}", 'public');
        }

        return back()->with('success', 'Images uploaded');
    }

    public function createFolder(Request $request)
    {
        $request->validate([
           
            'folder' => 'required|string'
        ]);

        $path = "uploads/2/{$request->folder}";
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        return back()->with('success', 'Folder created');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'folders' => 'array',
            'images' => 'array'
        ]);

        foreach ($request->folders ?? [] as $folder) {
            Storage::disk('public')->deleteDirectory($folder);
        }

        foreach ($request->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }

        return back()->with('success', 'Selected items deleted');
    }
    public function showFolderImages($folderName)
    {
        $user_id =2;
        $folderPath = "uploads/{$user_id}/{$folderName}";

        if (!Storage::disk('public')->exists($folderPath)) {
            abort(404, 'Folder not found');
        }

        $images = Storage::disk('public')->files($folderPath);

        return view('admin.file-manager.folder-images', compact('images', 'folderName'));
    }
    // app/Http/Controllers/FileManagerController.php

public function deleteFolder(Request $request)
{
    $folderName = $request->folder;
    $user_id = auth()->id();
    $folderPath = storage_path("app/public/uploads/{$user_id}/{$folderName}");

    // Check if folder exists
    if (is_dir($folderPath)) {
        // Delete the folder and its contents
        $this->deleteDirectory($folderPath);

        return response()->json(['message' => 'Folder deleted successfully.']);
    }

    return response()->json(['message' => 'Folder not found.'], 404);
}

private function deleteDirectory($dirPath)
{
    // Delete all files and folders inside the directory
    $files = glob("{$dirPath}/*");
    foreach ($files as $file) {
        is_dir($file) ? $this->deleteDirectory($file) : unlink($file);
    }

    // Remove the folder itself
    rmdir($dirPath);
}

}
