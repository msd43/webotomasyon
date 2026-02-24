<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class ProductGroup
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
        $stmt = $this->db->prepare('SELECT * FROM product_groups ORDER BY id DESC');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return is_array($rows) ? $rows : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getActive(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_groups WHERE status = :status ORDER BY name ASC');
        $stmt->execute([':status' => 1]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return is_array($rows) ? $rows : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_groups WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }

    /** @param array<string,mixed> $data */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO product_groups (name, slug, description, status, created_at, updated_at)
             VALUES (:name, :slug, :description, :status, NOW(), NOW())'
        );

        $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':status' => (int) ($data['status'] ?? 1),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array<string,mixed> $data */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE product_groups
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 status = :status,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':status' => (int) ($data['status'] ?? 1),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM product_groups WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
