<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Message;
use Intervention\Image\Facades\Image;

class FileService
{
    protected $allowedTypes = [
        'image' => ['jpg', 'jpeg', 'png', 'gif'],
        'document' => ['pdf', 'doc', 'docx', 'txt'],
        'audio' => ['mp3', 'wav'],
        'video' => ['mp4', 'avi', 'mov']
    ];

    protected $maxFileSize = 10485760; // 10MB

    public function attachToMessage(Message $message, $file)
    {
        if (!$this->validateFile($file)) {
            throw new \Exception('Geçersiz dosya formatı veya boyutu');
        }

        $path = $this->storeFile($file);
        
        $metadata = $message->metadata ?? [];
        $metadata['attachments'][] = [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()
        ];

        $message->metadata = $metadata;
        $message->save();

        return $path;
    }

    protected function validateFile($file)
    {
        // Dosya boyutu kontrolü
        if ($file->getSize() > $this->maxFileSize) {
            return false;
        }

        // Dosya tipi kontrolü
        $extension = strtolower($file->getClientOriginalExtension());
        foreach ($this->allowedTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return true;
            }
        }

        return false;
    }

    protected function storeFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Resim optimizasyonu
        if ($this->isImage($extension)) {
            return $this->storeImage($file, $filename);
        }

        return $file->storeAs('attachments', $filename, 'public');
    }

    protected function isImage($extension)
    {
        return in_array($extension, $this->allowedTypes['image']);
    }

    protected function storeImage($file, $filename)
    {
        $image = Image::make($file);

        // Görüntü boyutunu optimize et
        if ($image->width() > 2000) {
            $image->resize(2000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Kaliteyi düşür ama görünür fark olmasın
        $image->save(storage_path('app/public/attachments/' . $filename), 80);

        return 'attachments/' . $filename;
    }

    public function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return true;
        }
        return false;
    }

    public function getFileUrl($path)
    {
        return Storage::disk('public')->url($path);
    }

    public function getFileMetadata($path)
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        $file = Storage::disk('public')->path($path);
        
        return [
            'size' => filesize($file),
            'mime_type' => mime_content_type($file),
            'last_modified' => filemtime($file),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'is_image' => $this->isImage(pathinfo($path, PATHINFO_EXTENSION))
        ];
    }

    public function createThumbnail($path, $width = 150, $height = 150)
    {
        if (!$this->isImage(pathinfo($path, PATHINFO_EXTENSION))) {
            return null;
        }

        $originalFile = Storage::disk('public')->path($path);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $thumbnailPath = "thumbnails/{$filename}_{$width}x{$height}.{$extension}";

        $image = Image::make($originalFile);
        $image->fit($width, $height);
        $image->save(storage_path('app/public/' . $thumbnailPath));

        return $thumbnailPath;
    }

    public function moveFile($oldPath, $newPath)
    {
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
            return true;
        }
        return false;
    }

    public function copyFile($sourcePath, $destinationPath)
    {
        if (Storage::disk('public')->exists($sourcePath)) {
            Storage::disk('public')->copy($sourcePath, $destinationPath);
            return true;
        }
        return false;
    }

    // ZIP işlemleri
    public function createZipArchive($files, $zipName)
    {
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/public/temp/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $filePath = Storage::disk('public')->path($file);
                $zip->addFile($filePath, basename($file));
            }
            $zip->close();
            return 'temp/' . $zipName;
        }

        return null;
    }

    public function cleanupTempFiles()
    {
        $files = Storage::disk('public')->files('temp');
        foreach ($files as $file) {
            $filePath = Storage::disk('public')->path($file);
            if (filemtime($filePath) < time() - 3600) { // 1 saat önce oluşturulan geçici dosyaları sil
                Storage::disk('public')->delete($file);
            }
        }
    }

    // PDF işlemleri
    public function mergePDFs($files, $outputName)
    {
        $pdf = new \Spatie\PdfMerger\Merger();

        foreach ($files as $file) {
            $filePath = Storage::disk('public')->path($file);
            $pdf->addFile($filePath);
        }

        $outputPath = storage_path('app/public/merged/' . $outputName);
        $pdf->merge();
        $pdf->save($outputPath);

        return 'merged/' . $outputName;
    }

    public function optimizePDF($path)
    {
        $inputFile = Storage::disk('public')->path($path);
        $outputFile = Storage::disk('public')->path('optimized_' . basename($path));

        exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile={$outputFile} {$inputFile}");

        return 'optimized_' . basename($path);
    }
}