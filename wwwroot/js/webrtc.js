// Complete Multi-User WebRTC Conference with Join Request Approval System
// Place this file in wwwroot/js/webrtc.js

class MultiUserWebRTC {
    constructor() {
        this.connection = null;
        this.localStream = null;
        this.peers = new Map(); // Map of connectionId -> RTCPeerConnection
        this.currentRoom = null;
        this.currentUser = null;
        this.isAudioEnabled = true;
        this.isVideoEnabled = true;
        this.isScreenSharing = false;
        this.mediaRecorder = null;
        this.recordedChunks = [];
        this.recordings = [];
        this.participants = new Map(); // Map of connectionId -> participant info
        this.isRoomCreator = false;
        this.roomLinkModal = null;
        this.joiningModal = null;
        this.managementModal = null;
        this.roomTable = null;

        // NEW: Join Request Management
        this.pendingJoinRequests = new Map(); // Map of requestId -> join request data
        this.isWaitingForApproval = false;
        this.currentJoinRequestId = null;

        this.initialize();
    }

    async initialize() {
        await this.setupSignalR();
        await this.setupMediaDevices();
        this.setupEventHandlers();
        this.setupDataTable();
        console.log("Multi-User WebRTC initialized successfully");
    }

    async setupSignalR() {
        try {
            this.connection = new signalR.HubConnectionBuilder()
                .withUrl("/videoChatHub")
                .withAutomaticReconnect([0, 2000, 10000, 30000])
                .configureLogging(signalR.LogLevel.Information)
                .build();

            this.setupSignalRHandlers();
            this.setupConnectionHandlers();

            await this.connection.start();
            console.log("✅ SignalR Connected successfully");

            this.updateConnectionStatus("Connected to server. Ready to create or join rooms.");
            setTimeout(() => this.checkForRoomInUrl(), 1000);

            setTimeout(() => {
                this.connection.invoke("GetRooms").catch(err =>
                    console.error("Error getting initial room list:", err)
                );
            }, 2000);

        } catch (err) {
            console.error("❌ SignalR Connection Error:", err);
            this.updateConnectionStatus("Failed to connect to server. Retrying...");
            setTimeout(() => this.setupSignalR(), 5000);
        }
    }

    setupConnectionHandlers() {
        this.connection.onreconnecting(() => {
            console.log("🔄 SignalR Reconnecting...");
            this.updateConnectionStatus("Reconnecting to server...");
        });

        this.connection.onreconnected(() => {
            console.log("✅ SignalR Reconnected!");
            this.updateConnectionStatus("Reconnected to server successfully.");

            setTimeout(() => {
                this.connection.invoke("GetRooms").catch(err =>
                    console.error("Error getting rooms after reconnection:", err)
                );
            }, 1000);
        });

        this.connection.onclose(() => {
            console.log("❌ SignalR Connection closed");
            this.updateConnectionStatus("Connection lost. Attempting to reconnect...");
            setTimeout(() => this.setupSignalR(), 2000);
        });
    }

