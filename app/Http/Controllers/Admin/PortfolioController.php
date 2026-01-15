<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $portfolios= Portfolio::with('category', 'images')->get();
        return view('admin.portfolio.index',compact('portfolios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.portfolio.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:4',
            'project_url' => 'required',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'cat_id' => 'required|exists:categories,id'
        ]);

        $portfolio = new Portfolio();
        $portfolio->title = $validated['title'];
        $portfolio->project_url = $validated['project_url'];
        $portfolio->cat_id = $request->cat_id;
        $portfolio->save();

        // Handle multiple image uploads
        if($request->hasfile('images')){
            foreach($request->file('images') as $index => $image){
                $path = $image->store('images/portfolios', 'public');
                PortfolioImage::create([
                    'portfolio_id' => $portfolio->id,
                    'image' => $path,
                    'is_primary' => $index === 0, // first image is primary
                    'sort_order' => $index
                ]);
            }
        }

        return to_route('admin.portfolio.index')->with('message','Portfolio Added');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Portfolio $portfolio)
    {
        $categories = Category::all();
        $portfolio->load('images');
        return view('admin.portfolio.edit', compact('portfolio','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        $validated = $request->validate([
            'title' => 'required|min:4',
            'project_url' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $portfolio->title = $validated['title'];
        $portfolio->project_url = $validated['project_url'];
        $portfolio->cat_id = $request->cat_id;

        // Handle deleting selected images
        if($request->has('delete_images')){
            foreach($request->delete_images as $imageId){
                $image = PortfolioImage::find($imageId);
                if($image){
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
            }
        }

        // Handle setting primary image
        if($request->has('primary_image')){
            $portfolio->images()->update(['is_primary' => false]);
            PortfolioImage::where('id', $request->primary_image)->update(['is_primary' => true]);
        }

        // Handle new image uploads
        if($request->hasfile('images')){
            $lastOrder = $portfolio->images()->max('sort_order') ?? -1;
            foreach($request->file('images') as $index => $image){
                $path = $image->store('images/portfolios', 'public');
                PortfolioImage::create([
                    'portfolio_id' => $portfolio->id,
                    'image' => $path,
                    'is_primary' => false,
                    'sort_order' => $lastOrder + $index + 1
                ]);
            }
        }

        $portfolio->update();
        return to_route('admin.portfolio.index')->with('message','Portfolio Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portfolio $portfolio)
    {
        // Delete all related images from storage
        foreach($portfolio->images as $image){
            Storage::disk('public')->delete($image->image);
        }

        // Delete legacy image if exists
        if($portfolio->image != null){
            Storage::disk('public')->delete($portfolio->image);
        }

        $portfolio->delete();
        return back()->with('message', 'Portfolio Deleted');
    }

    public function search(Request $request)
    {
        $searchedItem = $request->input('search');

        $portfolios = Portfolio::query()
        ->where('title', 'LIKE', "%{$searchedItem}%")
        ->orWhere('project_url', 'LIKE', "%{$searchedItem}%")
        ->get();


    // Return the search view with the resluts compacted
    return view('admin.portfolio.search', compact('portfolios'));

    }

    /**
     * Delete a single image via AJAX
     */
    public function deleteImage(PortfolioImage $image)
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();
        return response()->json(['success' => true]);
    }
}
