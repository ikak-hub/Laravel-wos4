<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;

class VendorController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────────
    public function showLogin()
    {
        if (session('vendor_id')) {
            return redirect()->route('kantor.dashboard');
        }
        return view('kantor.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $vendor = Vendor::where('username', $request->username)->first();

        if (!$vendor || !Hash::check($request->password, $vendor->password)) {
            return back()->with('error', 'Username atau password salah.');
        }

        session([
            'vendor_id'   => $vendor->idvendor,
            'vendor_name' => $vendor->nama_vendor,
        ]);

        return redirect()->route('kantor.dashboard');
    }

    public function logout()
    {
        session()->forget(['vendor_id', 'vendor_name']);
        return redirect()->route('kantor.login');
    }

    // ── Dashboard ─────────────────────────────────────────────────
    public function dashboard()
    {
        $vendor          = Vendor::findOrFail(session('vendor_id'));
        $totalMenu       = $vendor->menus()->count();
        $totalOrders     = $vendor->pesanans()->where('status_bayar', 1)->count();
        $totalPendapatan = $vendor->pesanans()->where('status_bayar', 1)->sum('total');

        return view('kantor.dashboard', compact(
            'vendor', 'totalMenu', 'totalOrders', 'totalPendapatan'
        ));
    }

    // ── Menu Management ───────────────────────────────────────────
    public function menuIndex()
    {
        $vendor = Vendor::findOrFail(session('vendor_id'));
        $menus  = Menu::where('idvendor', $vendor->idvendor)->latest()->get();
        return view('kantor.menu.index', compact('menus', 'vendor'));
    }

    public function menuStore(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:0',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu', 'public');
        }

        Menu::create([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $path,
            'idvendor'    => session('vendor_id'),
        ]);

        return redirect()->route('kantor.menu')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function menuUpdate(Request $request, $id)
    {
        $menu = Menu::where('idmenu', $id)
            ->where('idvendor', session('vendor_id'))
            ->firstOrFail();

        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga'     => 'required|integer|min:0',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $menu->path_gambar;
        if ($request->hasFile('gambar')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('gambar')->store('menu', 'public');
        }

        $menu->update([
            'nama_menu'   => $request->nama_menu,
            'harga'       => $request->harga,
            'path_gambar' => $path,
        ]);

        return redirect()->route('kantor.menu')
            ->with('success', 'Menu berhasil diupdate!');
    }

    public function menuDestroy($id)
    {
        $menu = Menu::where('idmenu', $id)
            ->where('idvendor', session('vendor_id'))
            ->firstOrFail();

        if ($menu->path_gambar) {
            Storage::disk('public')->delete($menu->path_gambar);
        }
        $menu->delete();

        return redirect()->route('kantor.menu')
            ->with('success', 'Menu berhasil dihapus!');
    }

    // ── Pesanan Lunas ─────────────────────────────────────────────
    public function orders()
    {
        $vendor = Vendor::findOrFail(session('vendor_id'));
        $orders = Pesanan::where('idvendor', $vendor->idvendor)
            ->where('status_bayar', 1)
            ->with(['details.menu'])
            ->latest()
            ->get();

        return view('kantor.orders', compact('orders', 'vendor'));
    }
}