    setupSignalRHandlers() {
        // Enhanced Room management with creator support
        this.connection.on("RoomCreated", (room, user, isCreator) => {
            console.log("Room created:", room, "User:", user, "Is Creator:", isCreator);

            if (isCreator) {
                this.handleRoomJoined(room, user, true);
                this.showRoomLink(room);
            } else {
                this.addRoomToTable(room);
            }
        });

        this.connection.on("RoomJoined", (room, user, isCreator) => {
            console.log("Joined room:", room, "as user:", user, "Is Creator:", isCreator);
            this.handleRoomJoined(room, user, isCreator || false);
        });

        // NEW: Join Request Events
        this.connection.on("JoinRequestReceived", (requestId, requesterName, requesterConnectionId, timestamp) => {
            console.log("Join request received:", { requestId, requesterName, requesterConnectionId, timestamp });
            this.handleJoinRequestReceived(requestId, requesterName, requesterConnectionId, timestamp);
        });

        this.connection.on("JoinRequestApproved", (room, user) => {
            console.log("Join request approved, joining room:", room);
            this.handleJoinRequestApproved(room, user);
        });

        this.connection.on("JoinRequestDeclined", (reason) => {
            console.log("Join request declined:", reason);
            this.handleJoinRequestDeclined(reason);
        });

        this.connection.on("WaitingForApproval", (requestId) => {
            console.log("Waiting for approval with request ID:", requestId);
            this.handleWaitingForApproval(requestId);
        });

        this.connection.on("UserJoined", (user) => {
            console.log("User joined:", user);
            this.handleUserJoined(user);
        });

        this.connection.on("UserLeft", (connectionId) => {
            console.log("User left:", connectionId);
            this.handleUserLeft(connectionId);
        });

        this.connection.on("RoomList", (rooms) => {
            console.log("Room list received:", rooms);
            this.updateRoomTable(rooms);
        });
        this.connection.on("MeetingEnded", (data) => {
            console.log("Meeting ended:", data.message);
            this.handleMeetingEnded();
        });


        this.connection.on("ParticipantRemoved", (connectionId, participantName) => {
            console.log("Participant removed:", participantName);
            this.handleParticipantRemoved(connectionId, participantName);
        });

        this.connection.on("YouWereRemoved", (reason) => {
            console.log("You were removed from meeting:", reason);
            this.handleYouWereRemoved(reason);
        });

        this.connection.on("MeetingEnded", () => {
            console.log("Meeting ended by host");
            this.handleMeetingEnded();
        });
        // WebRTC signaling
        this.connection.on("ReceiveOffer", async (fromConnectionId, offer) => {
            console.log("Received offer from:", fromConnectionId);
            await this.handleReceiveOffer(fromConnectionId, offer);
        });

        this.connection.on("ReceiveAnswer", async (fromConnectionId, answer) => {
            console.log("Received answer from:", fromConnectionId);
            await this.handleReceiveAnswer(fromConnectionId, answer);
        });

        this.connection.on("ReceiveIceCandidate", async (fromConnectionId, candidate) => {
            console.log("Received ICE candidate from:", fromConnectionId);
            await this.handleReceiveIceCandidate(fromConnectionId, candidate);
        });

        // Chat and messaging
        this.connection.on("ReceiveMessage", (user, message, timestamp) => {
            this.displayMessage(user, message, timestamp);
        });

        // File sharing
        this.connection.on("ReceiveFile", (fileName, fileData, fileSize, senderName) => {
            this.handleFileReceived(fileName, fileData, fileSize, senderName);
        });

        // Media control events
        this.connection.on("UserAudioToggled", (connectionId, isEnabled) => {
            this.updateParticipantAudioStatus(connectionId, isEnabled);
        });

        this.connection.on("UserVideoToggled", (connectionId, isEnabled) => {
            this.updateParticipantVideoStatus(connectionId, isEnabled);
        });

        // Error handling
        this.connection.on("Error", (error) => {
            console.error("SignalR Error:", error);
            this.updateConnectionStatus(`Error: ${error}`);
            alert(`Error: ${error}`);
        });
        this.connection.on("HostControlledYourAudio", (isEnabled, hostName) => {
            this.handleHostControlledAudio(isEnabled, hostName);
        });

        this.connection.on("HostControlledYourVideo", (isEnabled, hostName) => {
            this.handleHostControlledVideo(isEnabled, hostName);
        });

        this.connection.on("ParticipantMediaControlled", (connectionId, mediaType, isEnabled, hostName) => {
            this.handleParticipantMediaControlled(connectionId, mediaType, isEnabled, hostName);
        });
        this.connection.on("ParticipantListUpdated", (participants) => {
            console.log("Participant list updated:", participants);
            this.updateParticipantsFromServer(participants);
        });
        this.connection.on("HostTransferred", (newHostConnectionId, newHostName) => {
            console.log(`Host transferred to: ${newHostName} (${newHostConnectionId})`);

            // Update participants list - mark new host
            this.participants.forEach((participant, connectionId) => {
                if (connectionId === newHostConnectionId) {
                    participant.isCreator = true;
                } else {
                    participant.isCreator = false;
                }
            });
            if (this.currentUser && this.currentUser.connectionId === newHostConnectionId) {
                this.isRoomCreator = true;

                // Update button for new host
                const endCallBtn = document.getElementById('endCallBtn');
                if (endCallBtn) {
                    endCallBtn.innerHTML = '🔚 End Meeting';
                    endCallBtn.title = 'End meeting for all participants';
                }

                // Show host transfer notification
                this.showHostTransferNotification(`You are now the meeting host!`, 'success');

                // Apply creator styling to local video
                this.applyCreatorVideoLayout();
            } else {
                this.isRoomCreator = false;

                // Update button for regular participants
                const endCallBtn = document.getElementById('endCallBtn');
                if (endCallBtn) {
                   
                    endCallBtn.title = 'Leave the meeting';
                }

                // Show host transfer notification
                this.showHostTransferNotification(`${newHostName} is now the meeting host`, 'info');
            }

            // Update UI
            this.updateParticipantsList();
        });
        

    }

