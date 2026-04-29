<?php
// Use this to test if your controller can actually "see" the classes
require_once __DIR__ . '/../models/entities/Product.php';
require_once __DIR__ . '/../models/entities/Category.php';
require_once __DIR__ . '/../models/daos/ProductDao.php';

echo class_exists('Product') ? "✅ Product Entity Loaded<br>" : "❌ Product Missing<br>";
echo class_exists('Category') ? "✅ Category Entity Loaded<br>" : "❌ Category Missing<br>";
echo class_exists('ProductDao') ? "✅ ProductDao Loaded<br>" : "❌ ProductDao Missing<br>";