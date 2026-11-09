<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function ktp(Reservasi $reservasi): StreamedResponse
    {
        $user = request()->user();
        abort_unless($user && ($user->role === 'admin_asrama' || (int) $reservasi->user_id === (int) $user->id), 403);

        return $this->servePrivateDocument($reservasi->foto_ktp_path, 'dokumen-ktp', 'dokumen-ktp-' . $reservasi->id);
    }

    public function paymentProof(Pembayaran $pembayaran): StreamedResponse
    {
        $user = request()->user();
        abort_unless($user && ($user->role === 'admin_keuangan' || (int) $pembayaran->user_id === (int) $user->id), 403);

        return $this->servePrivateDocument($pembayaran->bukti_pembayaran, 'bukti-pembayaran', 'bukti-pembayaran-' . $pembayaran->id);
    }

    private function servePrivateDocument(?string $path, string $directory, string $filenamePrefix): StreamedResponse
    {
        abort_unless($this->isSafeRelativePath($path, $directory), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404, 'Dokumen belum tersedia pada private storage.');

        $mime = $disk->mimeType($path);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];
        abort_unless(is_string($mime) && isset($extensions[$mime]), 415, 'Format dokumen tidak dapat ditampilkan.');

        return $disk->response($path, $filenamePrefix . '.' . $extensions[$mime], [
            'Cache-Control' => 'private, no-store',
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }

    private function isSafeRelativePath(?string $path, string $directory): bool
    {
        return is_string($path)
            && $path !== ''
            && str_starts_with($path, $directory . '/')
            && !str_contains($path, '..')
            && !str_starts_with($path, '/')
            && !str_contains($path, '\\')
            && !preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $path)
            && preg_match('/^[A-Za-z0-9_\/.\\-]+$/', $path) === 1;
    }
}
