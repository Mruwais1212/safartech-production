<?php

namespace App\Http\Controllers\Panel\Backend;

use App\Http\Controllers\Panel\Controller;
use App\Models\TBOHotel;
use Illuminate\Http\Request;

class CacheHotelController extends Controller
{
    public function index()
    {
        // $hotels = TBOHotel::all();

        return view('admin.backend.cache-hotel.index');
    }

    public function allhotels(Request $request)
    {

        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'body',
            3 => 'created_at',
            4 => 'id',
        ];

        $totalData = TBOHotel::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $posts = TBOHotel::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $posts = TBOHotel::where('id', 'LIKE', "%{$search}%")
                ->orWhere('name_ar', 'LIKE', "%{$search}%")
                ->orWhere('name_en', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('city_name', 'LIKE', "%{$search}%")
                ->orWhere('country_name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = TBOHotel::where('id', 'LIKE', "%{$search}%")
                ->orWhere('name_ar', 'LIKE', "%{$search}%")
                ->orWhere('name_en', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('city_name', 'LIKE', "%{$search}%")
                ->orWhere('country_name', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = [];
        if (! empty($posts)) {
            foreach ($posts as $post) {
                $edit = '/admin-panel/cached-hotels/'.$post->id.'/edit';
                $images = json_decode($post->images, true);
                $firstImage = isset($images[0]) ? $images[0] : url('/no-image.png');

                $nestedData['id'] = $post->id;
                $nestedData['name'] = $post->name_ar ?? $post->name_en;
                $nestedData['image'] = "<img src=\"{$firstImage}\" style=\"width: 50px; height: 50px;\">";

                $nestedData['phone'] = $post->phone;
                $nestedData['address'] = $post->address;
                $nestedData['city_name'] = $post->city_name;
                $nestedData['country_name'] = $post->country_name;
                $nestedData['options'] = '<ul class="icons-list">
                                        <li class="text-primary-600">
                                            <a href="'.$edit.'" data-popup="tooltip" title="Edit" data-placement="top">
                                                <i class="icon-pencil7"></i>
                                            </a>
                                        </li>
                                    </ul>';
                $data[] = $nestedData;
            }
        }

        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ];

        echo json_encode($json_data);
    }

    public function edit($id)
    {
        $hotel = TBOHotel::findOrFail($id);

        return view('admin.backend.cache-hotel.add', compact('hotel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePaths = [];

        $hotel = TBOHotel::findOrFail($id);
        $hotel->name_en = $request->name_en;
        $hotel->name_ar = $request->name_ar;

        $existingImages = json_decode($hotel->images, true) ?: [];
        $imagePaths = array_merge($existingImages, $imagePaths);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $imagePaths[] = asset('storage/'.$path);
            }
        }

        $hotel->images = json_encode($imagePaths);
        $hotel->save();

        return back()->with('success', __('dashboard.updated_successfully'));
    }

    public function deleteImage($id, Request $request)
    {
        $record = TBOHotel::findOrFail($id);

        $images = json_decode($record->images, true);

        $imageToDelete = $request->image_url;

        if (($key = array_search($imageToDelete, $images)) !== false) {
            unset($images[$key]);
        }

        $record->images = json_encode(array_values($images));
        $record->save();

        return back()->with('success', __('dashboard.photo_deleted_successfully'));
    }
}
