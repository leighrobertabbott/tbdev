<?php
/**
 * Forum Structure Population Script
 * 
 * This script populates the database with forum sections, forums, and subforums
 * matching the reference design structure.
 * 
 * Usage: php scripts/populate_forums.php
 * Or visit: http://localhost:8000/scripts/populate_forums.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database;

// Load configuration
Config::load();

// Connect to database
try {
    $db = Database::getInstance();
    echo "✅ Connected to database\n\n";
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Forum structure data
$forumStructure = [
    [
        'section' => [
            'name' => 'GENERAL SECTION',
            'description' => 'General site announcements and information',
            'sort_order' => 1,
            'minclassread' => 0,
        ],
        'forums' => [
            [
                'name' => 'ANNOUNCEMENTS',
                'description' => 'ALL IMPORTANT ANNOUNCEMENTS WILL BE DROPPED IN HERE, ALSO INCLUDES THE SITE FAQ....',
                'sort' => 1,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
        ],
    ],
    [
        'section' => [
            'name' => 'OPEN DISCUSSION',
            'description' => 'Open discussion areas for various topics',
            'sort_order' => 2,
            'minclassread' => 0,
        ],
        'forums' => [
            [
                'name' => 'PADDOCK CLUB',
                'description' => 'GOT SOMETHING TO SAY? LET US KNOW ABOUT IT!',
                'sort' => 1,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
                'subforums' => [
                    [
                        'name' => 'FORMULA ONE',
                        'description' => 'THE PINNACLE OF MOTORSPORTS. SPOILERS, RESULTS AND NEWS ARTICLES...',
                        'sort' => 1,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'MOTOGP / SBK',
                        'description' => 'TWO-WHEELED DARE-DEVILS. SPOILERS, RESULTS AND NEWS ARTICLES.',
                        'sort' => 2,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'INDYCAR SERIES',
                        'description' => '300KM/H ON OVALS. SPOILERS, RESULTS AND NEWS ARTICLES..',
                        'sort' => 3,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'NASCAR SERIES',
                        'description' => 'STOCKCARS FTW! SPOILERS, RESULTS AND NEWS ARTICLES.',
                        'sort' => 4,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'OTHER RACE CLASSES',
                        'description' => 'ALL OTHER RACE CLASSES, ADD YOUR SPOILERS AND NEWS ARTICLES RIGHT HERE',
                        'sort' => 5,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                ],
            ],
            [
                'name' => 'GAMES & CONTESTS',
                'description' => 'RACING OR NON-RACING RELATED GAMES AND CONTESTS...',
                'sort' => 2,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
            [
                'name' => 'NEWS DISCUSSIONS',
                'description' => 'TO DISCUSS ALL OUR NEWS MESSAGES AND ANNOUNCEMENTS....',
                'sort' => 3,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
                'subforums' => [
                    [
                        'name' => 'TORRENT DISCUSSIONS',
                        'description' => 'DISCUSS TORRENTS, AND WHAT YOU HAVE THAT PEOPLE MIGHT WANT.',
                        'sort' => 1,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'SUPPORT SECTION (OTHER)',
                        'description' => 'FOR ALL OTHER ISSUES NOT RELATED TO THE SITE, TRACKER OR FORUMS...',
                        'sort' => 2,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'BROADCAST DISCUSSIONS',
                        'description' => 'IF YOU HAVE NEWS ABOUT A BROADCASTER..',
                        'sort' => 3,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                ],
            ],
            [
                'name' => 'INTERNATIONAL FORUM',
                'description' => 'DISCUSS THINGS IN YOUR OWN LANGUAGE IN HERE, BUT STAY CIVILIZED.',
                'sort' => 4,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
        ],
    ],
    [
        'section' => [
            'name' => 'TECHNICAL SECTION',
            'description' => 'Technical discussions and support',
            'sort_order' => 3,
            'minclassread' => 0,
        ],
        'forums' => [
            [
                'name' => 'CAPTURE SOFTWARE',
                'description' => 'DISCUSS CAPTURE SOFTWARE, THE PRO\'S AND THE CON\'S...',
                'sort' => 1,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
                'subforums' => [
                    [
                        'name' => 'CAPTURE CARDS',
                        'description' => 'FOR ADVISE, SUGGESTIONS AND TIPS REGARDING CAPTURE CARDS...',
                        'sort' => 1,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'TECHNICAL INFORMATION',
                        'description' => 'LINKS AND OTHER HELPFUL INFORMATION ABOUT CAPTURING...',
                        'sort' => 2,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                    [
                        'name' => 'CAPTURING / ENCODING',
                        'description' => 'FOR ALL YOUR QUESTIONS REGARDING CAPTURING AND ENCODING...',
                        'sort' => 3,
                        'minclassread' => 0,
                        'minclasswrite' => 0,
                        'minclasscreate' => 0,
                    ],
                ],
            ],
        ],
    ],
    [
        'section' => [
            'name' => 'TUTORIALS (HOW TO\'S)',
            'description' => 'Tutorials and how-to guides',
            'sort_order' => 4,
            'minclassread' => 0,
        ],
        'forums' => [
            [
                'name' => 'CLIENTS',
                'description' => 'ALL KINDS OF TUTORIALS FOR DIFFERENT BT CLIENTS...',
                'sort' => 1,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
            [
                'name' => 'CAPTURING / ENCODING',
                'description' => 'CAPTURE TUTORIALS CAN BE FOUND HERE..',
                'sort' => 2,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
            [
                'name' => 'OTHER TUTORIALS',
                'description' => 'ALL NON-CLIENT AND CAPTURE / ENCODING TUTORIALS ARE LOCATED HERE..',
                'sort' => 3,
                'minclassread' => 0,
                'minclasswrite' => 0,
                'minclasscreate' => 0,
            ],
        ],
    ],
];

// Function to create or get section
function getOrCreateSection($db, $sectionData) {
    // Check if section exists
    $stmt = $db->prepare("SELECT id FROM forum_sections WHERE name = :name");
    $stmt->execute(['name' => $sectionData['name']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        echo "  📁 Section '{$sectionData['name']}' already exists (ID: {$existing['id']})\n";
        return (int)$existing['id'];
    }
    
    // Create section
    $stmt = $db->prepare("
        INSERT INTO forum_sections (name, description, sort_order, minclassread)
        VALUES (:name, :description, :sort_order, :minclassread)
    ");
    $stmt->execute($sectionData);
    $sectionId = (int)$db->lastInsertId();
    
    echo "  ✅ Created section '{$sectionData['name']}' (ID: {$sectionId})\n";
    return $sectionId;
}

// Function to create or get forum
function getOrCreateForum($db, $forumData, $sectionId, $parentId = null) {
    // Check if forum exists
    if ($parentId === null) {
        $stmt = $db->prepare("
            SELECT id FROM forums 
            WHERE name = :name 
            AND section_id = :section_id
            AND (parent_id IS NULL OR parent_id = 0)
        ");
        $stmt->execute([
            'name' => $forumData['name'],
            'section_id' => $sectionId,
        ]);
    } else {
        $stmt = $db->prepare("
            SELECT id FROM forums 
            WHERE name = :name 
            AND section_id = :section_id
            AND parent_id = :parent_id
        ");
        $stmt->execute([
            'name' => $forumData['name'],
            'section_id' => $sectionId,
            'parent_id' => $parentId,
        ]);
    }
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        $indent = $parentId ? '    ' : '  ';
        echo "{$indent}📂 Forum '{$forumData['name']}' already exists (ID: {$existing['id']})\n";
        return (int)$existing['id'];
    }
    
    // Create forum
    $stmt = $db->prepare("
        INSERT INTO forums (
            name, description, sort, section_id, parent_id,
            minclassread, minclasswrite, minclasscreate,
            postcount, topiccount
        )
        VALUES (
            :name, :description, :sort, :section_id, :parent_id,
            :minclassread, :minclasswrite, :minclasscreate,
            0, 0
        )
    ");
    $stmt->execute([
        'name' => $forumData['name'],
        'description' => $forumData['description'] ?? '',
        'sort' => $forumData['sort'] ?? 0,
        'section_id' => $sectionId,
        'parent_id' => $parentId,
        'minclassread' => $forumData['minclassread'] ?? 0,
        'minclasswrite' => $forumData['minclasswrite'] ?? 0,
        'minclasscreate' => $forumData['minclasscreate'] ?? 0,
    ]);
    $forumId = (int)$db->lastInsertId();
    
    $indent = $parentId ? '    ' : '  ';
    echo "{$indent}✅ Created forum '{$forumData['name']}' (ID: {$forumId})\n";
    return $forumId;
}

// Start processing
echo "🚀 Starting forum structure population...\n\n";

$db->beginTransaction();

try {
    foreach ($forumStructure as $sectionGroup) {
        $sectionId = getOrCreateSection($db, $sectionGroup['section']);
        
        foreach ($sectionGroup['forums'] as $forumData) {
            $subforums = $forumData['subforums'] ?? [];
            unset($forumData['subforums']);
            
            $forumId = getOrCreateForum($db, $forumData, $sectionId);
            
            // Create subforums
            if (!empty($subforums)) {
                foreach ($subforums as $subforumData) {
                    getOrCreateForum($db, $subforumData, $sectionId, $forumId);
                }
            }
        }
        
        echo "\n";
    }
    
    $db->commit();
    echo "✅ Forum structure populated successfully!\n";
    echo "\n";
    echo "📊 Summary:\n";
    
    // Count sections
    $stmt = $db->query("SELECT COUNT(*) as count FROM forum_sections");
    $sectionCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count forums (no parent)
    $stmt = $db->query("SELECT COUNT(*) as count FROM forums WHERE parent_id IS NULL OR parent_id = 0");
    $forumCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count subforums
    $stmt = $db->query("SELECT COUNT(*) as count FROM forums WHERE parent_id IS NOT NULL AND parent_id != 0");
    $subforumCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "  - Sections: {$sectionCount}\n";
    echo "  - Forums: {$forumCount}\n";
    echo "  - Subforums: {$subforumCount}\n";
    echo "\n";
    echo "🌐 Visit http://localhost:8000/forums to see the forums!\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

