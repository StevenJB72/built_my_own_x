'use strict';

document.addEventListener('DOMContentLoaded', () => {
    let timePara = document.getElementById("time");
    let distancePara = document.getElementById("distance");
    let pacePara = document.getElementById("pace");
    let activePara = distancePara; // Start with distance paragraph highlighted

    // Initialize display
    function initialize() {
        timePara.innerText = localStorage.getItem('timeValue') || '0';
        distancePara.innerText = localStorage.getItem('distanceValue') || '0';
        setActivePara(activePara);
        updatePace();
    }

    // Update the display of pace
    function updatePace() {
        const time = parseInt(timePara.innerText, 10);
        const distance = parseInt(distancePara.innerText, 10);
        if (distance >= 10 && time >= 5) {
            const pace = Math.round(time / (distance / 1000));
            pacePara.innerText = `${pace} mins/km`;
        } else {
            pacePara.innerText = '--';
        }
    }

    // Set active paragraph and add caret
    function setActivePara(para) {
        if (activePara) {
            activePara.innerText = activePara.innerText.replace('|', '');
        }
        activePara = para;
        if (!activePara.innerText.includes('|')) {
            activePara.innerText += '|';
        }
        updatePace();
    }

    document.querySelectorAll('.cal_button, .diff_button').forEach(button => {
        button.addEventListener('click', (e) => {
            let currentVal = activePara.innerText.replace('|', ''); // Remove the caret for processing
            const buttonVal = e.target.innerText;
    
            if (buttonVal === 'C') {
                currentVal = '0'; // Reset to zero on 'C'
            } else if (buttonVal === '⇦') {
                if (currentVal.length === 1 || currentVal === '0') { // If only one digit or already at zero, reset to zero
                    currentVal = '0';
                } else {
                    currentVal = currentVal.slice(0, -1); // Remove the last digit
                }
            } else if (!isNaN(buttonVal)) { // Check if the button pressed is a number
                if (currentVal === '0') { // Replace the initial zero with the new number
                    currentVal = buttonVal;
                } else if ((activePara === timePara && currentVal.length < 3) || 
                           (activePara === distancePara && currentVal.length < 5)) {
                    currentVal += buttonVal; // Append the new digit
                }
            }
    
            activePara.innerText = currentVal + '|'; // Add the caret back for display
            updatePace();
        });
    });
    // Toggle active input on paragraph click
    [timePara, distancePara].forEach(para => {
        para.addEventListener('click', () => setActivePara(para));
    });
    const r0f = x => {
        let f = Math.round(x);
        return (f >= 0 ? "+" : "-") + ("00" + Math.abs(f)).slice(-3);
    };

    function log_o(msg) {
        const p_o = document.getElementById("onscreenconsole");
        if (p_o) {
            p_o.innerHTML = msg;
        }
    }

    const handleDeviceOrientation = function(event) {
        let b = event.beta; // Front-to-back tilt, used to calculate incline

        // Check if the device is tilted more than 50 degrees in either direction
        if (b < -50 || b > 50) {
            // Display the notice for laying the device flat
            log_o("<p>Please lay the device flat on the ground.</p>");
        } else {
            // Proceed with your existing incline calculation and display logic
            let inclineRadians = b * Math.PI / 180;
            let inclinePercent = Math.tan(inclineRadians) * 100;
            let inclineDirection = inclinePercent > 0 ? "Uphill" : inclinePercent < 0 ? "Downhill" : "Flat";

            log_o("<p>◬ Incline : " + inclinePercent.toFixed(1) + "% " +
                inclineDirection + " (" + b.toFixed(1) + "°) ◬</p>");
        }
    };



    const init = function() {
        displayInclinePrompt(); // Call this function to display the initial prompt

        if (window.DeviceOrientationEvent) {
            if (typeof DeviceOrientationEvent.requestPermission === "function") { // iOS 13+ devices
                document.getElementById("onscreenconsole").addEventListener("click", () => {
                    DeviceOrientationEvent.requestPermission()
                        .then(response => {
                            if (response === "granted") {
                                activateInclineFeature();
                            } else {
                                log_o("Permission needed to access incline data.");
                            }
                        })
                        .catch(() => log_o("Device orientation not supported or permission denied."));
                });
            } else { // Non-iOS 13+ devices
                document.getElementById("onscreenconsole").addEventListener("click", activateInclineFeature);
            }
        } else {
            log_o("DeviceOrientationEvent not supported on your device.");
        }
    };

    function activateInclineFeature() {
        window.addEventListener("deviceorientation", handleDeviceOrientation);
        // Remove the event listener after 30 seconds and display the initial prompt again
        setTimeout(() => {
            window.removeEventListener("deviceorientation", handleDeviceOrientation);
            displayInclinePrompt();
        }, 30000); // 30 seconds
    }

    function displayInclinePrompt() {
        log_o("◬ Tap to show incline ◬");
    }

    window.addEventListener("DOMContentLoaded", init);

    // Save inputs on page unload
    window.addEventListener('beforeunload', () => {
        localStorage.setItem('timeValue', timePara.innerText.replace('|', ''));
        localStorage.setItem('distanceValue', distancePara.innerText.replace('|', ''));
    });

    const startStopBtn = document.getElementById('startStopBtn');
    const liveDistance = document.getElementById('liveDistance');
    const averagePace = document.getElementById('averagePace');

    let watching = false;
    let watchId = null;
    let startPosition = null;
    let lastPosition = null;
    let totalDistance = 0;
    let startTime = null;

    startStopBtn.addEventListener('click', () => {
        if (!watching) {
            startTracking();
        } else {
            stopTracking();
        }
    });

    function startTracking() {
        watching = true;
        startStopBtn.innerText = 'Stop';
        startPosition = null;
        lastPosition = null;
        totalDistance = 0;
        startTime = new Date();

        const MOVEMENT_THRESHOLD = 0.01; // meters

        if (navigator.geolocation) {
            watchId = navigator.geolocation.watchPosition(position => {
                if (!startPosition) {
                    startPosition = position;
                } else {
                    const distance = calculateDistance(lastPosition || startPosition, position);
                    totalDistance += distance;
                    const elapsedTime = (new Date() - startTime) / 1000 / 60; // Convert to minutes
                    const pace = elapsedTime / (totalDistance / 1000); // mins/km, for pace conversion to km is needed
                    liveDistance.innerText = `Live Distance: ${totalDistance.toFixed(2)} m`;
                    averagePace.innerText = `Average Pace: ${pace.toFixed(2)} mins/km`;
                }
                lastPosition = position;

                if (startPosition && lastPosition) {
                    const distance = calculateDistance(lastPosition, position);
                    if (distance > MOVEMENT_THRESHOLD) {
                        totalDistance += distance;
                        lastPosition = position; // Update lastPosition for the next calculation
                    }
                } else if (!startPosition) {
                    startPosition = position;
                    lastPosition = position;
                }
            }, error => {
                console.error(error);
            }, {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 1
            });
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
    }


    function stopTracking() {
        watching = false;
        startStopBtn.innerText = 'Start';
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
        }
    }

    function calculateDistance(start, current) {
        const toRadians = degree => degree * Math.PI / 180;
        const R = 6371e3; // meters
        const φ1 = toRadians(start.coords.latitude);
        const φ2 = toRadians(current.coords.latitude);
        const Δφ = toRadians(current.coords.latitude - start.coords.latitude);
        const Δλ = toRadians(current.coords.longitude - start.coords.longitude);

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        const distance = R * c; // in meters
        return distance; // Keep distance in meters
    }

    // Initialize fields on page load
    initialize();
});