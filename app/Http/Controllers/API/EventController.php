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
                'status'  => 'error',
                'message' => 'No active concert event found.',
            ], 404);
        }
        return response()->json($event);
    }

    public function settings()
    {
        $deliveryFee = floatval(\App\Models\Setting::get('delivery_fee', 30));
        return response()->json([
            'status'       => 'success',
            'delivery_fee' => $deliveryFee,
        ]);
    }

    public function vendors(Request $request)
    {
        $event = Event::where('status', 'active')->first();

        $query = Vendor::query()
            ->with(['products' => function($q) {
                $q->select(['id', 'vendor_id', 'name', 'description', 'price', 'image_url', 'stock_status'])->orderBy('name');
            }])
            ->where('status', 'active');

        if ($event) {
            $query->where(function($q) use ($event) {
                $q->where('event_id', $event->id)
                  ->orWhereNull('event_id');
            });
        }

        if ($request->has('page') || $request->has('per_page')) {
            $perPage = min($request->integer('per_page', 25), 100);
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    public function toggleProductStock(Request $request, $productId)
    {
        $vendor = $request->user()->vendor;
        if (!$vendor && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Vendor profile not found.'], 404);
        }

        $product = Product::findOrFail($productId);
        $this->authorize('update', $product);

        $newStatus = $product->stock_status === 'in_stock' ? 'out_of_stock' : 'in_stock';

        $product->update([
            'stock_status' => $newStatus
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product stock status updated to ' . $newStatus,
            'product' => $product
        ]);
    }

    public function storeProduct(Request $request)
    {
        $this->authorize('create', Product::class);

        $vendor = $request->user()->vendor;
        if (!$vendor && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Vendor profile not found.'], 404);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
        ]);

        $image_url = null;
        if ($request->hasFile('image')) {
            $file     = $request->file('image');
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
            'vendor_id'    => $vendor ? $vendor->id : $request->input('vendor_id'),
            'name'         => $data['name'],
            'description'  => $data['description'] ?? '',
            'price'        => $data['price'],
            'stock_status' => $data['stock_status'],
            'image_url'    => $image_url,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'product' => $product
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock_status' => 'required|in:in_stock,out_of_stock',
        ]);

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/uploads'), $filename);
            $product->image_url = '/images/uploads/' . $filename;
        }

        $product->update([
            'name'         => $data['name'],
            'description'  => $data['description'],
            'price'        => $data['price'],
            'stock_status' => $data['stock_status'],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroyProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
