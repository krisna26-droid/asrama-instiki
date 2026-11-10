<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsramaSetting;
use App\Models\FasilitasAsrama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class KontenAsramaController extends Controller
{
    public function index()
    {
        $contact = AsramaSetting::query()->find(1) ?? new AsramaSetting();
        $facilities = FasilitasAsrama::query()->orderBy('urutan')->orderBy('id')->get();

        return view('admin.konten-asrama.index', compact('contact', 'facilities'));
    }

    public function updateContact(Request $request)
    {
        $data = $request->validate([
            'whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'telepon' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:3000'],
        ]);

        $contact = AsramaSetting::query()->find(1) ?? new AsramaSetting();
        if (! $contact->exists) {
            $contact->id = 1;
        }

        foreach (['whatsapp', 'telepon', 'email', 'alamat'] as $field) {
            $contact->{$field} = $data[$field] ?? null;
        }
        $contact->save();

        return back()->with('success', 'Informasi kontak asrama diperbarui.');
    }

    public function storeFacility(Request $request)
    {
        $data = $this->validatedFacility($request, true);
        $imagePath = $this->storeImage($request);

        try {
            DB::transaction(function () use ($data, $imagePath): void {
                $facility = new FasilitasAsrama();
                $this->assignFacilityFields($facility, $data);
                $facility->slug = $this->uniqueSlug($data['judul']);
                $facility->gambar_path = $imagePath;
                $facility->aktif = true;
                $facility->save();
            }, 3);
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);
            throw $exception;
        }

        return back()->with('success', 'Fasilitas asrama ditambahkan ke halaman publik.');
    }

    public function updateFacility(Request $request, FasilitasAsrama $facility)
    {
        $data = $this->validatedFacility($request, false);
        $newImagePath = $this->storeImage($request);
        $oldImagePath = $facility->gambar_path;

        try {
            DB::transaction(function () use ($facility, $data, $newImagePath): void {
                $lockedFacility = FasilitasAsrama::query()->whereKey($facility->id)->lockForUpdate()->firstOrFail();
                $this->assignFacilityFields($lockedFacility, $data);
                $lockedFacility->slug = $this->uniqueSlug($data['judul'], $lockedFacility->id);
                if ($newImagePath !== null) {
                    $lockedFacility->gambar_path = $newImagePath;
                }
                $lockedFacility->save();
            }, 3);
        } catch (Throwable $exception) {
            $this->deleteManagedImage($newImagePath);
            throw $exception;
        }

        if ($newImagePath !== null && $oldImagePath !== $newImagePath) {
            $this->deleteManagedImage($oldImagePath);
        }

        return back()->with('success', 'Detail fasilitas asrama diperbarui.');
    }

    public function toggleFacility(FasilitasAsrama $facility)
    {
        $facility->aktif = ! $facility->aktif;
        $facility->save();

        return back()->with('success', $facility->aktif
            ? 'Fasilitas ditampilkan pada halaman publik.'
            : 'Fasilitas diarsipkan dan disembunyikan dari halaman publik.');
    }

    private function validatedFacility(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'caption' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'gambar' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        $file = $request->file('gambar');
        if ($file === null) {
            return null;
        }

        if (! $file->isValid()) {
            throw ValidationException::withMessages(['gambar' => 'Foto fasilitas gagal diterima. Silakan pilih ulang file.']);
        }

        $path = $file->storePublicly('asrama/fasilitas', 'public');
        if (! is_string($path) || ! Storage::disk('public')->exists($path)) {
            throw ValidationException::withMessages(['gambar' => 'Foto fasilitas gagal disimpan. Silakan coba kembali.']);
        }

        return $path;
    }

    private function assignFacilityFields(FasilitasAsrama $facility, array $data): void
    {
        $facility->judul = $data['judul'];
        $facility->caption = $data['caption'];
        $facility->deskripsi = $data['deskripsi'] ?? null;
        $facility->urutan = (int) ($data['urutan'] ?? 0);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'fasilitas-asrama';
        $slug = $base;
        $suffix = 2;

        while (FasilitasAsrama::query()->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function deleteManagedImage(?string $path): void
    {
        if ($path !== null && Str::startsWith($path, 'asrama/fasilitas/') && ! Str::contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }
    }
}
