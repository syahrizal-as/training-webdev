<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        //get all books
        $books = Book::latest()->get();

        //return collection of books as a resource
        return new BookResource(true, 'List Data Books', $books);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'harga'     => 'required',
            'stock'     => 'required',
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/books', $image->hashName());

        //create books
        $book = Book::create([
            'user_id'   => $request->user()->id,
            'name'      => $request->name,
            'harga'     => $request->harga,
            'stock'     => $request->stock,
            'image'     => $image->hashName(),
        ]);

        //return response
        return new BookResource(true, 'Data Book Berhasil Ditambahkan!', $book);
    }
    public function show($id)
    {
        //find book by ID
        $book = Book::find($id);

        //return single book as a resource
        return new BookResource(true, 'Detail Data Book!', $book);
    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'harga'     => 'required',
            'stock'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find book by ID
        $book = Book::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/books', $image->hashName());

            //delete old image
            Storage::delete('public/books/' . basename($book->image));

            //update book with new image
            $book->update([
                'image'     => $image->hashName(),
                'name'      => $request->name,
                'harga'     => $request->harga,
                'stock'     => $request->stock,
            ]);
        } else {

            //update post without image
            $book->update([
                'name'      => $request->name,
                'harga'     => $request->harga,
                'stock'     => $request->stock,
            ]);
        }

        //return response
        return new BookResource(true, 'Data Book Berhasil Diubah!', $book);
    }

    public function destroy($id)
    {

        //find book by ID
        $book = Book::find($id);

        //delete image
        Storage::delete('public/books/'.basename($book->image));

        //delete book
        $book->delete();

        //return response
        return new BookResource(true, 'Data Book Berhasil Dihapus!', null);
    }
}
