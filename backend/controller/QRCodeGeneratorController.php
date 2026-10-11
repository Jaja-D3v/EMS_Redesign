<?php

require_once __DIR__ . '/../model/QRCodeGeneratorModel.php';


function getAllFireExtinguishersCode()
{
    $role = strtolower(trim($_SESSION['Role'] ?? ''));
    $sessionBranch = trim($_SESSION['Branch'] ?? '');

    // ADMIN
    if ($role === 'admin') {

        // If branch is selected from dropdown, use it
        if (
            isset($_GET['branch']) &&
            trim($_GET['branch']) !== ''
        ) {
            $selectedBranch = trim($_GET['branch']);
        } else {
            // Default = Admin's assigned branch
            $selectedBranch = $sessionBranch;
        }

        // If Admin has no assigned branch
        // fallback to all branches
        if ($selectedBranch === '') {
            $selectedBranch = 'all';
        }

        return getExtinguisher($selectedBranch);
    }


    // INSPECTOR
    if ($role === 'inspector') {

        if (empty($sessionBranch)) {
            return false;
        }

        return getExtinguisher($sessionBranch);
    }


    return false;
}


function getAllBranches()
{
    return getBranches();
}