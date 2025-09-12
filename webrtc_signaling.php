<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? '';

$signalingFile = 'webrtc_signaling.json';

// Initialize signaling data if file doesn't exist
if (!file_exists($signalingFile)) {
    file_put_contents($signalingFile, json_encode([]));
}

$signalingData = json_decode(file_get_contents($signalingFile), true);

switch ($action) {
    case 'create_room':
        $roomId = $input['roomId'] ?? 'room_' . uniqid();
        $signalingData[$roomId] = [
            'host' => [
                'status' => 'ready',
                'timestamp' => time()
            ],
            'viewers' => [],
            'offers' => [],
            'answers' => [],
            'ice_candidates' => []
        ];
        file_put_contents($signalingFile, json_encode($signalingData));
        echo json_encode(['success' => true, 'roomId' => $roomId]);
        break;

    case 'join_room':
        $roomId = $input['roomId'] ?? '';
        if (isset($signalingData[$roomId])) {
            $signalingData[$roomId]['viewers'][] = [
                'id' => uniqid(),
                'timestamp' => time()
            ];
            file_put_contents($signalingFile, json_encode($signalingData));
            echo json_encode(['success' => true, 'roomId' => $roomId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'send_offer':
        $roomId = $input['roomId'] ?? '';
        $offer = $input['offer'] ?? '';
        if (isset($signalingData[$roomId])) {
            $signalingData[$roomId]['offers'][] = [
                'offer' => $offer,
                'from' => 'viewer',
                'timestamp' => time()
            ];
            file_put_contents($signalingFile, json_encode($signalingData));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'send_answer':
        $roomId = $input['roomId'] ?? '';
        $answer = $input['answer'] ?? '';
        if (isset($signalingData[$roomId])) {
            $signalingData[$roomId]['answers'][] = [
                'answer' => $answer,
                'from' => 'host',
                'timestamp' => time()
            ];
            file_put_contents($signalingFile, json_encode($signalingData));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'get_offers':
        $roomId = $input['roomId'] ?? '';
        if (isset($signalingData[$roomId])) {
            echo json_encode(['success' => true, 'offers' => $signalingData[$roomId]['offers']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'get_answers':
        $roomId = $input['roomId'] ?? '';
        if (isset($signalingData[$roomId])) {
            echo json_encode(['success' => true, 'answers' => $signalingData[$roomId]['answers']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'send_ice_candidate':
        $roomId = $input['roomId'] ?? '';
        $candidate = $input['candidate'] ?? '';
        $from = $input['from'] ?? '';
        if (isset($signalingData[$roomId])) {
            $signalingData[$roomId]['ice_candidates'][] = [
                'candidate' => $candidate,
                'from' => $from,
                'timestamp' => time()
            ];
            file_put_contents($signalingFile, json_encode($signalingData));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    case 'get_ice_candidates':
        $roomId = $input['roomId'] ?? '';
        if (isset($signalingData[$roomId])) {
            echo json_encode(['success' => true, 'candidates' => $signalingData[$roomId]['ice_candidates']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Room not found']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
?>
