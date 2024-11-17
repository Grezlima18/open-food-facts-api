<?php

namespace App\Console\Commands;

use App\Models\ImportHistory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Http;
use Illuminate\Console\Command;
use PharData;
use ZipArchive;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar produtos do Open Food Facts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $indexUrl = 'https://challenges.coode.sh/food/data/json/index.txt';

        $response = Http::get($indexUrl);
        if ($response->failed()) {
            $this->error('Falha ao obter a lista de arquivos.');
            return;
        }

        $files = explode("\n", $response->body());
        array_pop($files); 
        $this->info('Arquivos encontrados: ' . count($files));

        $tempDir = storage_path('app/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        foreach ($files as $file) {
            $fileUrl = "https://challenges.coode.sh/food/data/json/{$file}";

            
            $conteudoGz = file_get_contents($fileUrl);
            if ($conteudoGz === false) {
                $this->error("Falha ao baixar o arquivo: {$file}");
                ImportHistory::create([
                    'imported_at' => now(),
                    'products_imported' => 0,
                    'file_name' => $file,
                    'status' => 'Failed to download file',
                ]);
                continue;
            }

            $gzFilePath = $tempDir . "/" . $file;
            file_put_contents($gzFilePath, $conteudoGz);

            
            $outFilePath = str_replace('.gz', '', $gzFilePath);
            $buffer_size = 4096;
            $gzFile = gzopen($gzFilePath, 'rb');
            if (!$gzFile) {
                $this->error("Falha ao abrir o arquivo gzip: {$gzFilePath}");
                ImportHistory::create([
                    'imported_at' => now(),
                    'products_imported' => 0,
                    'file_name' => $file,
                    'status' => 'Failed to decompress file',
                ]);
                continue;
            }

            $outFile = fopen($outFilePath, 'wb');
            while (!gzeof($gzFile)) {
                fwrite($outFile, gzread($gzFile, $buffer_size));
            }
            fclose($outFile);
            gzclose($gzFile);

            $jsonFile = fopen($outFilePath, 'r');
            if (!$jsonFile) {
                $this->error("Erro ao abrir o arquivo JSON: {$outFilePath}");
                ImportHistory::create([
                    'imported_at' => now(),
                    'products_imported' => 0,
                    'file_name' => $file,
                    'status' => 'Failed to open JSON file',
                ]);
                continue;
            }

            $counter = 0; 

            while (($line = fgets($jsonFile)) !== false && $counter < 100) {
                $productData = json_decode($line, true);

                if (!$productData) {
                    $this->error("Erro ao decodificar uma linha JSON: {$line}");
                    continue;
                }

                try {
                    $code = isset($productData['code']) ? ltrim($productData['code'], '"') : null;

                    $serving_quantity = !empty($productData['serving_quantity']) ? (float) $productData['serving_quantity'] : null;
                    $nutriscore_score = !empty($productData['nutriscore_score']) ? (int) $productData['nutriscore_score'] : null;

                    Product::updateOrCreate(
                        ['code' => $code],
                        [
                            'status' => 'draft',
                            'imported_t' => now(),
                            'url' => $productData['url'] ?? null,
                            'creator' => $productData['creator'] ?? null,
                            'created_t' => $productData['created_t'] ?? null,
                            'last_modified_t' => $productData['last_modified_t'] ?? null,
                            'product_name' => $productData['product_name'] ?? null,
                            'quantity' => $productData['quantity'] ?? null,
                            'brands' => $productData['brands'] ?? null,
                            'categories' => $productData['categories'] ?? null,
                            'labels' => $productData['labels'] ?? null,
                            'cities' => $productData['cities'] ?? null,
                            'purchase_places' => $productData['purchase_places'] ?? null,
                            'stores' => $productData['stores'] ?? null,
                            'ingredients_text' => $productData['ingredients_text'] ?? null,
                            'traces' => $productData['traces'] ?? null,
                            'serving_size' => $productData['serving_size'] ?? null,
                            'serving_quantity' => $serving_quantity,
                            'nutriscore_score' => $nutriscore_score,
                            'nutriscore_grade' => $productData['nutriscore_grade'] ?? null,
                            'main_category' => $productData['main_category'] ?? null,
                            'image_url' => $productData['image_url'] ?? null,
                        ]
                    );
                    $counter++;
                } catch (\Exception $e) {
                    $this->error("Erro ao importar o produto com código: {$productData['code']}");
                }
            }

            fclose($jsonFile);

            
            ImportHistory::create([
                'imported_at' => now(),
                'products_imported' => $counter,
                'file_name' => $file,
                'status' => 'success',
            ]);

            
            unlink($gzFilePath);
            unlink($outFilePath);
        }

        $this->info('Importação concluída com sucesso!');
    }
}
