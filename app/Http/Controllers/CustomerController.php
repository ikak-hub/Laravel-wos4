<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;


class CustomerController extends Controller
{
  // Data Customer
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    // Form Tambah Customer 1 (blob)
    public function createBlob()
    {
        return view('customer.create_blob');
    }

    // Simpan Customer 1 (foto sebagai blob base64)
    public function storeBlob(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'foto_blob' => 'required|string', // base64 dari kamera
        ]);

        Customer::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'foto_blob' => $request->foto_blob, // simpan base64 langsung
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Customer berhasil ditambahkan! (blob)');
    }

    // Form Tambah Customer 2 (file path)
    public function createFile()
    {
        return view('customer.create_file');
    }

    // Simpan Customer 2 (foto sebagai file)
    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'foto'  => 'required|string', // base64 dari kamera
        ]);

        // Decode base64 dan simpan sebagai file
        $base64 = $request->foto;
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($base64);
        $filename = 'customer_' . time() . '.jpg';
        Storage::disk('public')->put('customers/' . $filename, $imageData);

        Customer::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'foto_path' => 'customers/' . $filename,
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Customer berhasil ditambahkan! (file)');
    }

}
