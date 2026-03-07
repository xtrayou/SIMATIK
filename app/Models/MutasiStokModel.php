<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class MutasiStokModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'reference_no',
        'notes',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get movements with product details
     */
    public function getMovementsWithProduct(int $limit = 10, array $filters = []): array
    {
        $builder = $this->select('stock_movements.*, products.name as product_name, products.sku as product_sku, products.unit')
            ->join('products', 'products.id = stock_movements.product_id');

        if (!empty($filters['product_id'])) {
            $builder->where('stock_movements.product_id', $filters['product_id']);
        }

        if (!empty($filters['type'])) {
            $builder->where('stock_movements.type', $filters['type']);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('DATE(stock_movements.created_at) >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('DATE(stock_movements.created_at) <=', $filters['end_date']);
        }

        $builder->orderBy('stock_movements.created_at', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get monthly movements summary for charts
     */
    public function getMonthlyMovements(): array
    {
        $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));

        return $this->select("
                MONTH(created_at) as month,
                type,
                SUM(quantity) as total_quantity
            ")
            ->where('created_at >=', $sixMonthsAgo)
            ->groupBy('MONTH(created_at), type')
            ->orderBy('month', 'ASC')
            ->findAll();
    }

    /**
     * Create a stock movement and update product stock
     */
    public function createMovement(array $data): int
    {
        $modelProduk = new ProdukModel();
        $product = $modelProduk->find($data['product_id']);

        if (!$product) {
            throw new Exception('Produk tidak ditemukan');
        }

        $previousStock = (int) $product['current_stock'];
        $quantity = (int) $data['quantity'];

        switch ($data['type']) {
            case 'IN':
                $newStock = $previousStock + $quantity;
                break;
            case 'OUT':
                if ($previousStock < $quantity) {
                    throw new Exception('Stok tidak mencukupi untuk ' . ($product['name'] ?? 'produk ini'));
                }
                $newStock = $previousStock - $quantity;
                break;
            case 'ADJUSTMENT':
                $newStock = $quantity; // In adjustment, quantity is the final stock level
                break;
            default:
                throw new Exception('Tipe mutasi tidak valid');
        }

        $data['previous_stock'] = $previousStock;

        $movementId = $this->insert($data);

        // Update product current stock
        $modelProduk->update($data['product_id'], ['current_stock' => $newStock]);

        return (int) $movementId;
    }

    /**
     * Generate automatic reference number
     */
    public function generateReferenceNo(string $type = 'IN'): string
    {
        $prefix = match ($type) {
            'IN'         => 'SM-IN',
            'OUT'        => 'SM-OUT',
            'ADJUSTMENT' => 'SM-ADJ',
            default      => 'SM',
        };

        $today = date('Ymd');
        $last = $this->like('reference_no', $prefix . '-' . $today, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $number = 1;
        if ($last) {
            $parts = explode('-', $last['reference_no']);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $number = (int) $lastPart + 1;
            }
        }

        return $prefix . '-' . $today . '-' . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
    }
}
