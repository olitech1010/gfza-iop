<?php

namespace App\Console\Commands;

use App\Models\StoreCategory;
use App\Models\StoreItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportStoreLedger extends Command
{
    protected $signature = 'store:import-ledger {--path= : Path to the HTML files directory}';

    protected $description = 'Import store items from HTML ledger files (balance-only import)';

    /**
     * Category patterns: keyword => category name.
     */
    private array $categoryPatterns = [
        // Toners & Cartridges
        'TONER' => 'Toners & Cartridges',
        'PHOTOCOPIER' => 'Toners & Cartridges',
        'LASERJET' => 'Toners & Cartridges',

        // IT Equipment
        'LAPTOP' => 'IT Equipment',
        'DESKTOP' => 'IT Equipment',
        'COMPUTER' => 'IT Equipment',
        'MONITOR' => 'IT Equipment',
        'MOUSE' => 'IT Equipment',
        'KEYBOARD' => 'IT Equipment',
        'UPS' => 'IT Equipment',
        'SCANNER' => 'IT Equipment',
        'HPSCANJET' => 'IT Equipment',
        'ETHERNET' => 'IT Equipment',
        'PEN DRIVE' => 'IT Equipment',
        'MEMORY' => 'IT Equipment',
        'CHARGER' => 'IT Equipment',
        'BATTERY BACKUP' => 'IT Equipment',

        // Beverages & Provisions
        'WATER' => 'Beverages & Provisions',
        'COFFEE' => 'Beverages & Provisions',
        'MILO' => 'Beverages & Provisions',
        'LIPTON' => 'Beverages & Provisions',
        'NESCAFE' => 'Beverages & Provisions',
        'SUGAR' => 'Beverages & Provisions',
        'MILK' => 'Beverages & Provisions',
        'COKE' => 'Beverages & Provisions',
        'FANTA' => 'Beverages & Provisions',
        'CERES' => 'Beverages & Provisions',
        'JUICE' => 'Beverages & Provisions',
        'BISCUIT' => 'Beverages & Provisions',
        'SOFT DRINK' => 'Beverages & Provisions',
        'PROVISION' => 'Beverages & Provisions',

        // Cleaning Supplies
        'TISSUE' => 'Cleaning & Hygiene',
        'TOILET' => 'Cleaning & Hygiene',
        'HAND SANITIZER' => 'Cleaning & Hygiene',
        'HAND PAPER' => 'Cleaning & Hygiene',
        'HARPIC' => 'Cleaning & Hygiene',
        'FABULOSO' => 'Cleaning & Hygiene',
        'DETTOL' => 'Cleaning & Hygiene',
        'DETOL' => 'Cleaning & Hygiene',
        'POWERZONE' => 'Cleaning & Hygiene',
        'ZOFLORA' => 'Cleaning & Hygiene',
        'GOJO' => 'Cleaning & Hygiene',
        'SOAP' => 'Cleaning & Hygiene',
        'MOSQUIT' => 'Cleaning & Hygiene',
        'AIR REFRESH' => 'Cleaning & Hygiene',
        'DIFFUSER' => 'Cleaning & Hygiene',
        'URINAL' => 'Cleaning & Hygiene',
        'WC BLOCK' => 'Cleaning & Hygiene',
        'NEPHTALENE' => 'Cleaning & Hygiene',
        'ASEVI' => 'Cleaning & Hygiene',
        'DUST BIN' => 'Cleaning & Hygiene',

        // GFZA Branded Items
        'GFZA' => 'GFZA Branded Items',

        // Stationery (catch-all for the rest - assigned last)
    ];

    public function handle(): int
    {
        $path = $this->option('path') ?: base_path('docs/STORES LEDGER');

        if (! File::isDirectory($path)) {
            $this->error("Directory not found: {$path}");
            return self::FAILURE;
        }

        $files = File::glob($path . '/*.html');
        $this->info("Found " . count($files) . " HTML files to process.");

        // Ensure categories exist
        $this->createCategories();

        $imported = 0;
        $skipped = 0;
        $errors = [];

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);

            // Skip non-item files
            if (in_array(trim($filename), ['Sheet2', 'STOCK LEVEL, 31-08-2021', 'STATIONERY'])) {
                $bar->advance();
                $skipped++;
                continue;
            }

            try {
                $result = $this->parseHtmlFile($file, $filename);

                if ($result) {
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors[] = "{$filename}: {$e->getMessage()}";
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Imported: {$imported} items");
        $this->info("⏭️  Skipped: {$skipped} items");

        if (! empty($errors)) {
            $this->warn("⚠️  Errors (" . count($errors) . "):");
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return self::SUCCESS;
    }

    private function createCategories(): void
    {
        $categories = [
            'Stationery',
            'Toners & Cartridges',
            'IT Equipment',
            'Beverages & Provisions',
            'Cleaning & Hygiene',
            'GFZA Branded Items',
        ];

        foreach ($categories as $name) {
            StoreCategory::firstOrCreate(['name' => $name]);
        }

        $this->info("Categories created/verified.");
    }

    private function parseHtmlFile(string $filePath, string $filename): ?StoreItem
    {
        $html = File::get($filePath);

        // Extract item name from the header (softmerge-inner div or the filename)
        $itemName = $this->extractItemName($html, $filename);

        // Extract the last balance from the table
        $lastBalance = $this->extractLastBalance($html);

        if ($itemName === null) {
            return null;
        }

        // Determine category
        $categoryName = $this->categorizeItem($itemName);
        $category = StoreCategory::where('name', $categoryName)->first();

        // Create StoreItem (skip if already exists)
        return StoreItem::firstOrCreate(
            ['name' => $itemName],
            [
                'store_category_id' => $category->id,
                'unit_of_measure' => 'Pieces',
                'current_stock' => $lastBalance ?? 0,
                'reorder_level' => 5,
                'unit_cost' => 0,
            ]
        );
    }

    private function extractItemName(string $html, string $filename): ?string
    {
        // Try to extract from softmerge-inner div (the header row in the HTML)
        if (preg_match('/class="softmerge-inner"[^>]*>([^<]+)</', $html, $matches)) {
            $name = trim($matches[1]);
            if (strlen($name) > 2 && $name !== 'BALANCES' && $name !== 'REQUISITION NO.') {
                return mb_convert_case($name, MB_CASE_TITLE);
            }
        }

        // Fallback to the filename
        $name = trim($filename);
        return mb_convert_case($name, MB_CASE_TITLE);
    }

    private function extractLastBalance(string $html): int
    {
        // Parse the HTML table and find the last non-empty value in the balance column (column J / index 9)
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($doc);

        // Get all table rows
        $rows = $xpath->query('//tr');
        $lastBalance = 0;

        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');

            if ($cells->length < 10) {
                continue;
            }

            // The balance is in the last cell (column J, index 9)
            $balanceCell = $cells->item(9);
            $text = trim($balanceCell->textContent ?? '');

            if ($text !== '' && is_numeric($text)) {
                $lastBalance = (int) $text;
            }
        }

        libxml_clear_errors();

        return $lastBalance;
    }

    private function categorizeItem(string $itemName): string
    {
        $upperName = strtoupper($itemName);

        foreach ($this->categoryPatterns as $keyword => $category) {
            if (str_contains($upperName, $keyword)) {
                return $category;
            }
        }

        // Default category
        return 'Stationery';
    }
}
