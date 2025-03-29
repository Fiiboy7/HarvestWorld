<?php
// This file simulates a database connection and provides plant data
// In a real application, you would connect to a MySQL database

// Database connection settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // Change to your MySQL username
define('DB_PASS', '');         // Change to your MySQL password
define('DB_NAME', 'harvestworld');

// Create a database connection
function getDbConnection() {
    static $conn;
    
    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // For production, you should log errors instead of displaying them
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $conn;
}

// Get all plants data
function getPlantsData($filters = []) {
    $conn = getDbConnection();
    
    $sql = "SELECT p.*, c.name as category 
            FROM plants p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    $params = [];
    
    // Apply search filter
    if (!empty($filters['search'])) {
        $sql .= " AND (p.name LIKE :search OR p.scientific_name LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    // Apply category filter
    if (!empty($filters['category'])) {
        if (is_array($filters['category'])) {
            $placeholders = [];
            foreach ($filters['category'] as $i => $cat) {
                $key = ":category{$i}";
                $placeholders[] = $key;
                $params[$key] = $cat;
            }
            $sql .= " AND c.name IN (" . implode(', ', $placeholders) . ")";
        } else {
            $sql .= " AND c.name = :category";
            $params[':category'] = $filters['category'];
        }
    }
    
    // Apply category_id filter
    if (!empty($filters['category_id'])) {
        $sql .= " AND p.category_id = :category_id";
        $params[':category_id'] = $filters['category_id'];
    }
    
    // Apply difficulty filter
    if (!empty($filters['difficulty'])) {
        if (is_array($filters['difficulty'])) {
            $placeholders = [];
            foreach ($filters['difficulty'] as $i => $diff) {
                $key = ":difficulty{$i}";
                $placeholders[] = $key;
                $params[$key] = $diff;
            }
            $sql .= " AND p.difficulty IN (" . implode(', ', $placeholders) . ")";
        } else {
            $sql .= " AND p.difficulty = :difficulty";
            $params[':difficulty'] = $filters['difficulty'];
        }
    }
    
    // Order by name
    $sql .= " ORDER BY p.name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Get a single plant by ID
function getPlantById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT p.*, c.name as category, c.color 
            FROM plants p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

// Get all categories
function getCategoryData() {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM categories";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $categories = [];
    foreach ($stmt->fetchAll() as $category) {
        $categories[$category['id']] = [
            'name' => $category['name'],
            'description' => $category['description'],
            'icon' => $category['icon'],
            'color' => $category['color']
        ];
    }
    
    return $categories;
}

// Get a single category by ID
function getCategoryById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT * FROM categories WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

// Count plants in a category
function countPlantsByCategory($categoryId) {
    $conn = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM plants WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':category_id' => $categoryId]);
    
    $result = $stmt->fetch();
    return $result['count'];
}

// Admin functions for managing plants

// Add a new plant
function addPlant($plantData) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO plants (name, scientific_name, category_id, image, difficulty, growth_time, 
                               description, planting_guide, care_instructions, harvest_instructions) 
            VALUES (:name, :scientific_name, :category_id, :image, :difficulty, :growth_time, 
                   :description, :planting_guide, :care_instructions, :harvest_instructions)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':name' => $plantData['name'],
        ':scientific_name' => $plantData['scientific_name'],
        ':category_id' => $plantData['category_id'],
        ':image' => $plantData['image'] ?? '/placeholder.svg?height=300&width=300',
        ':difficulty' => $plantData['difficulty'],
        ':growth_time' => $plantData['growth_time'],
        ':description' => $plantData['description'] ?? null,
        ':planting_guide' => $plantData['planting_guide'] ?? null,
        ':care_instructions' => $plantData['care_instructions'] ?? null,
        ':harvest_instructions' => $plantData['harvest_instructions'] ?? null
    ]);
    
    if ($result) {
        return $conn->lastInsertId();
    }
    
    return false;
}

