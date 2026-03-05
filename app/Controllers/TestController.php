<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TestController extends BaseController
{
    public function testModels()
    {
        // Test CategoryModel
        $categoryModel = new \App\Models\CategoryModel();
        $categories = $categoryModel->findAll();
        echo "<h3>Categories:</h3>";
        foreach ($categories as $cat) {
            echo $cat['name'] . "<br>";
        }

        // Test ProductModel
        $productModel = new \App\Models\ProductModel();
        $products = $productModel->getProductsWithCategory();
        echo "<h3>Products with Categories:</h3>";
        foreach ($products as $product) {
            echo $product['name'] . " - " . $product['category_name'] . " (Stock: " . $product['current_stock'] . ")<br>";
        }

        // Test low stock
        $lowStock = $productModel->getLowStockProducts();
        echo "<h3>Low Stock Products:</h3>";
        foreach ($lowStock as $product) {
            echo $product['name'] . " - Stock: " . $product['current_stock'] . " (Min: " . $product['min_stock'] . ")<br>";
        }

        // Test SKU generation
        $newSKU = $productModel->generateSKU(1, 'MacBook Pro');
        echo "<h3>Generated SKU:</h3>" . $newSKU;

        return "Models test completed!";
    }
}
