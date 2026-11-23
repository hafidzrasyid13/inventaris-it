<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount('assets')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:categories,kode'],
            'nama' => ['required', 'string', 'max:100'],
        ]);

        Category::create($data);

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->assets()->withTrashed()->exists()) {
            return back()->with('error', 'Kategori masih dipakai oleh aset, tidak bisa dihapus.');
        }

        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
