<?php
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getRole() {
    return $_SESSION['role'] ?? null;
}
?> 