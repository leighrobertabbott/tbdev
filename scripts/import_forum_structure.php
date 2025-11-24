<?php
/**
 * Import Forum Structure SQL Script
 * 
 * This script imports the forum_structure.sql file to create the necessary
 * tables and columns for the forum hierarchy system.
 * 
 * Usage: php scripts/import_forum_structure.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use PDO;
use PDOException;

// Load configuration
Config::load();

// Get database config
$dbConfig = Config::get('db');

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['name']
    );
    
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✅ Connected to database: {$dbConfig['name']}\n\n";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = __DIR__ . '/../SQL/forum_structure.sql';
if (!file_exists($sqlFile)) {
    die("❌ SQL file not found: {$sqlFile}\n");
}

echo "📖 Reading SQL file: {$sqlFile}\n";
$sql = file_get_contents($sqlFile);

// Remove comments
$sql = preg_replace('/--.*$/m', '', $sql);

// Split by semicolon
$statements = array_filter(array_map('trim', explode(';', $sql)));

echo "📝 Found " . count($statements) . " SQL statements\n\n";

$pdo->beginTransaction();

try {
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }
        
        // Handle CREATE TABLE IF NOT EXISTS
        if (preg_match('/CREATE TABLE IF NOT EXISTS/i', $statement)) {
            echo "  Creating table (if not exists)...\n";
            try {
                $pdo->exec($statement);
                echo "  ✅ Table created or already exists\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "  ℹ️  Table already exists (skipped)\n";
                } else {
                    throw $e;
                }
            }
            continue;
        }
        
        // Handle ALTER TABLE with IF NOT EXISTS
        if (preg_match('/ALTER TABLE.*ADD COLUMN IF NOT EXISTS/i', $statement)) {
            // Extract table and column info
            if (preg_match('/ALTER TABLE\s+`?(\w+)`?\s+ADD COLUMN IF NOT EXISTS\s+`?(\w+)`?/i', $statement, $matches)) {
                $tableName = $matches[1];
                $columnName = $matches[2];
                
                // Check if column exists
                $checkSql = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = :table_name 
                            AND COLUMN_NAME = :column_name";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([
                    'table_name' => $tableName,
                    'column_name' => $columnName,
                ]);
                $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] == 0) {
                    // Column doesn't exist, add it
                    $alterStatement = preg_replace('/IF NOT EXISTS\s+/i', '', $statement);
                    echo "  Adding column `{$columnName}` to table `{$tableName}`...\n";
                    $pdo->exec($alterStatement);
                    echo "  ✅ Column added\n";
                } else {
                    echo "  ℹ️  Column `{$columnName}` already exists in `{$tableName}` (skipped)\n";
                }
                continue;
            }
        }
        
        // Handle ALTER TABLE with ADD KEY IF NOT EXISTS
        if (preg_match('/ALTER TABLE.*ADD KEY IF NOT EXISTS/i', $statement)) {
            // Extract table and index info
            if (preg_match('/ALTER TABLE\s+`?(\w+)`?\s+ADD KEY IF NOT EXISTS\s+`?(\w+)`?/i', $statement, $matches)) {
                $tableName = $matches[1];
                $indexName = $matches[2];
                
                // Check if index exists
                $checkSql = "SELECT COUNT(*) as count FROM information_schema.STATISTICS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = :table_name 
                            AND INDEX_NAME = :index_name";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute([
                    'table_name' => $tableName,
                    'index_name' => $indexName,
                ]);
                $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] == 0) {
                    // Index doesn't exist, add it
                    $alterStatement = preg_replace('/IF NOT EXISTS\s+/i', '', $statement);
                    echo "  Adding index `{$indexName}` to table `{$tableName}`...\n";
                    $pdo->exec($alterStatement);
                    echo "  ✅ Index added\n";
                } else {
                    echo "  ℹ️  Index `{$indexName}` already exists on `{$tableName}` (skipped)\n";
                }
                continue;
            }
        }
        
        // Execute other statements
        if (!empty($statement)) {
            echo "  Executing statement " . ($index + 1) . "...\n";
            try {
                $pdo->exec($statement);
                echo "  ✅ Statement executed\n";
            } catch (PDOException $e) {
                // Ignore "already exists" errors
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate column name') === false &&
                    strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
                echo "  ℹ️  " . $e->getMessage() . " (skipped)\n";
            }
        }
    }
    
    // DDL statements auto-commit, so we don't need to commit
    // But we'll try to commit anyway (it's safe)
    try {
        $pdo->commit();
    } catch (PDOException $e) {
        // Transaction already committed (DDL auto-commits), ignore
    }
    
    echo "\n✅ Forum structure imported successfully!\n\n";
    
    // Verify tables exist
    echo "🔍 Verifying tables...\n";
    
    $tables = ['forum_sections', 'forums'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() > 0) {
            echo "  ✅ Table `{$table}` exists\n";
            
            // Check columns for forums table
            if ($table === 'forums') {
                $stmt = $pdo->query("SHOW COLUMNS FROM forums LIKE 'section_id'");
                if ($stmt->rowCount() > 0) {
                    echo "    ✅ Column `section_id` exists\n";
                } else {
                    echo "    ⚠️  Column `section_id` missing\n";
                }
                
                $stmt = $pdo->query("SHOW COLUMNS FROM forums LIKE 'parent_id'");
                if ($stmt->rowCount() > 0) {
                    echo "    ✅ Column `parent_id` exists\n";
                } else {
                    echo "    ⚠️  Column `parent_id` missing\n";
                }
            }
        } else {
            echo "  ❌ Table `{$table}` does not exist\n";
        }
    }
    
    echo "\n🎉 Done! You can now run: php scripts/populate_forums.php\n";
    
} catch (PDOException $e) {
    try {
        $pdo->rollBack();
    } catch (PDOException $rollbackError) {
        // Transaction may have already been committed (DDL auto-commits)
    }
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

