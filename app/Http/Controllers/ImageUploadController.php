<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Upload;
use App\Models\Product;
use App\Jobs\ProcessImageVariantsJob;
use KalynaSolutions\Tus\Tus;

class ImageUploadController extends Controller
{
    protected $tus;

    // Inject the KalynaSolutions TusServer instance
    public function __construct(Tus $tus)
    {
        $this->tus = $tus;
    }

    public function handleTusUpload(Request $request)
    {
        // The Tus object automatically handles all TUS protocol logic 
        // (including resumability checks via HEAD requests and chunking via PATCH).
        return $this->tus->handle();
    }


    public function linkImage(Request $request, string $sku)
    {
        return "Function called";
    }


    public function imageUpload()
    {
        return view('image_upload');
    }
}
