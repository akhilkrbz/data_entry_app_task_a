<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Upload;
use App\Models\Image;
use Intervention\Image\ImageManagerStatic as ImageManager;
use Illuminate\Support\Facades\Storage;

class ProcessImageVariantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadId;

    public function __construct(int $uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function handle(): void
    {
        // 1. Load the Upload record
        $upload = Upload::findOrFail($this->uploadId);
        $disk = $upload->disk;
        $originalFilePath = $upload->path;
        
        $originalImageContent = Storage::disk($disk)->get($originalFilePath);
        
        $variants = [256, 512, 1024];

        foreach ($variants as $size) {
            // 2. Generate Variant (respecting aspect ratio)
            $img = ImageManager::make($originalImageContent);

            // Use fit to maintain aspect ratio while ensuring the image fits the canvas size
            $img->fit($size, $size); 

            // 3. Define Path and Save
            $variantPath = str_replace('.', "_{$size}px.", $originalFilePath);
            Storage::disk($disk)->put($variantPath, (string) $img->encode());

            // 4. Create Image DB Record
            Image::create([
                'upload_id' => $upload->id,
                'path' => $variantPath,
                'size_px' => $size,
            ]);
        }
        
        // 5. Final Status Update
        $upload->update(['status' => 'processed']);
    }
}