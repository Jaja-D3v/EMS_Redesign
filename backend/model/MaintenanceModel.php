<?php
require_once __DIR__ . '/../config/db.php';

class MaintenanceModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }


    /*
    |--------------------------------------------------------------------------
    | GET PAGINATED DATA
    |--------------------------------------------------------------------------
    */

    public function getData($category, $page = 1, $limit = 5, $search = '')
    {
        $offset = ($page - 1) * $limit;

        $sql = "
            SELECT id, category, value, status
            FROM maintenance_dropdown_tbl
            WHERE category = ?
            AND status = 'active'
        ";

        $params = [$category];
        $types = "s";

        if (!empty($search)) {

            $sql .= " AND value LIKE ?";

            $params[] = "%" . $search . "%";
            $types .= "s";
        }

        $sql .= "
            ORDER BY value ASC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;

        $types .= "ii";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            $types,
            ...$params
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /*
    |--------------------------------------------------------------------------
    | GET TOTAL
    |--------------------------------------------------------------------------
    */

    public function getTotal($category, $search = '')
    {
        $sql = " SELECT COUNT(*) AS total
            FROM maintenance_dropdown_tbl
            WHERE category = ?
            AND status = 'active'
        ";

        $params = [$category];
        $types = "s";

        if (!empty($search)) {

            $sql .= " AND value LIKE ?";

            $params[] = "%" . $search . "%";
            $types .= "s";
        }

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            $types,
            ...$params
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc()['total'];
    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE RECORD
    |--------------------------------------------------------------------------
    */

    public function getById($id)
    {
        $sql = "
            SELECT *
            FROM maintenance_dropdown_tbl
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt
            ->get_result()
            ->fetch_assoc();
    }


    /*
    |--------------------------------------------------------------------------
    | ADD
    |--------------------------------------------------------------------------
    */

    public function add($category, $value)
    {
        $value = trim($value);

        $value = strtolower($value);

        $value = ucwords($value);

        $sql = "
            INSERT INTO maintenance_dropdown_tbl
            (category, value)
            VALUES (?, ?)
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ss",
            $category,
            $value
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update($id, $value)
    {
        $value = trim($value);

        $value = strtolower($value);
        
        $value = ucwords($value);

        $sql = "
        UPDATE maintenance_dropdown_tbl
        SET value = ?
        WHERE id = ?
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $value,
            $id
        );

        return $stmt->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $sql = "
            UPDATE maintenance_dropdown_tbl
            SET status = 'inactive'
            WHERE id = ?
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $id
        );

        return $stmt->execute();
    }
}
