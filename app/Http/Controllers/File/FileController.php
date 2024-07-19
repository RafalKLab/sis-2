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
        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('orders.index')->with(ConfigDefaultInterface::FLASH_ERROR, 'Order not found');
        }

        return view('main.user.order.file.index', compact('order'));
    }

    public function upload(int $orderId)
    {
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
                    ['file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf,psd|max:5120']
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
                        'file_path' => $path
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
}
