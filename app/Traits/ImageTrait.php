<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageTrait
{
    public function upload(UploadedFile $image, string $folder, ?string $name = null)
    {
        $imageName = $name
            ? Str::slug($name) . '.' . $image->getClientOriginalExtension()
            : 'IMG_' . Str::uuid7(now()) . '.' . $image->getClientOriginalExtension();

        $path = "{$folder}/{$imageName}";
        Storage::disk('public')->put($path, file_get_contents($image));
    }

    public function update(UploadedFile $image, $oldPath, $folder, $name)
    {
        $this->deleteImage($oldPath);
        return $this->uploadImage($image, $folder, $name);
    }
    public function delete($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
    public function getImageUrl($path)
    {
        return $path ? asset('storage/' . $path) : null;
    }
}