// Update an existing plant
function updatePlant($id, $plantData) {
    $conn = getDbConnection();
    
    $sql = "UPDATE plants SET 
                name = :name,
                scientific_name = :scientific_name,
                category_id = :category_id,
                difficulty = :difficulty,
                growth_time = :growth_time,
                description = :description,
                planting_guide = :planting_guide,
                care_instructions = :care_instructions,
                harvest_instructions = :harvest_instructions";
    
    $params = [
        ':id' => $id,
        ':name' => $plantData['name'],
        ':scientific_name' => $plantData['scientific_name'],
        ':category_id' => $plantData['category_id'],
        ':difficulty' => $plantData['difficulty'],
        ':growth_time' => $plantData['growth_time'],
        ':description' => $plantData['description'] ?? null,
        ':planting_guide' => $plantData['planting_guide'] ?? null,
        ':care_instructions' => $plantData['care_instructions'] ?? null,
        ':harvest_instructions' => $plantData['harvest_instructions'] ?? null
    ];
    
    // Only update image if it's provided and not empty
    if (!empty($plantData['image'])) {
        $sql .= ", image = :image";
        $params[':image'] = $plantData['image'];
    }
    
    $sql .= " WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

// Delete a plant
function deletePlant($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM plants WHERE id = :id";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([':id' => $id]);
}

// Add a new category
function addCategory($categoryData) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO categories (id, name, description, icon, color) 
            VALUES (:id, :name, :description, :icon, :color)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':id' => $categoryData['id'],
        ':name' => $categoryData['name'],
        ':description' => $categoryData['description'],
        ':icon' => $categoryData['icon'],
        ':color' => $categoryData['color']
    ]);
}

// Update an existing category
function updateCategory($id, $categoryData) {
    $conn = getDbConnection();
    
    $sql = "UPDATE categories SET 
                name = :name,
                description = :description,
                icon = :icon,
                color = :color
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':name' => $categoryData['name'],
        ':description' => $categoryData['description'],
        ':icon' => $categoryData['icon'],
        ':color' => $categoryData['color']
    ]);
}

// Delete a category
function deleteCategory($id) {
    $conn = getDbConnection();
    
    // First update any plants in this category to have null category_id
    $sql1 = "UPDATE plants SET category_id = NULL WHERE category_id = :id";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([':id' => $id]);
    
    // Then delete the category
    $sql2 = "DELETE FROM categories WHERE id = :id";
    $stmt2 = $conn->prepare($sql2);
    return $stmt2->execute([':id' => $id]);
}

// User Authentication Functions

// Register a new user
function registerUser($userData) {
    $conn = getDbConnection();
    
    // Check if username or email already exists
    $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $userData['username'],
        ':email' => $userData['email']
    ]);
    
    if ($stmt->rowCount() > 0) {
        return [
            'success' => false,
            'message' => 'Username atau email sudah digunakan'
        ];
    }
    
    // Hash the password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Insert the new user
    $sql = "INSERT INTO users (username, email, password, full_name, role) 
            VALUES (:username, :email, :password, :full_name, 'user')";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':username' => $userData['username'],
        ':email' => $userData['email'],
        ':password' => $hashedPassword,
        ':full_name' => $userData['full_name']
    ]);
    
    if ($result) {
        return [
            'success' => true,
            'user_id' => $conn->lastInsertId(),
            'message' => 'Registrasi berhasil'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Gagal mendaftarkan pengguna'
    ];
}

// Login a user
function loginUser($username, $password) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, password, full_name, role, profile_image 
            FROM users 
            WHERE username = :username OR email = :email";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':email' => $username
    ]);
    
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Remove password from the array before returning
        unset($user['password']);
        
        return [
            'success' => true,
            'user' => $user,
            'message' => 'Login berhasil'
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Username/email atau password salah'
    ];
}

// Get user by ID
function getUserById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, role, profile_image, bio, location, created_at 
            FROM users 
            WHERE id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

// Get recent users
function getRecentUsers($limit = 5) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, role, profile_image, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Get all users
function getAllUsers() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, role, profile_image, created_at 
            FROM users 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Update user profile
