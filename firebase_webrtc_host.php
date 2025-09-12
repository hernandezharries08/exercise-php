<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase WebRTC Screen Share Host</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Firebase WebRTC Screen Share Host</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Screen Sharing Status</h2>
                <div id="status" class="text-center text-gray-600 mb-4 p-4 bg-gray-50 rounded-md">
                    Ready to start screen sharing...
                </div>
                
                <div class="text-center mb-6">
                    <button id="start-btn" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Start Screen Sharing
                    </button>
                </div>
                
                <div id="share-info" class="hidden">
                    <h3 class="text-lg font-semibold mb-3">Share URL:</h3>
                    <div class="bg-gray-50 p-4 rounded-md mb-4">
                        <input type="text" id="share-url" class="w-full p-2 border rounded-md bg-white" readonly>
                        <button id="copy-btn" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Copy URL
                        </button>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p><strong>Instructions:</strong></p>
                        <p>1. Copy the URL above</p>
                        <p>2. Send it to the person you want to share your screen with</p>
                        <p>3. They will see your screen in real-time</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Screen Preview</h2>
                <video id="preview-video" class="w-full max-w-2xl mx-auto rounded-md" autoplay muted></video>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js";
        import { getFirestore, collection, doc, setDoc, getDoc, onSnapshot, deleteDoc } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-firestore.js";

        // Firebase configuration
        const firebaseConfig = {
            apiKey: "",
            authDomain: "",
            projectId: "screenshare-exercise",
            storageBucket: "",
            messagingSenderId: "",
            appId: "",
            databaseURL: ""
        };
        
        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
        
        // Test Firestore connection
        try {
            const testDoc = doc(db, 'test', 'connection');
            console.log('Firebase Firestore connection successful');
        } catch (error) {
            console.error('Firebase Firestore connection failed:', error);
        }

        // Global variables
        let currentStream = null;
        let peerConnection = null;
        let roomId = null;
        let offersRef = null;

        // Generate unique room ID
        function generateRoomId() {
            return 'room_' + Math.random().toString(36).substr(2, 9);
        }

        // Update status
        function updateStatus(message) {
            document.getElementById('status').textContent = message;
        }

        // Start screen sharing
        async function startScreenSharing() {
            try {
                updateStatus('Starting screen sharing...');
                console.log('Step 1: Starting screen sharing...');
                
                // Generate room ID
                roomId = generateRoomId();
                console.log('Step 2: Generated room ID:', roomId);
                
                // Create room in Firebase Firestore
                console.log('Step 3: Creating room in Firebase Firestore...');
                try {
                    const roomRef = doc(db, 'rooms', roomId);
                    await setDoc(roomRef, {
                        host: {
                            status: 'ready',
                            timestamp: Date.now()
                        },
                        viewers: {},
                        offers: {},
                        answers: {}
                    });
                    console.log('Step 4: Room created in Firebase Firestore:', roomId);
                } catch (error) {
                    console.error('Error creating room in Firebase:', error);
                    updateStatus('Firebase error: ' + error.message);
                    return;
                }
                
                // Start screen capture
                console.log('Step 5: Requesting screen capture...');
                currentStream = await navigator.mediaDevices.getDisplayMedia({
                    video: true,
                    audio: false
                });
                console.log('Step 6: Screen capture successful');
                
                // Set preview video
                const previewVideo = document.getElementById('preview-video');
                previewVideo.srcObject = currentStream;
                console.log('Step 7: Preview video set');
                
                // Create WebRTC peer connection
                console.log('Step 8: Creating WebRTC peer connection...');
                peerConnection = new RTCPeerConnection({
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        { urls: 'stun:stun2.l.google.com:19302' },
                        { urls: 'stun:stun3.l.google.com:19302' },
                        { urls: 'stun:stun4.l.google.com:19302' }
                    ],
                    iceCandidatePoolSize: 10
                });
                console.log('Step 9: WebRTC peer connection created');
                
                // Add stream to peer connection
                currentStream.getTracks().forEach(track => {
                    peerConnection.addTrack(track, currentStream);
                });
                console.log('Step 10: Stream tracks added to peer connection');
                
                // Handle connection state changes
                peerConnection.onconnectionstatechange = function() {
                    console.log('Connection state:', peerConnection.connectionState);
                    if (peerConnection.connectionState === 'connected') {
                        console.log('🎉 WebRTC connection established!');
                        updateStatus('Connected! Screen sharing active.');
                    } else if (peerConnection.connectionState === 'failed') {
                        console.log('❌ WebRTC connection failed');
                        updateStatus('Connection failed');
                    } else if (peerConnection.connectionState === 'connecting') {
                        console.log('🔄 WebRTC connection in progress...');
                        updateStatus('Connecting...');
                    }
                };
                
                // Handle ICE connection state changes
                peerConnection.oniceconnectionstatechange = function() {
                    console.log('ICE connection state:', peerConnection.iceConnectionState);
                    if (peerConnection.iceConnectionState === 'connected') {
                        console.log('ICE connection established!');
                        console.log('WebRTC connection successful!');
                    } else if (peerConnection.iceConnectionState === 'failed') {
                        console.log('ICE connection failed');
                        console.log('Debugging connection failure...');
                        console.log('Peer connection state:', peerConnection.connectionState);
                        console.log('Signaling state:', peerConnection.signalingState);
                        console.log('ICE gathering state:', peerConnection.iceGatheringState);
                        
                        // Check if we have any ICE candidates
                        peerConnection.getStats().then(stats => {
                            console.log('📊 Connection statistics:');
                            stats.forEach(report => {
                                if (report.type === 'candidate-pair' && report.state === 'failed') {
                                    console.log('Failed candidate pair:', report);
                                }
                            });
                        });
                    } else if (peerConnection.iceConnectionState === 'checking') {
                        console.log('🔄 ICE connection checking...');
                    } else if (peerConnection.iceConnectionState === 'disconnected') {
                        console.log('⚠️ ICE connection disconnected');
                    }
                };
                
                // handle ICE candidates
                console.log('Setting up ICE candidate handler...');
                peerConnection.onicecandidate = function(event) {
                    console.log('ICE candidate event triggered:', event);
                    if (event.candidate) {
                        console.log('ICE candidate generated:', event.candidate);

                        const candidateData = {
                            candidate: event.candidate.candidate,
                            sdpMid: event.candidate.sdpMid,
                            sdpMLineIndex: event.candidate.sdpMLineIndex,
                            foundation: event.candidate.foundation,
                            component: event.candidate.component,
                            priority: event.candidate.priority,
                            protocol: event.candidate.protocol,
                            port: event.candidate.port,
                            type: event.candidate.type,
                            tcpType: event.candidate.tcpType,
                            relatedAddress: event.candidate.relatedAddress,
                            relatedPort: event.candidate.relatedPort,
                            usernameFragment: event.candidate.usernameFragment
                        };
                        setDoc(doc(db, 'rooms', roomId, 'host', 'iceCandidate'), {
                            candidate: candidateData,
                            timestamp: Date.now()
                        }).then(() => {
                            console.log('ICE candidate saved to Firebase');
                        }).catch((error) => {
                            console.error('Error saving ICE candidate:', error);
                        });
                    } else {
                        console.log('ICE gathering completed');
                    }
                };
                console.log('ICE candidate handler set up');
                
                // handle incoming connection
                peerConnection.ontrack = function(event) {
                    console.log('Received remote stream');
                    updateStatus('Viewer connected! Screen sharing active.');
                };
                
                // show share URL
                const shareUrl = `${window.location.origin}${window.location.pathname.replace('firebase_webrtc_host.php', 'firebase_webrtc_viewer.php')}?id=${roomId}`;
                document.getElementById('share-url').value = shareUrl;
                document.getElementById('share-info').classList.remove('hidden');
                
                updateStatus('Screen sharing ready! Share the URL with viewers.');
                console.log('Step 11: Screen sharing ready');
                
                // listen for offers from viewers
                console.log('Setting up offer listener for room:', roomId);
                const offersRef = collection(db, 'rooms', roomId, 'offers');
                onSnapshot(offersRef, async (snapshot) => {
                    console.log('Offer listener triggered, snapshot size:', snapshot.size);
                    if (!snapshot.empty) {
                        console.log('Received offers:', snapshot.docs.length);
                        
                        // process the latest offer
                        const latestDoc = snapshot.docs[snapshot.docs.length - 1];
                        const offerData = latestDoc.data();
                        console.log('Processing offer from:', offerData.from);
                        const offer = JSON.parse(offerData.offer);
                        
                        console.log('Setting remote description...');
                        await peerConnection.setRemoteDescription(offer);
                        console.log('Remote description set successfully');
                        
                        console.log('Creating answer...');
                        const answer = await peerConnection.createAnswer();
                        console.log('Answer created successfully');
                        
                        console.log('Setting local description...');
                        await peerConnection.setLocalDescription(answer);
                        console.log('Local description set successfully');
                        
                        // ICE candidate handler is already set up
                        
                        // Trigger ICE gathering after setting local description
                        console.log('ICE gathering should start now...');
                        console.log('Peer connection state:', peerConnection.connectionState);
                        console.log('ICE connection state:', peerConnection.iceConnectionState);
                        
                        // Wait for ICE gathering to start naturally
                        console.log('Waiting for ICE gathering to start naturally...');
                        setTimeout(() => {
                            console.log('Checking ICE gathering state after delay...');
                            console.log('Peer connection state:', peerConnection.connectionState);
                            console.log('ICE connection state:', peerConnection.iceConnectionState);
                            console.log('ICE gathering state:', peerConnection.iceGatheringState);
                            
                            // If ICE gathering hasn't started, try to force it
                            if (peerConnection.iceGatheringState === 'new') {
                                console.log('ICE gathering still in new state, trying to force start...');
                                try {
                                    // Try multiple approaches to trigger ICE gathering
                                    peerConnection.restartIce();
                                    console.log('ICE restart attempted');
                                    
                                    // Also try to trigger ICE gathering by creating a new offer
                                    setTimeout(async () => {
                                        try {
                                            console.log('Trying to trigger ICE gathering with new offer...');
                                            const newOffer = await peerConnection.createOffer();
                                            await peerConnection.setLocalDescription(newOffer);
                                            console.log('New offer created and set to trigger ICE gathering');
                                        } catch (error) {
                                            console.log('Failed to create new offer:', error.message);
                                        }
                                    }, 500);
                                    
                                } catch (error) {
                                    console.log('ICE restart failed:', error.message);
                                }
                            }
                            
                            // Check again after another delay
                            setTimeout(() => {
                                console.log('Checking ICE gathering state after second delay...');
                                console.log('Peer connection state:', peerConnection.connectionState);
                                console.log('ICE connection state:', peerConnection.iceConnectionState);
                                console.log('ICE gathering state:', peerConnection.iceGatheringState);
                            }, 2000);
                        }, 1000);
                        
                        // Send answer back
                        console.log('Sending answer to viewer...');
                        await setDoc(doc(db, 'rooms', roomId, 'answers', Date.now().toString()), {
                            answer: JSON.stringify(answer),
                            from: 'host',
                            timestamp: Date.now()
                        });
                        
                        console.log('Answer sent to viewer');
                        updateStatus('Viewer connected! Screen sharing active.');
                        
                        // Exchange ICE candidates with viewer
                        console.log('Starting ICE candidate exchange with viewer...');
                        exchangeIceCandidates();
                    } else {
                        console.log('No offers received yet');
                    }
                });
                
            } catch (error) {
                console.error('Error starting screen sharing:', error);
                updateStatus('Error: ' + error.message);
            }
        }

        // Exchange ICE candidates with viewer
        async function exchangeIceCandidates() {
            try {
                console.log('Host: Checking for viewer ICE candidates...');
                // Get viewer ICE candidates
                const viewerIceRef = doc(db, 'rooms', roomId, 'viewer', 'iceCandidate');
                const viewerIceSnapshot = await getDoc(viewerIceRef);
                
                if (viewerIceSnapshot.exists()) {
                    const viewerIceData = viewerIceSnapshot.data();
                    console.log('Host: Found viewer ICE candidate:', viewerIceData.candidate);
                    // Reconstruct RTCIceCandidate object from Firebase data
                    const candidate = new RTCIceCandidate({
                        candidate: viewerIceData.candidate.candidate,
                        sdpMid: viewerIceData.candidate.sdpMid,
                        sdpMLineIndex: viewerIceData.candidate.sdpMLineIndex
                    });
                    await peerConnection.addIceCandidate(candidate);
                    console.log('Host: Viewer ICE candidate added successfully');
                } else {
                    console.log('Host: No viewer ICE candidate found yet');
                }
                
                // Listen for new ICE candidates from viewer
                console.log('Host: Setting up viewer ICE candidate listener...');
                onSnapshot(viewerIceRef, async (snapshot) => {
                    if (snapshot.exists()) {
                        const iceData = snapshot.data();
                        console.log('Host: Received new viewer ICE candidate:', iceData.candidate);
                        try {
                            // Reconstruct RTCIceCandidate object from Firebase data
                            const candidate = new RTCIceCandidate({
                                candidate: iceData.candidate.candidate,
                                sdpMid: iceData.candidate.sdpMid,
                                sdpMLineIndex: iceData.candidate.sdpMLineIndex
                            });
                            await peerConnection.addIceCandidate(candidate);
                            console.log('Host: New viewer ICE candidate added successfully');
                        } catch (error) {
                            console.error('Host: Error adding ICE candidate:', error);
                        }
                    } else {
                        console.log('Host: Viewer ICE candidate document does not exist');
                    }
                });
                
            } catch (error) {
                console.error('Host: Error exchanging ICE candidates:', error);
            }
        }

        // Event listeners
        document.getElementById('start-btn').addEventListener('click', startScreenSharing);
        
        document.getElementById('copy-btn').addEventListener('click', function() {
            const shareUrl = document.getElementById('share-url');
            shareUrl.select();
            document.execCommand('copy');
            updateStatus('URL copied to clipboard!');
        });

        // Handle stream end
        window.addEventListener('beforeunload', function() {
            if (roomId) {
                deleteDoc(doc(db, 'rooms', roomId));
            }
        });
    </script>
</body>
</html>
