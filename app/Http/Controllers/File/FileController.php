<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\MainController;
use App\Models\Order\File;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use shared\ConfigDefaultInterface;

class FileController extends MainController
{
    public function index(int $orderId)
    {
        if (!(Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_UPLOAD_FILE) || Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_SEE_UPLOADED_FILES))) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'User do not has permission to see uploaded files');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        $fileSizes = [];
        foreach ($order->files as $file) {
            $fileSizes[$file->id] = [
                'formatted' => $file->formatSize(),
                'raw' => (int) $file->size,
            ];
        }

        return view('main.user.order.file.index', compact('order', 'fileSizes'));
    }

    public function upload(int $orderId)
    {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_UPLOAD_FILE)) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'User do not has permission to upload files');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        return view('main.user.order.file.upload', compact('order'));
    }

    public function store(Request $request)
    {
        // Retrieve order_id from the request
        $orderId = $request->input('order_id');
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 400);
        }

        $userId = Auth::user()->id;


        $response = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $validator = Validator::make(
                    ['file' => $file],
                    ['file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,psd,txt,svg,csv|max:40120']
                );

                if ($validator->fails()) {
                    // Return a 400 Bad Request with the validation errors
                    return response()->json([
                        'name' => $file->getClientOriginalName(),
                        'status' => 'error',
                        'message' => $validator->errors()->first('file')
                    ], 400);
                }

                try {
                    // Generate a directory name using the Order Key
                    $orderKey = $order->getKeyField();
                    $directory = 'uploads/' . $orderKey;

                    // Generate a unique file name using the full date
                    $date = now()->format('Y-m-d_H-i-s');
                    $fileName = $date . '_' . $file->getClientOriginalName();

                    // Save the file
                    $path = $file->storeAs($directory, $fileName, ConfigDefaultInterface::FILE_SYSTEM_PRIVATE);

                    File::create([
                        'order_id' => $orderId,
                        'user_id' => $userId,
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'size' => $file->getSize(),
                    ]);

                    // Add to response
                    $response[] = [
                        'name' => $fileName,
                        'status' => 'success',
                        'path' => Storage::url($path),
                        'message' => 'File uploaded successfully'
                    ];
                } catch (\Exception $e) {
                    $response[] = [
                        'name' => $file->getClientOriginalName(),
                        'status' => 'error',
                        'message' => 'Server error while uploading'
                    ];
                }
            }
        } else {
            $response[] = [
                'status' => 'error',
                'message' => 'No files provided for upload.'
            ];
        }

        return response()->json($response);
    }

    public function show(int $fileId)
    {
        $file = File::find($fileId);
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        $storageDisk = ConfigDefaultInterface::FILE_SYSTEM_PRIVATE;
        $filePath = 'uploads/' . $file->order->getKeyField() . '/' . $file->file_name;

        if (Storage::disk($storageDisk)->exists($filePath)) {
            $fileContent = Storage::disk($storageDisk)->get($filePath);
            $fileType = Storage::disk($storageDisk)->mimeType($filePath);

            // Encode the content in base64 for inline display
            $base64Content = base64_encode($fileContent);
            $src = 'data:' . $fileType . ';base64,' . $base64Content;

            return response()->json([
                'success' => true,
                'src' => $src,
                'fileType' => $fileType,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }
    }

    public function download(int $fileId)
    {
        $file = File::find($fileId);
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        $storageDisk = ConfigDefaultInterface::FILE_SYSTEM_PRIVATE;
        $filePath = 'uploads/' . $file->order->getKeyField() . '/' . $file->file_name;

        if (Storage::disk($storageDisk)->exists($filePath)) {
            $fileType = Storage::disk($storageDisk)->mimeType($filePath);
            $fileName = $file->file_name;

            return Storage::disk($storageDisk)->download($filePath, $fileName, [
                'Content-Type' => $fileType,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }
    }

    public function delete(int $fileId) {
        if (!Auth::user()->hasPermissionTo(ConfigDefaultInterface::PERMISSION_DELETE_UPLOADED_FILES)) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'User do not has permission to upload files');
        }

        $file = File::find($fileId);
        if (!$file) {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'File not found');
        }

        // Delete the file from storage and database
        $deleted = Storage::disk(ConfigDefaultInterface::FILE_SYSTEM_PRIVATE)->delete('uploads/' . $file->order->getKeyField() . '/' . $file->file_name);
        if ($deleted) {
            $file->delete();

            return redirect()->back()->with(ConfigDefaultInterface::FLASH_SUCCESS, 'File successfully deleted');
        } else {
            return redirect()->back()->with(ConfigDefaultInterface::FLASH_ERROR, 'Failed to delete the file');
        }
    }
}
