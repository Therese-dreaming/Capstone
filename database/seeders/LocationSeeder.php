<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('BUILDING, FLOOR, ROOMS.csv');
        if (!file_exists($path)) {
            if (isset($this->command)) {
                $this->command->warn("CSV file not found: {$path}");
            }
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            if (isset($this->command)) {
                $this->command->warn('Unable to open the CSV file.');
            }
            return;
        }

        $row = 0;
        while (($data = fgetcsv($handle)) !== false) {
            // Skip header
            if ($row === 0) {
                $row++;
                continue;
            }

            // Expecting 3 columns: Building, Floor, Room/Office
            if (count($data) < 3) {
                $row++;
                continue;
            }

            [$building, $floor, $room] = $data;

            $building = trim((string) $building);
            $floor    = trim((string) $floor);
            $room     = trim((string) $room);

            if ($building === '' || $floor === '' || $room === '') {
                $row++;
                continue;
            }

            // Normalize unicode dashes and quotes that often appear in CSVs
            $building = self::normalizeText($building);
            $floor    = self::normalizeText($floor);
            $room     = self::normalizeText($room);

            // Remove any existing aggregated entry (original unexpanded string) for idempotent replacement
            Location::where('building', $building)
                ->where('floor', $floor)
                ->where('room_number', $room)
                ->delete();

            // Expand ranges and comma-separated room tokens
            foreach (self::expandRooms($room) as $expandedRoom) {
                Location::firstOrCreate([
                    'building'    => $building,
                    'floor'       => $floor,
                    'room_number' => $expandedRoom,
                ]);
            }

            $row++;
        }

        fclose($handle);

        $processed = $row - 1; // exclude header
        if (isset($this->command)) {
            $this->command->info("Seeded locations from CSV ({$processed} rows processed, header excluded).");
        }
    }

    protected static function normalizeText(string $value): string
    {
        // Convert fancy quotes and dashes to simple ASCII
        $replacements = [
            "\xE2\x80\x93" => '-', // en dash
            "\xE2\x80\x94" => '-', // em dash
            "\xE2\x80\x98" => "'", // left single quote
            "\xE2\x80\x99" => "'", // right single quote
            "\xE2\x80\x9C" => '"', // left double quote
            "\xE2\x80\x9D" => '"', // right double quote
            "\xC2\xA0"      => ' ', // non-breaking space
        ];
        $value = strtr($value, $replacements);
        // Collapse repeated whitespace
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        return trim($value);
    }

    /**
     * Expand room labels handling ranges (e.g., 401-409, SD3-SD5) and comma-separated lists.
     * Returns an array of room labels to insert.
     */
    protected static function expandRooms(string $room): array
    {
        $room = trim($room);
        if ($room === '') return [];

        // Extract base label once from the full room string
        $base = self::extractBaseLabel($room);

        // Split by comma first (e.g., "65-69, 74")
        $parts = array_map('trim', explode(',', $room));

        $results = [];

        foreach ($parts as $part) {
            if ($part === '') continue;

            // Strip the base label from the part to avoid duplication
            // e.g., "Computer Labs 401-409" with base "Computer Lab" -> strip "Computer Labs" -> "401-409"
            if ($base !== '') {
                // Try to remove plural form first, then singular
                $pluralBase = self::pluralize($base);
                if (stripos($part, $pluralBase) === 0) {
                    $part = trim(substr($part, strlen($pluralBase)));
                } elseif (stripos($part, $base) === 0) {
                    $part = trim(substr($part, strlen($base)));
                }
            }

            // Normalize Unicode dashes already handled by normalizeText before
            // Handle alphanumeric or numeric range with possible spaced prefixes: e.g.,
            // "401-409", "SD3-SD5" (after base stripped)
            if (preg_match('/^([^\d]*)\s*(\d+)\s*-\s*([^\d]*)\s*(\d+)$/', $part, $m)) {
                $leftPrefix  = trim($m[1] ?? '');
                $startNum    = (int)($m[2] ?? 0);
                $rightPrefix = trim($m[3] ?? '');
                $endNum      = (int)($m[4] ?? 0);

                // If one side lacks prefix, use the other
                $alphaPrefix = $leftPrefix !== '' ? $leftPrefix : $rightPrefix;
                if ($startNum > $endNum) {
                    [$startNum, $endNum] = [$endNum, $startNum];
                }

                for ($n = $startNum; $n <= $endNum; $n++) {
                    $label = trim(($base !== '' ? ($base . ' ') : '') . ($alphaPrefix !== '' ? $alphaPrefix : '') . $n);
                    if ($label !== '') $results[] = $label;
                }
                continue;
            }

            // Single alphanumeric like SD3 or single number like 74 with possible spaced prefixes
            if (preg_match('/^([^\d]*)\s*(\d+)$/', $part, $m)) {
                $alpha = trim($m[1] ?? '');
                $num   = (int)($m[2] ?? 0);
                $label = trim(($base !== '' ? ($base . ' ') : '') . ($alpha !== '' ? $alpha : '') . $num);
                if ($label !== '') $results[] = $label;
                continue;
            }

            // Fallback: use part as is with base if appropriate
            $label = $base !== '' ? trim($base . ' ' . $part) : $part;
            if ($label !== '') $results[] = $label;
        }

        // Deduplicate while preserving order
        $results = array_values(array_unique($results));
        return $results;
    }

    /**
     * Simple pluralization helper for common cases.
     */
    protected static function pluralize(string $word): string
    {
        $word = trim($word);
        if ($word === '') return '';

        // Common patterns
        if (preg_match('/\b(Room|Lab|Library|Office|Area|Hall|Center)$/i', $word, $m)) {
            return $word . 's';
        }

        // Default: add 's'
        return $word . 's';
    }

    /**
     * Extract a base label from the full room string, e.g.,
     * "Computer Labs 401-409" -> "Computer Lab"
     * "HS Rooms 65-69, 74" -> "HS Room"
     * If no obvious base exists (e.g., "65-69, 74"), returns ''.
     */
    protected static function extractBaseLabel(string $full): string
    {
        $full = trim($full);
        if ($full === '') return '';

        // Position of first digit occurrence (start of numbers)
        if (preg_match('/\d/', $full, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1];
            $candidate = trim(substr($full, 0, $pos));
        } else {
            // No digits at all
            $candidate = '';
        }

        if ($candidate === '') return '';

        // Singularize common plurals: Rooms->Room, Labs->Lab, Libraries->Library
        $replacements = [
            '/\bRooms\b/i' => 'Room',
            '/\bLabs\b/i' => 'Lab',
            '/\bLibraries\b/i' => 'Library',
        ];
        $candidate = preg_replace(array_keys($replacements), array_values($replacements), $candidate) ?? $candidate;

        // Also trim a trailing 's' if it looks like a simple plural (best-effort)
        $candidate = preg_replace('/\b([A-Za-z]+)s\b/', '$1', $candidate) ?? $candidate;

        return trim($candidate);
    }
}
