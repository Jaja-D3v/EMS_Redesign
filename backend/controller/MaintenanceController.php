<?php

require_once __DIR__ . '/../model/MaintenanceModel.php';

class MaintenanceController
{
    private $model;

    public function __construct($conn)
    {
        $this->model = new MaintenanceModel($conn);
    }


    /*
    |--------------------------------------------------------------------------
    | GET DATA
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $categories = [
            'branch',
            'placement',
            'type',
            'fire_class',
            'capacity',
            'location'
        ];

        $data = [];

        foreach ($categories as $category) {

            $page = isset($_GET[$category . '_page']) ? max(1, (int) $_GET[$category . '_page']) : 1;
            $search = isset($_GET[$category . '_search']) ? trim($_GET[$category . '_search']) : '';
            $data[$category] = [
                'records' => $this->model->getData( $category, $page, 5, $search ),
                'total' => $this->model->getTotal( $category, $search ),
                'page' => $page,
                'total_pages' => ceil( $this->model->getTotal( $category, $search ) / 5 ),
                'search' => $search
            ];
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | ADD
    |--------------------------------------------------------------------------
    */

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $category = trim($_POST['category'] ?? '');
        $value = trim($_POST['value'] ?? '');

        if (
            empty($category) ||
            empty($value)
        ) {
            header("Location: maintenance.php?error=empty");
            exit;
        }

        $allowedCategories = [
            'branch',
            'placement',
            'type',
            'fire_class',
            'capacity',
            'location'
        ];

        if (!in_array($category, $allowedCategories)) {
            header("Location: maintenance.php?error=invalid_category");
            exit;
        }

        $result = $this->model->add(
            $category,
            $value
        );

        if ($result) {

            header("Location: maintenance.php?success=added");
            exit;

        }

        header("Location: maintenance.php?error=add_failed");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $value = trim($_POST['value'] ?? '');

        if (
            $id <= 0 ||
            empty($value)
        ) {
            header("Location: maintenance.php?error=invalid");
            exit;
        }

        $result = $this->model->update(
            $id,
            $value
        );

        if ($result) {

            header("Location: maintenance.php?success=updated");
            exit;

        }

        header("Location: maintenance.php?error=update_failed");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE / DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: maintenance.php?error=invalid");
            exit;
        }

        $result = $this->model->delete($id);

        if ($result) {

            header("Location: maintenance.php?success=deleted");
            exit;

        }

        header("Location: maintenance.php?error=delete_failed");
        exit;
    }
}