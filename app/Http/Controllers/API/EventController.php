<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $event = Event::with('venue')->where('status', 'active')->first();
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active concert event found.',
            ], 404);
        }
        return response()->json($event);
    }

    public function vendors()
    {
        $event = Event::where('status', 'active')->first();
        if (!$event) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active concert event found.',
            ], 404);
        }

        $vendors = Vendor::with(['products' => function($q) {
            $q->orderBy('name');
        }])->where('event_id', $event->id)
           ->where('status', 'active')
           ->get();

        return response()->json($vendors);
    }

    public function toggleProductStock(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $newStatus = $product->stock_status === 'in_stock' ? 'out_of_stock' : 'in_stock';
        
        $product->update([
            'stock_status' => $newStatus
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product stock status updated to ' . $newStatus,
            'product' => $product
        ]);
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
        ]);

        $image_url = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/uploads'), $filename);
            $image_url = '/images/uploads/' . $filename;
        } else {
            $gradients = [
                'bg-gradient-to-br from-amber-400 to-red-500',
                'bg-gradient-to-br from-green-400 to-blue-500',
                'bg-gradient-to-br from-purple-400 to-pink-500',
                'bg-gradient-to-br from-blue-400 to-indigo-500',
            ];
            $image_url = $gradients[array_rand($gradients)];
        }

        $product = Product::create([
            'vendor_id' => $data['vendor_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'stock_status' => $data['stock_status'],
            'image_url' => $image_url,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/uploads'), $filename);
            $product->image_url = '/images/uploads/' . $filename;
        }

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_status' => $data['stock_status'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
