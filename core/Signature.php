<?php
declare(strict_types=1);

namespace Core;

class Signature
{
    public static function saveFromCanvas(string $base64Data, string $directory): string
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $data = explode(',', $base64Data);
        $imageData = base64_decode($data[1] ?? $data[0]);
        $filename = 'sig_' . time() . '_' . bin2hex(random_bytes(8)) . '.png';
        $path = $directory . '/' . $filename;

        file_put_contents($path, $imageData);
        return $path;
    }

    public static function saveSignatureRecord(int $contractId, string $imagePath, string $signedBy, string $ip): int
    {
        return Database::insert('contract_signatures', [
            'contract_id' => $contractId,
            'image_path' => $imagePath,
            'signed_by' => $signedBy,
            'ip_address' => $ip,
            'signed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function getSignaturePath(int $contractId): ?string
    {
        $sig = Database::fetch(
            "SELECT image_path FROM contract_signatures WHERE contract_id = ? ORDER BY signed_at DESC LIMIT 1",
            [$contractId]
        );
        return $sig['image_path'] ?? null;
    }

    public static function hasSignature(int $contractId): bool
    {
        return (bool) Database::count('contract_signatures', 'contract_id = ?', [$contractId]);
    }
}