    async setupMediaDevices() {
        try {
            const constraints = {
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    frameRate: { ideal: 30 }
                },
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                    sampleRate: 44100,  
                    sampleSize: 16,     
                    channelCount: 2     
                }
            };

            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            const localVideo = document.getElementById('localVideo');
            if (localVideo) {
                localVideo.srcObject = this.localStream;
                // NEW: Apply enhancements to local video
                this.enhanceVideoElement(localVideo, true);
            }

            console.log("Media devices setup successfully");
        } catch (err) {
            console.error("Error accessing media devices:", err);
            this.updateConnectionStatus("Error accessing camera/microphone. Please check permissions.");
        }
    }

    handleRemoteStream(connectionId, stream) {
        console.log(`🎥 Handling remote stream for: ${connectionId}`);

        if (!connectionId || !stream) {
            console.error("❌ Missing connectionId or stream");
            return;
        }

        // Create audio context for better audio handling
        if (!this.audioContext) {
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (err) {
                console.warn("⚠️ AudioContext not supported:", err);
            }
        }

        const remoteVideos = document.getElementById('remoteVideos');
        if (!remoteVideos) {
            console.error('❌ Remote videos container not found');
            return;
        }

        // Remove existing video container for this connection first
        this.removeRemoteVideoContainer(connectionId);

        // Get participant info
        const participant = this.participants.get(connectionId);
        const participantName = participant ? participant.name : 'Unknown User';

        // Create new video container with enhanced structure
        const videoContainer = document.createElement('div');
        videoContainer.className = 'remote-video-container';
        videoContainer.id = `video-container-${connectionId}`;
        videoContainer.setAttribute('data-connection-id', connectionId);
        videoContainer.setAttribute('data-user-id', connectionId);
        videoContainer.setAttribute('data-peer-id', connectionId);

        // Enhanced styling for remote videos
        videoContainer.style.cssText = `
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        min-height: 200px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    `;

        // Create video element with enhanced attributes
        const video = document.createElement('video');
        video.id = `remote-video-${connectionId}`;
        video.className = `remote-video remote-video-${connectionId}`;
        video.autoplay = true;
        video.playsInline = true;
        video.muted = false;  // Enable audio
        video.srcObject = stream;
        video.setAttribute('data-connection-id', connectionId);

        video.style.cssText = `
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    `;

        // NEW: Apply enhancements to remote video (with mirror effect)
        this.enhanceVideoElement(video, false);

        // Create participant info overlay
        const infoOverlay = document.createElement('div');
        infoOverlay.className = 'participant-info-overlay';
        infoOverlay.style.cssText = `
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        z-index: 10;
    `;
        infoOverlay.textContent = participantName;

        // Create media status indicators
        const mediaStatus = document.createElement('div');
        mediaStatus.className = 'media-status';
        mediaStatus.style.cssText = `
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        z-index: 10;
    `;

        // Audio indicator
        const audioIndicator = document.createElement('div');
        audioIndicator.className = 'audio-indicator';
        audioIndicator.style.cssText = `
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 15px;
        font-size: 12px;
        backdrop-filter: blur(10px);
    `;
        audioIndicator.textContent = '🎤';

        // Video indicator
        const videoIndicator = document.createElement('div');
        videoIndicator.className = 'video-indicator';
        videoIndicator.style.cssText = `
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 8px;
        border-radius: 15px;
        font-size: 12px;
        backdrop-filter: blur(10px);
    `;
        videoIndicator.textContent = '📹';

        mediaStatus.appendChild(audioIndicator);
        mediaStatus.appendChild(videoIndicator);

        // Assemble the container
        videoContainer.appendChild(video);
        videoContainer.appendChild(infoOverlay);
        videoContainer.appendChild(mediaStatus);

        // Enhanced video event handlers
        video.onloadedmetadata = () => {
            console.log(`✅ Video metadata loaded for ${participantName} (${connectionId})`);

            // Log stream info
            const audioTracks = stream.getAudioTracks();
            const videoTracks = stream.getVideoTracks();
            console.log(`🎵 Audio tracks: ${audioTracks.length}, 🎥 Video tracks: ${videoTracks.length}`);

            // Handle audio playback
            if (audioTracks.length > 0) {
                video.play().catch(err => {
                    console.warn('⚠️ Auto-play failed, waiting for user interaction:', err);
                    this.handleAudioPlayback(video);
                });
            } else {
                video.play().catch(err => {
                    console.warn('⚠️ Video play failed:', err);
                });
            }
        };

        video.onplay = () => {
            console.log(`▶️ Video started playing for ${participantName}`);
            videoContainer.style.borderColor = '#10b981';
        };

        video.onerror = (err) => {
            console.error(`❌ Video error for ${participantName}:`, err);
            infoOverlay.textContent = `${participantName} (Error)`;
            videoContainer.style.borderColor = '#ef4444';
        };

        video.onended = () => {
            console.log(`⏹️ Video ended for ${participantName}`);
        };

        // Track video/audio status changes
        stream.getVideoTracks().forEach(track => {
            track.onended = () => {
                console.log(`📹 Video track ended for ${participantName}`);
                videoIndicator.textContent = '📷';
                videoIndicator.style.opacity = '0.5';
            };
        });

        stream.getAudioTracks().forEach(track => {
            track.onended = () => {
                console.log(`🎵 Audio track ended for ${participantName}`);
                audioIndicator.textContent = '🔇';
                audioIndicator.style.opacity = '0.5';
            };
        });

        // Add to DOM with animation
        videoContainer.style.opacity = '0';
        videoContainer.style.transform = 'scale(0.8)';
        remoteVideos.appendChild(videoContainer);

        // Animate in
        setTimeout(() => {
            videoContainer.style.transition = 'all 0.5s ease';
            videoContainer.style.opacity = '1';
            videoContainer.style.transform = 'scale(1)';
        }, 50);

        console.log(`✅ Remote video container created for ${participantName} (${connectionId})`);

        // Update grid layout
        this.updateVideoGrid();
    }

    // NEW: Remove video controls and customize right-click menu
    setupVideoControls(videoElement) {
        if (!videoElement) return;

        // Disable all browser video controls
        videoElement.setAttribute('disablepictureinpicture', '');
        videoElement.setAttribute('controlsList', 'nodownload nofullscreen noremoteplayback');
        videoElement.removeAttribute('controls');
        
        // Disable right-click context menu completely
        videoElement.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            this.showCustomVideoMenu(e, videoElement);
        });

        // Disable other video-related keyboard shortcuts
        videoElement.addEventListener('keydown', (e) => {
            // Disable common video control keys
            if (['Space', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'KeyF', 'KeyM'].includes(e.code)) {
                e.preventDefault();
            }
        });
    }

    // NEW: Show custom video menu with only expand option
    showCustomVideoMenu(event, videoElement) {
        // Remove any existing custom menu
        const existingMenu = document.querySelector('.custom-video-menu');
        if (existingMenu) existingMenu.remove();

        // Create custom menu
        const menu = document.createElement('div');
        menu.className = 'custom-video-menu';
        menu.style.cssText = `
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            z-index: 10000;
            padding: 8px 0;
            min-width: 150px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
        `;

        // Position menu at cursor
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';

        // Expand/Fullscreen option
        const expandOption = document.createElement('div');
        expandOption.style.cssText = `
            padding: 10px 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        expandOption.innerHTML = `
            <span style="font-size: 16px;">⛶</span>
            <span>Expand Video</span>
        `;

        expandOption.addEventListener('mouseenter', () => {
            expandOption.style.backgroundColor = '#f3f4f6';
        });

        expandOption.addEventListener('mouseleave', () => {
            expandOption.style.backgroundColor = 'transparent';
        });

        expandOption.addEventListener('click', () => {
            this.expandVideo(videoElement);
            menu.remove();
        });

        menu.appendChild(expandOption);
        document.body.appendChild(menu);

        // Close menu when clicking elsewhere
        const closeMenu = (e) => {
            if (!menu.contains(e.target)) {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            }
        };
        setTimeout(() => document.addEventListener('click', closeMenu), 100);
    }

    // NEW: Expand video to fullscreen
    expandVideo(videoElement) {
        if (!videoElement) return;

        if (videoElement.requestFullscreen) {
            videoElement.requestFullscreen();
        } else if (videoElement.webkitRequestFullscreen) {
            videoElement.webkitRequestFullscreen();
        } else if (videoElement.mozRequestFullScreen) {
            videoElement.mozRequestFullScreen();
        } else if (videoElement.msRequestFullscreen) {
            videoElement.msRequestFullscreen();
        }
    }

    // NEW: Apply mirror effect to video
    applyMirrorEffect(videoElement, isMirrored = true) {
        if (!videoElement) return;

        if (isMirrored) {
            videoElement.style.transform = 'scaleX(-1)';
            videoElement.setAttribute('data-mirrored', 'true');
        } else {
            videoElement.style.transform = 'scaleX(1)';
            videoElement.setAttribute('data-mirrored', 'false');
        }
    }

    // NEW: Toggle mirror effect
    toggleMirrorEffect(videoElement) {
        if (!videoElement) return;

        const isMirrored = videoElement.getAttribute('data-mirrored') === 'true';
        this.applyMirrorEffect(videoElement, !isMirrored);
    }

    // NEW: Setup all video enhancements
    enhanceVideoElement(videoElement, isLocal = false) {
        if (!videoElement) return;

        // Setup custom controls
        this.setupVideoControls(videoElement);

        // Apply mirror effect (especially for local video)
        if (isLocal) {
            this.applyMirrorEffect(videoElement, true);
        }

        // Add loading indicator
        videoElement.addEventListener('loadstart', () => {
            this.showVideoLoading(videoElement);
        });

        videoElement.addEventListener('loadeddata', () => {
            this.hideVideoLoading(videoElement);
        });
    }

    // NEW: Show loading indicator on video
    showVideoLoading(videoElement) {
        const container = videoElement.parentElement;
        if (!container) return;

        let loader = container.querySelector('.video-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'video-loader';
            loader.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0,0,0,0.7);
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                font-size: 14px;
                z-index: 100;
                display: flex;
                align-items: center;
                gap: 10px;
            `;
            loader.innerHTML = `
                <div style="width: 20px; height: 20px; border: 2px solid #fff; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <span>Loading video...</span>
            `;

            // Add spinner animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            if (!document.querySelector('#spinner-animation')) {
                style.id = 'spinner-animation';
                document.head.appendChild(style);
            }

            container.appendChild(loader);
        }
        loader.style.display = 'flex';
    }

    // NEW: Hide loading indicator
    hideVideoLoading(videoElement) {
        const container = videoElement.parentElement;
        if (!container) return;

        const loader = container.querySelector('.video-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    // Placeholder for other methods - add your existing methods here
    handleUserJoined(user) {
        // Implementation here
    }

    handleUserLeft(connectionId) {
        // Implementation here
    }

    updateConnectionStatus(message) {
        const statusElement = document.getElementById('connectionStatusMessage');
        if (statusElement) {
            statusElement.textContent = message;
        }
        console.log("Status:", message);
    }

    // Add all your other existing methods here...

} // End of MultiUserWebRTC class

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log("🚀 Initializing Multi-User WebRTC...");

    try {
        setTimeout(() => {
            if (typeof signalR === 'undefined') {
                console.error('SignalR library not loaded');
                return;
            }

            window.webRTC = new MultiUserWebRTC();
            console.log("✅ Multi-User WebRTC initialized successfully!");

        }, 1000);

    } catch (error) {
        console.error("❌ Failed to initialize WebRTC:", error);
        alert("Failed to initialize video conference. Please refresh the page.");
    }
});