function updateUserProfile($id, $userData) {
  $conn = getDbConnection();
  
  // Check if username is being changed and if it's available
  if (!empty($userData['username']) && $userData['username'] !== getUserById($id)['username']) {
      // Check if username already exists
      $checkSql = "SELECT id FROM users WHERE username = :username AND id != :id";
      $checkStmt = $conn->prepare($checkSql);
      $checkStmt->execute([
          ':username' => $userData['username'],
          ':id' => $id
      ]);
      
      if ($checkStmt->rowCount() > 0) {
          // Username already taken
          return false;
      }
  }
  
  $sql = "UPDATE users SET 
              full_name = :full_name";
  
  $params = [
      ':id' => $id,
      ':full_name' => $userData['full_name']
  ];
  
  // Add username if provided
  if (!empty($userData['username'])) {
      $sql .= ", username = :username";
      $params[':username'] = $userData['username'];
  }
  
  // Add optional fields if they exist
  if (isset($userData['bio'])) {
      $sql .= ", bio = :bio";
      $params[':bio'] = $userData['bio'];
  }
  
  if (isset($userData['location'])) {
      $sql .= ", location = :location";
      $params[':location'] = $userData['location'];
  }
  
  // Only update profile image if provided and not empty
  if (!empty($userData['profile_image'])) {
      $sql .= ", profile_image = :profile_image";
      $params[':profile_image'] = $userData['profile_image'];
  }
  
  // Only update password if provided
  if (!empty($userData['password'])) {
      $sql .= ", password = :password";
      $params[':password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
  }
  
  $sql .= " WHERE id = :id";
  
  $stmt = $conn->prepare($sql);
  return $stmt->execute($params);
}

// Comment Functions

// Get comments for a plant
function getCommentsByPlantId($plantId) {
    $conn = getDbConnection();
    
    $sql = "SELECT c.*, u.username, u.full_name, u.profile_image 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.plant_id = :plant_id 
            ORDER BY c.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':plant_id' => $plantId]);
    
    return $stmt->fetchAll();
}

// Add a comment
function addComment($commentData) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO comments (plant_id, user_id, comment) 
            VALUES (:plant_id, :user_id, :comment)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':plant_id' => $commentData['plant_id'],
        ':user_id' => $commentData['user_id'],
        ':comment' => $commentData['comment']
    ]);
    
    if ($result) {
        return $conn->lastInsertId();
    }
    
    return false;
}

// Delete a comment
function deleteComment($id, $userId) {
    $conn = getDbConnection();
    
    // Only allow the comment owner or admin to delete
    $sql = "DELETE FROM comments 
            WHERE id = :id 
            AND (user_id = :user_id OR :user_id IN (SELECT id FROM users WHERE role = 'admin'))";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId
    ]);
}

// Count comments for a plant
function countCommentsByPlantId($plantId) {
    $conn = getDbConnection();
    
    $sql = "SELECT COUNT(*) as count FROM comments WHERE plant_id = :plant_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':plant_id' => $plantId]);
    
    $result = $stmt->fetch();
    return $result['count'];
}

// Forum Functions

// Get forum topics
function getForumTopics($limit = 10, $offset = 0) {
    $conn = getDbConnection();
    
    $sql = "SELECT t.*, u.username, u.full_name, u.profile_image, 
                  (SELECT COUNT(*) FROM forum_replies WHERE topic_id = t.id) as reply_count,
                  (SELECT MAX(created_at) FROM forum_replies WHERE topic_id = t.id) as last_reply_at
            FROM forum_topics t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.is_pinned DESC, COALESCE(last_reply_at, t.created_at) DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Get forum topic by ID
function getForumTopicById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT t.*, u.username, u.full_name, u.profile_image 
            FROM forum_topics t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = :id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

// Get forum replies for a topic
function getForumRepliesByTopicId($topicId) {
    $conn = getDbConnection();
    
    $sql = "SELECT r.*, u.username, u.full_name, u.profile_image 
            FROM forum_replies r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.topic_id = :topic_id 
            ORDER BY r.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':topic_id' => $topicId]);
    
    return $stmt->fetchAll();
}

// Article Functions

// Get all articles
function getAllArticles() {
  $conn = getDbConnection();
  
  $sql = "SELECT * FROM articles ORDER BY created_at DESC";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  
  return $stmt->fetchAll();
}

