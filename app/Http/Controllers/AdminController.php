<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class adminController extends Controller
{

    public function home()
    {
        return view('dashboard.admin.home');
    }


    // --------------------- Categorie -----------------------------

    public function indexCategorie()
    {
        $categories = Categorie::paginate(10);

        return view('dashboard.admin.Categories.indexCategorie', compact('categories'));
    }
    
    public function createCategorie()
    {
        return view('dashboard.admin.Categories.createCategorie');
    }

    public function storeCategorie(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images/categories'), $imageName);

        $categorie = new Categorie();
        $categorie->name = $request->name;
        $categorie->image_path = 'images/categories/' . $imageName;
        $categorie->save();

        return redirect()->route('categories.store')->with('success', 'Catégorie ajoutée avec succès.');
    }

    public function showCategorie($id)
    {
        $categorie = Categorie::findOrFail($id);
        
        return view('dashboard.admin.Categories.showCategorie', compact('categorie'));
    }

    public function editCategorie($id)
    {
        $categorie = Categorie::findOrFail($id);

        return view('dashboard.admin.Categories.editCategorie', compact('categorie'));
    }

    public function updateCategorie(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $categorie->image_path = 'images/categories/' . $imageName;
        }
        $categorie->name = $request->name;
        $categorie->save();

        return redirect()->route('categories.index')->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroyCategorie($id)
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->delete();
        return redirect()->route('categories.index')->with('success', 'Catégorie supprimée avec succès.');
    }




    // --------------------- Product -----------------------------


    // --------------------- Commandes -----------------------------
    public function createProduct()
    {
        $categories = Categorie::all();
        return view('dashboard.admin.Produits.createProduct', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Créer le produit
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->save();

        // Gérer le téléchargement des images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Stocker l'image dans le répertoire storage/app/public
                $imagePath = $image->store('public/images');
                // Récupérer le nom du fichier à partir du chemin stocké
                $imageName = basename($imagePath);
                // Créer une nouvelle entrée d'image associée au produit
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image_path = $imageName;
                $productImage->save();
            }
        }

        return redirect()->route('produits.create')->with('success', 'Produit ajouté avec succès.');
    }

    public function destroy($id)
    {
        // Récupérer le produit par son ID
        $product = Product::findOrFail($id);

        // Supprimer les images associées au produit du stockage
        foreach ($product->images as $image) {
            Storage::delete('public/images/' . $image->image_path);
        }

        // Supprimer le produit
        $product->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé avec succès.');
    }

    public function indexProduct()
    {
        // Récupérer toutes les catégories
        $categories = Categorie::all();

        // Récupérer tous les produits avec leurs catégories correspondantes paginés par 10 par page
        $productsWithCategories = Product::with('category')->paginate(10);

        return view('dashboard.admin.Produits.indexProduct', compact('categories', 'productsWithCategories'));
    }

    // --------------------- testimonials -----------------------------

    public function indexTestimonial()
    {
        $testimonials = Testimonial::all();

        return view('dashboard.admin.testimonial', compact('testimonials'));
    }

  // --------------------- testimonials -----------------------------

    public function indexComment()
    {
        $comments = Comment::all();

        return view('dashboard.admin.comment', compact('comments'));
    }

}
