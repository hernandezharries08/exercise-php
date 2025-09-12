<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firebase WebRTC Screen Share Viewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Firebase WebRTC Screen Share Viewer</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Connection Status</h2>
                <div id="status" class="text-center text-gray-600 mb-4 p-4 bg-gray-50 rounded-md">
                    Connecting to host...
                </div>
                
                <div class="text-center mb-6">
                    <button id="connect-btn" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Connect to Host
                    </button>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Shared Screen</h2>
                <video id="shared-video" class="w-full max-w-2xl mx-auto rounded-md" autoplay muted playsinline></video>
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
        let peerConnection = null;
        let roomId = null;
        let answersRef = null;
        let answerProcessed = false;

        // Get room ID from URL
        function getRoomIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        // Update status
        function updateStatus(message) {
            document.getElementById('status').textContent = message;
        }

        // Connect to host
        async function connectToHost() {
            try {
                roomId = getRoomIdFromUrl();
                if (!roomId) {
                    updateStatus('No room ID provided in URL');
                    return;
                }

                updateStatus('Connecting to host...');
                console.log('Connecting to room:', roomId);
                
                // Reset answer processed flag
                answerProcessed = false;

                // Check if room exists in Firebase Firestore
                const roomRef = doc(db, 'rooms', roomId);
                const roomSnapshot = await getDoc(roomRef);
                if (!roomSnapshot.exists()) {
                    throw new Error('Room not found');
                }
                console.log('Room found in Firebase Firestore');

                // Create WebRTC peer connection
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
                
                // Add a dummy data channel to trigger ICE gathering
                try {
                    const dataChannel = peerConnection.createDataChannel('dummy', {
                        ordered: true
                    });
                    console.log('Dummy data channel created to trigger ICE gathering');
                } catch (error) {
                    console.log('Failed to create dummy data channel:', error.message);
                }

                // Handle incoming stream
                peerConnection.ontrack = function(event) {
                    console.log('🎥 Received stream from host!');
                    console.log('Stream tracks:', event.streams[0].getTracks());
                    console.log('Stream active:', event.streams[0].active);
                    console.log('Stream id:', event.streams[0].id);
                    console.log('Track count:', event.streams[0].getTracks().length);
                    
                    // Check each track
                    event.streams[0].getTracks().forEach((track, index) => {
                        console.log(`Track ${index}:`, {
                            kind: track.kind,
                            enabled: track.enabled,
                            readyState: track.readyState,
                            muted: track.muted
                        });
                    });
                    
                    const sharedVideo = document.getElementById('shared-video');
                    console.log('Video element found:', !!sharedVideo);
                    
                    sharedVideo.srcObject = event.streams[0];
                    console.log('Video srcObject set:', !!sharedVideo.srcObject);
                    
                    // Add event listeners to debug video loading
                    sharedVideo.onloadstart = () => console.log('Video load started');
                    sharedVideo.onloadedmetadata = () => console.log('Video metadata loaded');
                    sharedVideo.onloadeddata = () => console.log('Video data loaded');
                    sharedVideo.oncanplay = () => console.log('Video can play');
                    sharedVideo.onplay = () => console.log('Video started playing');
                    sharedVideo.onerror = (e) => console.log('Video error:', e);
                    
                    updateStatus('Connected! Viewing host screen.');
                };
                
                // Handle connection state changes
                peerConnection.onconnectionstatechange = function() {
                    console.log('Connection state:', peerConnection.connectionState);
                    if (peerConnection.connectionState === 'connected') {
                        console.log('🎉 WebRTC connection established!');
                        updateStatus('Connected! Viewing host screen.');
                        
                        // Check if we have remote streams
                        const remoteStreams = peerConnection.getRemoteStreams();
                        console.log('Remote streams:', remoteStreams.length);
                        if (remoteStreams.length > 0) {
                            console.log('✅ Remote stream received!');
                            remoteVideo.srcObject = remoteStreams[0];
                            updateStatus('Screen sharing active!');
                        } else {
                            console.log('⚠️ No remote streams found');
                        }
                    } else if (peerConnection.connectionState === 'failed') {
                        console.log('❌ WebRTC connection failed - attempting retry...');
                        updateStatus('Connection failed. Retrying...');
                        
                        // Add retry limit
                        if (!window.retryCount) {
                            window.retryCount = 0;
                        }
                        window.retryCount++;
                        
                        if (window.retryCount <= 3) {
                            // Retry connection after a short delay
                            setTimeout(() => {
                                console.log(`🔄 Retrying WebRTC connection (attempt ${window.retryCount}/3)...`);
                                connectToHost();
                            }, 2000);
                        } else {
                            console.log('❌ Max retry attempts reached. Connection failed.');
                            updateStatus('Connection failed after multiple attempts. Please check your network.');
                            window.retryCount = 0; // Reset for next session
                        }
                    } else if (peerConnection.connectionState === 'connecting') {
                        console.log('🔄 WebRTC connection in progress...');
                        updateStatus('Connecting...');
                    }
                };
                
                // Handle ICE connection state changes
                peerConnection.oniceconnectionstatechange = function() {
                    console.log('ICE connection state:', peerConnection.iceConnectionState);
                    if (peerConnection.iceConnectionState === 'connected') {
                        console.log('🎉 ICE connection established!');
                        console.log('✅ WebRTC connection successful!');
                    } else if (peerConnection.iceConnectionState === 'failed') {
                        console.log('❌ ICE connection failed');
                        console.log('🔍 Debugging connection failure...');
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

                // Handle incoming tracks (remote streams)
                peerConnection.ontrack = function(event) {
                    console.log('🎥 Remote track received:', event.track.kind);
                    if (event.streams && event.streams[0]) {
                        console.log('✅ Remote stream received!');
                        remoteVideo.srcObject = event.streams[0];
                        updateStatus('Screen sharing active!');
                    }
                };
                
                // Handle ICE candidates
                console.log('Setting up viewer ICE candidate handler...');
                peerConnection.onicecandidate = function(event) {
                    console.log('Viewer ICE candidate event triggered:', event);
                    if (event.candidate) {
                        console.log('Viewer ICE candidate generated:', event.candidate);
                        console.log('Candidate type:', event.candidate.type);
                        console.log('Candidate protocol:', event.candidate.protocol);
                        console.log('Candidate address:', event.candidate.address);
                        console.log('Candidate candidate string:', event.candidate.candidate);
                        
                        // Check if this is a TURN candidate
                        if (event.candidate.type === 'relay') {
                            console.log('🎯 TURN candidate found!');
                        } else if (event.candidate.type === 'srflx') {
                            console.log('🌐 STUN candidate found!');
                        } else if (event.candidate.type === 'host') {
                            console.log('🏠 Host candidate found!');
                        }
                        
                        // Check if candidate string contains TURN server info
                        if (event.candidate.candidate.includes('openrelay.metered.ca')) {
                            console.log('🎯 TURN server candidate detected!');
                        }
                        
                        // Convert RTCIceCandidate to plain object for Firebase
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
                        setDoc(doc(db, 'rooms', roomId, 'viewer', 'iceCandidate'), {
                            candidate: candidateData,
                            timestamp: Date.now()
                        }).then(() => {
                            console.log('Viewer ICE candidate saved to Firebase');
                        }).catch((error) => {
                            console.error('Error saving viewer ICE candidate:', error);
                        });
                    } else {
                        console.log('Viewer ICE gathering completed');
                    }
                };
                console.log('Viewer ICE candidate handler set up');

                // Create offer
                const offer = await peerConnection.createOffer();
                await peerConnection.setLocalDescription(offer);
                console.log('Offer created:', offer);
                console.log('Viewer ICE gathering state after offer:', peerConnection.iceGatheringState);

                // Send offer to host via Firebase Firestore
                await setDoc(doc(db, 'rooms', roomId, 'offers', Date.now().toString()), {
                    offer: JSON.stringify(offer),
                    from: 'viewer',
                    timestamp: Date.now()
                });

                updateStatus('Offer sent. Waiting for host response...');

                // Listen for answers from host
                const answersRef = collection(db, 'rooms', roomId, 'answers');
                onSnapshot(answersRef, async (snapshot) => {
                    if (!snapshot.empty && !answerProcessed) {
                        console.log('Received answers:', snapshot.docs.length);
                        
                        // Process the latest answer
                        const latestDoc = snapshot.docs[snapshot.docs.length - 1];
                        const answerData = latestDoc.data();
                        const answer = JSON.parse(answerData.answer);
                        
                        try {
                            await peerConnection.setRemoteDescription(answer);
                            console.log('Answer set successfully');
                            answerProcessed = true;
                            
                            // Trigger ICE gathering after setting remote description
                            console.log('Viewer ICE gathering state after answer:', peerConnection.iceGatheringState);
                            console.log('Triggering ICE gathering...');
                            try {
                                await peerConnection.restartIce();
                                console.log('Viewer ICE restart successful');
                                
                            // Wait a bit for ICE gathering to start
                            setTimeout(() => {
                                console.log('Viewer ICE gathering state after restart:', peerConnection.iceGatheringState);
                                console.log('Viewer peer connection state:', peerConnection.connectionState);
                                console.log('Viewer ICE connection state:', peerConnection.iceConnectionState);
                                
                                // If ICE gathering still hasn't started, try to force it
                                if (peerConnection.iceGatheringState === 'new') {
                                    console.log('Viewer ICE gathering still in new state, trying to force start...');
                                    try {
                                        peerConnection.restartIce();
                                        console.log('Viewer ICE restart attempted again');
                                        
                                        // Also try to trigger ICE gathering by creating a new offer
                                        setTimeout(async () => {
                                            try {
                                                console.log('Trying to trigger ICE gathering with new offer...');
                                                const newOffer = await peerConnection.createOffer();
                                                await peerConnection.setLocalDescription(newOffer);
                                                console.log('New offer created and set to trigger ICE gathering');
                                                
                                                // Wait for ICE gathering to start after new offer
                                                setTimeout(() => {
                                                    console.log('Viewer ICE gathering state after new offer:', peerConnection.iceGatheringState);
                                                    if (peerConnection.iceGatheringState === 'gathering') {
                                                        console.log('Viewer ICE gathering finally started!');
                                                    } else {
                                                        console.log('Viewer ICE gathering still not started, trying one more time...');
                                                        try {
                                                            peerConnection.restartIce();
                                                            console.log('Final ICE restart attempt');
                                                        } catch (error) {
                                                            console.log('Final ICE restart failed:', error.message);
                                                        }
                                                    }
                                                }, 1000);
                                                
                                            } catch (error) {
                                                console.log('Failed to create new offer:', error.message);
                                            }
                                        }, 500);
                                        
                                    } catch (error) {
                                        console.log('Viewer ICE restart failed again:', error.message);
                                    }
                                }
                            }, 1000);
                                
                            } catch (iceError) {
                                console.log('Viewer ICE restart failed (this is normal):', iceError.message);
                            }
                            
                            // Exchange ICE candidates
                            console.log('Starting ICE candidate exchange...');
                            await exchangeIceCandidates();
                            console.log('ICE candidate exchange completed');
                        } catch (error) {
                            console.error('Error setting remote description:', error);
                        }
                    }
                });

            } catch (error) {
                console.error('Error connecting to host:', error);
                updateStatus('Connection error: ' + error.message);
            }
        }

        // Exchange ICE candidates
        async function exchangeIceCandidates() {
            try {
                console.log('Checking for host ICE candidates...');
                // Get host ICE candidates
                const hostIceRef = doc(db, 'rooms', roomId, 'host', 'iceCandidate');
                const hostIceSnapshot = await getDoc(hostIceRef);
                
                if (hostIceSnapshot.exists()) {
                    const hostIceData = hostIceSnapshot.data();
                    console.log('Found host ICE candidate:', hostIceData.candidate);
                    
                    // Check if peer connection is ready for ICE candidates
                    console.log('Peer connection state:', peerConnection.connectionState);
                    console.log('ICE connection state:', peerConnection.iceConnectionState);
                    console.log('Signaling state:', peerConnection.signalingState);
                    
                    // Reconstruct RTCIceCandidate object from Firebase data
                    const candidate = new RTCIceCandidate({
                        candidate: hostIceData.candidate.candidate,
                        sdpMid: hostIceData.candidate.sdpMid,
                        sdpMLineIndex: hostIceData.candidate.sdpMLineIndex
                    });
                    
                    console.log('Attempting to add ICE candidate:', candidate);
                    
                    // Wait a bit for ICE gathering to start before adding candidates
                    setTimeout(async () => {
                        try {
                            console.log('Viewer ICE gathering state before adding candidate:', peerConnection.iceGatheringState);
                            if (peerConnection.iceGatheringState === 'new') {
                                console.log('Viewer ICE gathering still not started, waiting longer...');
                                // Wait another 2 seconds
                                setTimeout(async () => {
                                    try {
                                        await peerConnection.addIceCandidate(candidate);
                                        console.log('Host ICE candidate added successfully after extended delay');
                                    } catch (error) {
                                        console.log('Error adding ICE candidate after extended delay:', error.message);
                                    }
                                }, 2000);
                            } else {
                                await peerConnection.addIceCandidate(candidate);
                                console.log('Host ICE candidate added successfully');
                            }
                        } catch (error) {
                            console.log('Error adding ICE candidate after delay:', error.message);
                        }
                    }, 2000);
                } else {
                    console.log('No host ICE candidate found yet');
                }
                
                // Listen for new ICE candidates from host
                console.log('Setting up ICE candidate listener...');
                onSnapshot(hostIceRef, async (snapshot) => {
                    if (snapshot.exists()) {
                        const iceData = snapshot.data();
                        console.log('Received new host ICE candidate:', iceData.candidate);
                        try {
                            // Check if peer connection is ready for ICE candidates
                            console.log('Peer connection state:', peerConnection.connectionState);
                            console.log('ICE connection state:', peerConnection.iceConnectionState);
                            console.log('Signaling state:', peerConnection.signalingState);
                            
                            // Reconstruct RTCIceCandidate object from Firebase data
                            const candidate = new RTCIceCandidate({
                                candidate: iceData.candidate.candidate,
                                sdpMid: iceData.candidate.sdpMid,
                                sdpMLineIndex: iceData.candidate.sdpMLineIndex
                            });
                            
                            console.log('Attempting to add ICE candidate:', candidate);
                            await peerConnection.addIceCandidate(candidate);
                            console.log('New host ICE candidate added successfully');
                        } catch (error) {
                            console.error('Error adding ICE candidate:', error);
                            console.error('Candidate data:', iceData.candidate);
                        }
                    } else {
                        console.log('Host ICE candidate document does not exist');
                    }
                });
                
            } catch (error) {
                console.error('Error exchanging ICE candidates:', error);
            }
        }

        // Event listeners
        document.getElementById('connect-btn').addEventListener('click', connectToHost);

        // Auto-connect if room ID is present
        window.addEventListener('load', function() {
            const roomId = getRoomIdFromUrl();
            if (roomId) {
                updateStatus(`Room ID: ${roomId}. Click Connect to join.`);
            } else {
                updateStatus('No room ID found in URL');
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            // Firestore listeners are automatically cleaned up
        });
    </script>
</body>
</html>