// Get article by ID
function getArticleById($id) {
  $conn = getDbConnection();
  
  $sql = "SELECT * FROM articles WHERE id = :id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([':id' => $id]);
  
  return $stmt->fetch();
}

// Add a new article
function addArticle($articleData) {
  $conn = getDbConnection();
  
  $sql = "INSERT INTO articles (title, excerpt, content, image, author, created_at) 
          VALUES (:title, :excerpt, :content, :image, :author, NOW())";
  
  $stmt = $conn->prepare($sql);
  $result = $stmt->execute([
    ':title' => $articleData['title'],
    ':excerpt' => $articleData['excerpt'],
    ':content' => $articleData['content'],
    ':image' => $articleData['image'] ?? '/images/article-placeholder.jpg',
    ':author' => $articleData['author']
  ]);
  
  if ($result) {
    return $conn->lastInsertId();
  }
  
  return false;
}

// Update an existing article
function updateArticle($id, $articleData) {
  $conn = getDbConnection();
  
  $sql = "UPDATE articles SET 
            title = :title,
            excerpt = :excerpt,
            content = :content,
            author = :author";
  
  $params = [
    ':id' => $id,
    ':title' => $articleData['title'],
    ':excerpt' => $articleData['excerpt'],
    ':content' => $articleData['content'],
    ':author' => $articleData['author']
  ];
  
  // Only update image if provided
  if (!empty($articleData['image'])) {
    $sql .= ", image = :image";
    $params[':image'] = $articleData['image'];
  }
  
  $sql .= " WHERE id = :id";
  
  $stmt = $conn->prepare($sql);
  return $stmt->execute($params);
}

// Delete an article
function deleteArticle($id) {
  $conn = getDbConnection();
  
  $sql = "DELETE FROM articles WHERE id = :id";
  $stmt = $conn->prepare($sql);
  return $stmt->execute([':id' => $id]);
}

// Add these forum functions at the end of the file

// Create a new forum topic
function createForumTopic($topicData) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO forum_topics (user_id, title, content, category, is_pinned, created_at) 
            VALUES (:user_id, :title, :content, :category, 0, NOW())";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':user_id' => $topicData['user_id'],
        ':title' => $topicData['title'],
        ':content' => $topicData['content'],
        ':category' => $topicData['category']
    ]);
    
    if ($result) {
        return $conn->lastInsertId();
    }
    
    return false;
}

// Add a reply to a forum topic
function addForumReply($replyData) {
    $conn = getDbConnection();
    
    $sql = "INSERT INTO forum_replies (topic_id, user_id, content, created_at) 
            VALUES (:topic_id, :user_id, :content, NOW())";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':topic_id' => $replyData['topic_id'],
        ':user_id' => $replyData['user_id'],
        ':content' => $replyData['content']
    ]);
    
    if ($result) {
        // Update the last_reply_at time for the topic
        $updateSql = "UPDATE forum_topics SET last_reply_at = NOW() WHERE id = :topic_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([':topic_id' => $replyData['topic_id']]);
        
        return $conn->lastInsertId();
    }
    
    return false;
}

// Delete a forum topic
function deleteForumTopic($id, $userId) {
    $conn = getDbConnection();
    
    // First delete all replies
    $sql1 = "DELETE FROM forum_replies WHERE topic_id = :id";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->execute([':id' => $id]);
    
    // Then delete the topic (only if user is the owner or an admin)
    $sql2 = "DELETE FROM forum_topics 
             WHERE id = :id 
             AND (user_id = :user_id OR :user_id IN (SELECT id FROM users WHERE role = 'admin'))";
    
    $stmt2 = $conn->prepare($sql2);
    return $stmt2->execute([
        ':id' => $id,
        ':user_id' => $userId
    ]);
}

// Delete a forum reply
function deleteForumReply($id, $userId) {
    $conn = getDbConnection();
    
    // Only allow the reply owner or admin to delete
    $sql = "DELETE FROM forum_replies 
            WHERE id = :id 
            AND (user_id = :user_id OR :user_id IN (SELECT id FROM users WHERE role = 'admin'))";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId
    ]);
}
?>

