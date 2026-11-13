<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ImportAssetsFromCsv
{
    private const MAX_ROWS = 5000;

    public function __construct(private RegisterAsset $registerAsset) {}

    public function handle(UploadedFile $file, int $userId): int
    {
        $stream = fopen($file->getPathname(), 'rb');

        if ($stream === false) {
            $this->fail(['The CSV file could not be read.']);
        }

        $rows = [];
        $errors = [];

        try {
            $headers = fgetcsv($stream, null, ',', '"', '');

            if ($headers === false) {
                $this->fail(['The CSV file must include a header row.']);
            }

            $headers[0] = ltrim((string) ($headers[0] ?? ''), "\xEF\xBB\xBF");
            $headerNames = array_map(
                static fn ($header): string => mb_strtolower(trim((string) $header)),
                $headers,
            );
            $expectedColumns = [
                'asset' => 'name',
                'asset code' => 'asset_code',
                'category' => 'category',
                'serial number' => 'serial_number',
                'location' => 'location',
                'purchase date' => 'purchase_date',
                'purchase cost' => 'purchase_cost',
                'warranty expiry' => 'warranty_expiry_date',
                'condition' => 'condition',
                'status' => 'status',
            ];

            if (
                count($headers) !== count($expectedColumns)
                || array_diff(array_keys($expectedColumns), $headerNames) !== []
            ) {
                $this->fail([
                    'CSV headers must be Asset, Asset code, Category, Serial number, Location, Purchase Date, Purchase Cost, Warranty expiry, Condition, and Status.',
                ]);
            }

            $columnIndexes = [];

            foreach ($expectedColumns as $header => $field) {
                $columnIndexes[$field] = array_search($header, $headerNames, true);
            }

            $lineNumber = 1;

            while (($csvRow = fgetcsv($stream, null, ',', '"', '')) !== false) {
                $lineNumber++;

                if (array_filter($csvRow, static fn ($value): bool => trim((string) $value) !== '') === []) {
                    continue;
                }

                if (count($csvRow) !== count($headers)) {
                    $errors[] = "Row {$lineNumber} must contain exactly ten columns.";

                    continue;
                }

                if (count($rows) === self::MAX_ROWS) {
                    $errors[] = 'CSV files may contain no more than 5,000 asset rows.';

                    break;
                }

                $data = [];

                foreach ($columnIndexes as $field => $columnIndex) {
                    $value = trim((string) ($csvRow[$columnIndex] ?? ''));

                    if ($field === 'condition' && $value !== '') {
                        $value = mb_strtolower($value);
                    }

                    $data[$field] = $value === '' ? null : $value;
                }

                $rows[] = ['line' => $lineNumber, 'data' => $data];
            }
        } finally {
            fclose($stream);
        }

        if ($errors !== []) {
            $this->fail($errors);
        }

        if ($rows === []) {
            $this->fail(['The CSV file must contain at least one asset row.']);
        }

        foreach ($rows as $row) {
            $validator = Validator::make($row['data'], [
                'name' => ['required', 'string', 'max:255'],
                'asset_code' => ['required', 'string', 'max:255'],
                'category' => ['required', 'string', 'max:255'],
                'serial_number' => ['nullable', 'string', 'max:255'],
                'location' => ['nullable', 'string', 'max:255'],
                'purchase_date' => ['nullable', 'date'],
                'purchase_cost' => ['nullable', 'numeric', 'min:0'],
                'warranty_expiry_date' => ['nullable', 'date'],
                'condition' => ['required', Rule::in(['good', 'fair', 'poor'])],
                'status' => ['required', 'string', 'max:255'],
            ], [], [
                'name' => 'Asset',
                'asset_code' => 'Asset code',
                'category' => 'Category',
                'serial_number' => 'Serial number',
                'location' => 'Location',
                'purchase_date' => 'Purchase date',
                'purchase_cost' => 'Purchase cost',
                'warranty_expiry_date' => 'Warranty expiry',
                'condition' => 'Condition',
                'status' => 'Status',
            ]);

            foreach ($validator->errors()->all() as $message) {
                $errors[] = "Row {$row['line']}: {$message}";
            }
        }

        if ($errors !== []) {
            $this->fail($errors);
        }

        $categoriesByName = Category::query()
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->keyBy(static fn (Category $category): string => mb_strtolower($category->name));
        $locationsByName = Location::query()
            ->where('is_active', true)
            ->get(['id', 'name'])
            ->groupBy(static fn (Location $location): string => mb_strtolower($location->name));
        $seenAssetCodes = [];
        $seenSerialNumbers = [];
        $assetCodes = [];
        $serialNumbers = [];

        foreach ($rows as &$row) {
            $data = $row['data'];
            $data['status'] = preg_replace('/[\s-]+/u', '_', mb_strtolower($data['status'])) ?? $data['status'];

            if (! in_array($data['status'], ['available', 'under_maintenance', 'damaged', 'disposed'], true)) {
                $errors[] = $data['status'] === 'assigned'
                    ? "Row {$row['line']}: Assigned assets must be registered through the assignment workflow."
                    : "Row {$row['line']}: Status must be Available, Under maintenance, Damaged, or Disposed.";
            }

            $category = $categoriesByName->get(mb_strtolower($data['category']));

            if ($category === null) {
                $errors[] = "Row {$row['line']}: Category '{$data['category']}' was not found or is inactive.";
            } else {
                $row['category_id'] = $category->id;
            }

            $locationId = null;

            if ($data['location'] !== null) {
                $locations = $locationsByName->get(mb_strtolower($data['location']));

                if ($locations === null || $locations->count() !== 1) {
                    $errors[] = "Row {$row['line']}: Location '{$data['location']}' was not found, is inactive, or is ambiguous.";
                } else {
                    $locationId = $locations->first()->id;
                }
            }

            $row['location_id'] = $locationId;
            $data['purchase_date'] = $data['purchase_date'] === null
                ? null
                : Carbon::parse($data['purchase_date'])->toDateString();
            $data['warranty_expiry_date'] = $data['warranty_expiry_date'] === null
                ? null
                : Carbon::parse($data['warranty_expiry_date'])->toDateString();

            $assetCode = $data['asset_code'];
            $assetCodes[] = $assetCode;

            if (isset($seenAssetCodes[$assetCode])) {
                $errors[] = "Row {$row['line']}: Asset code '{$assetCode}' appears more than once in the CSV.";
            }

            $seenAssetCodes[$assetCode] = true;

            if ($data['serial_number'] !== null) {
                $serialNumber = $data['serial_number'];
                $serialNumbers[] = $serialNumber;

                if (isset($seenSerialNumbers[$serialNumber])) {
                    $errors[] = "Row {$row['line']}: Serial number '{$serialNumber}' appears more than once in the CSV.";
                }

                $seenSerialNumbers[$serialNumber] = true;
            }

            $row['data'] = $data;
        }
        unset($row);

        $existingAssetCodes = array_fill_keys(
            Asset::query()->whereIn('asset_code', $assetCodes)->pluck('asset_code')->all(),
            true,
        );
        $existingSerialNumbers = array_fill_keys(
            Asset::query()->whereIn('serial_number', $serialNumbers)->pluck('serial_number')->all(),
            true,
        );

        foreach ($rows as $row) {
            $assetCode = $row['data']['asset_code'];

            if (isset($existingAssetCodes[$assetCode])) {
                $errors[] = "Row {$row['line']}: Asset code '{$assetCode}' already exists.";
            }

            $serialNumber = $row['data']['serial_number'];

            if ($serialNumber !== null && isset($existingSerialNumbers[$serialNumber])) {
                $errors[] = "Row {$row['line']}: Serial number '{$serialNumber}' already exists.";
            }
        }

        if ($errors !== []) {
            $this->fail($errors);
        }

        DB::transaction(function () use ($rows, $userId): void {
            foreach ($rows as $row) {
                $this->registerAsset->handle([
                    'asset_code' => $row['data']['asset_code'],
                    'name' => $row['data']['name'],
                    'category_id' => $row['category_id'],
                    'location_id' => $row['location_id'],
                    'serial_number' => $row['data']['serial_number'],
                    'purchase_date' => $row['data']['purchase_date'],
                    'purchase_cost' => $row['data']['purchase_cost'],
                    'warranty_expiry_date' => $row['data']['warranty_expiry_date'],
                    'condition' => $row['data']['condition'],
                    'status' => $row['data']['status'],
                ], $userId);
            }
        });

        return count($rows);
    }

    /**
     * @param  list<string>  $messages
     */
    private function fail(array $messages): never
    {
        if (count($messages) > 50) {
            $messages = [...array_slice($messages, 0, 50), 'Only the first 50 CSV errors are shown.'];
        }

        throw ValidationException::withMessages([
            'csv_file' => $messages,
        ])->errorBag('import');
    }
}
