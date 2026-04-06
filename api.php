// Add this inside the switch statement
case 'register-animal':
    if ($request_method == 'POST') { registerAnimalFromRFID($pdo); }
    break;
case 'animal':
    if ($request_method == 'GET' && isset($_GET['tagId'])) { getAnimalByRFID($pdo); }
    break;

// Add these functions
function getAnimalByRFID($pdo) {
    $tagId = $_GET['tagId'];
    
    // Get animal with latest health record
    $stmt = $pdo->prepare("
        SELECT a.*, 
               h.type as healthType, 
               h.startDate, 
               h.nextEventDate,
               h.vetName
        FROM animals a
        LEFT JOIN (
            SELECT * FROM health_records 
            WHERE (tagId, createdAt) IN (
                SELECT tagId, MAX(createdAt) 
                FROM health_records 
                GROUP BY tagId
            )
        ) h ON a.tagId = h.tagId
        WHERE a.tagId = ?
    ");
    $stmt->execute([$tagId]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($animal) {
        echo json_encode($animal);
    } else {
        echo json_encode(null);
    }
}

function registerAnimalFromRFID($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Generate auto tag ID based on animal type
    $animalType = $data['animalType'];
    $stmt = $pdo->prepare("SELECT tagId FROM animals WHERE tagId LIKE ? ORDER BY tagId DESC LIMIT 1");
    $stmt->execute([$animalType . '-%']);
    $last = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($last) {
        $lastNum = intval(substr($last['tagId'], strlen($animalType) + 1));
        $newNum = $lastNum + 1;
    } else {
        $newNum = 1;
    }
    $newTagId = $animalType . '-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    
    // Insert new animal
    $stmt = $pdo->prepare("
        INSERT INTO animals (tagId, name, animalType, sex, breed, ownerContact) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $newTagId,
        $data['name'],
        $data['animalType'],
        $data['sex'],
        $data['breed'],
        $data['ownerContact']
    ]);
    
    echo json_encode(['success' => true, 'tagId' => $newTagId]);
}