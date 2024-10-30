<?php

namespace App\Http\Controllers;

use App\Actions\GenerateKmeans;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('file');

        $filename = $file->getClientOriginalName();
        $uploadedName = Uuid::uuid4()->toString() . ".pdf";

        Storage::disk('public')->putFileAs('files', $file, $uploadedName);

        File::create([
            "real_name" => $filename,
            "upload_name" => $uploadedName,
        ]);

        $action = new GenerateKmeans();
        $action->generate();

        return redirect(route('dashboard.index'));
    }
}
