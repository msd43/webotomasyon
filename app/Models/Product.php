<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Product
{
    /** @var PDO */
    private $db;

    public function __construct()
    {
        /** @var array{database: array<string, mixed>} $config */
        $config = require dirname(__DIR__, 2) . '/config.php';
        $this->db = Database::getInstance($config['database'])->getConnection();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $sql = 'SELECT p.*, pg.name AS group_name
                FROM products p
                INNER JOIN product_groups pg ON pg.id = p.group_id
                ORDER BY p.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return is_array($rows) ? $rows : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }

    /** @param array<string,mixed> $data */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO products (
                    group_id, name, slug, description, type, module, price, setup_fee, billing_cycle, status, created_at, updated_at
                ) VALUES (
                    :group_id, :name, :slug, :description, :type, :module, :price, :setup_fee, :billing_cycle, :status, NOW(), NOW()
                )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':group_id' => (int) $data['group_id'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':type' => $data['type'],
            ':module' => $data['module'] ?? null,
            ':price' => (float) $data['price'],
            ':setup_fee' => (float) $data['setup_fee'],
            ':billing_cycle' => $data['billing_cycle'],
            ':status' => (int) ($data['status'] ?? 1),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string,mixed> $data */
    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE products
                SET group_id = :group_id,
                    name = :name,
                    slug = :slug,
                    description = :description,
                    type = :type,
                    module = :module,
                    price = :price,
                    setup_fee = :setup_fee,
                    billing_cycle = :billing_cycle,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':group_id' => (int) $data['group_id'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':type' => $data['type'],
            ':module' => $data['module'] ?? null,
            ':price' => (float) $data['price'],
            ':setup_fee' => (float) $data['setup_fee'],
            ':billing_cycle' => $data['billing_cycle'],
            ':status' => (int) ($data['status'] ?? 1),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
