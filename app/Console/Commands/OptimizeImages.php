<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Models\ProductImage; // Импортируйте модель изображения продукта

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Optimize all product images';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $optimizerChain = OptimizerChainFactory::create();
        $images = ProductImage::all(); // Предположим, что ваша модель изображений называется ProductImage

        foreach ($images as $image) {
            $imagePath = storage_path('app/public/' . $image->image_url);

            if (file_exists($imagePath)) {
                $this->info('Optimizing ' . $imagePath);
                $optimizerChain->optimize($imagePath);
                $this->info('Optimized ' . $imagePath);
            } else {
                $this->warn('File not found: ' . $imagePath);
            }
        }

        $this->info('All images have been optimized.');
    }
}
