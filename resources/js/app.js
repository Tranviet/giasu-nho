// Child-Friendly Interactivity & Touch Feedback for "Gia Su Nho"

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Web Audio Synthesizer (Instant tactile feedback without external audio files)
    // ----------------------------------------------------
    let soundEnabled = localStorage.getItem('child_sound_enabled') !== 'false';
    const soundToggleBtn = document.getElementById('btn-sound-toggle');
    const soundIcon = document.getElementById('sound-icon');

    function updateSoundUI() {
        if (soundIcon) {
            soundIcon.textContent = soundEnabled ? '🔊' : '🔇';
        }
    }
    updateSoundUI();

    if (soundToggleBtn) {
        soundToggleBtn.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            localStorage.setItem('child_sound_enabled', soundEnabled ? 'true' : 'false');
            updateSoundUI();
            if (soundEnabled) playChime(523.25, 'triangle'); // C5 tone
        });
    }

    // Gentle synthesizer for buttons & rewards
    function playChime(freq = 587.33, type = 'sine') {
        if (!soundEnabled) return;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = type;
            osc.frequency.setValueAtTime(freq, ctx.currentTime);
            // Quick cheerful bell decay
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start();
            osc.stop(ctx.currentTime + 0.36);
        } catch (e) {
            // Ignore audio context auto-play restrictions before user gesture
        }
    }

    // Attach chime to primary interactive buttons
    document.querySelectorAll('.btn-touch-primary, .btn-touch-pill, .nav-touch-item').forEach(el => {
        el.addEventListener('pointerdown', () => {
            playChime(659.25, 'sine'); // E5 pleasant pop
        });
    });

    // ----------------------------------------------------
    // 2. AI Tutor Interactive Dialog Sheet
    // ----------------------------------------------------
    const aiModal = document.getElementById('ai-modal-backdrop');
    const btnCloseAi = document.getElementById('btn-close-ai-modal');
    const btnTriggerHomework = document.getElementById('btn-trigger-homework-check');
    const btnTriggerQa = document.getElementById('btn-trigger-ai-qa');
    const btnNavAiTutor = document.getElementById('btn-nav-ai-tutor');

    const tabVoice = document.getElementById('tab-mode-voice');
    const tabPhoto = document.getElementById('tab-mode-photo');
    const viewVoice = document.getElementById('view-mode-voice');
    const viewPhoto = document.getElementById('view-mode-photo');

    function openAiModal(mode = 'voice') {
        if (!aiModal) return;
        aiModal.classList.remove('hidden');
        aiModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setAiMode(mode);
        playChime(783.99, 'triangle'); // G5 chime
    }

    function closeAiModal() {
        if (!aiModal) return;
        aiModal.classList.add('hidden');
        aiModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function setAiMode(mode) {
        if (mode === 'voice') {
            viewVoice?.classList.remove('hidden');
            viewPhoto?.classList.add('hidden');
            tabVoice?.classList.remove('bg-slate-100', 'text-slate-700', 'border-slate-200');
            tabVoice?.classList.add('bg-purple-600', 'text-white', 'border-purple-700');

            tabPhoto?.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200');
            tabPhoto?.classList.remove('bg-purple-600', 'text-white', 'border-purple-700');
        } else {
            viewPhoto?.classList.remove('hidden');
            viewVoice?.classList.add('hidden');
            tabPhoto?.classList.remove('bg-slate-100', 'text-slate-700', 'border-slate-200');
            tabPhoto?.classList.add('bg-purple-600', 'text-white', 'border-purple-700');

            tabVoice?.classList.add('bg-slate-100', 'text-slate-700', 'border-slate-200');
            tabVoice?.classList.remove('bg-purple-600', 'text-white', 'border-purple-700');
        }
    }

    btnTriggerHomework?.addEventListener('click', () => openAiModal('photo'));
    btnTriggerQa?.addEventListener('click', () => openAiModal('voice'));
    btnNavAiTutor?.addEventListener('click', () => openAiModal('voice'));
    btnCloseAi?.addEventListener('click', closeAiModal);

    // Close when tapping backdrop
    aiModal?.addEventListener('click', (e) => {
        if (e.target === aiModal) closeAiModal();
    });

    tabVoice?.addEventListener('click', () => setAiMode('voice'));
    tabPhoto?.addEventListener('click', () => setAiMode('photo'));

    // Microphone interactive recording simulation / Web Speech API
    const btnRecordVoice = document.getElementById('btn-record-voice');
    const voiceStatusText = document.getElementById('voice-status-text');
    const aiResponseBox = document.getElementById('ai-response-box');
    const aiResponseText = document.getElementById('ai-response-text');

    let isRecording = false;

    btnRecordVoice?.addEventListener('click', () => {
        playChime(880, 'sine'); // A5
        if (!isRecording) {
            isRecording = true;
            btnRecordVoice.classList.add('animate-pulse', 'scale-110');
            if (voiceStatusText) voiceStatusText.textContent = 'Đang lắng nghe bé nói... Hãy nói bài con thắc mắc nhé!';
            
            // Check browser Web Speech API
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                const recognition = new SpeechRecognition();
                recognition.lang = 'vi-VN';
                recognition.start();

                recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    handleChildQuestion(transcript);
                };

                recognition.onerror = () => {
                    simulateVoiceAnswer('Con vừa hỏi bài toán cộng có nhớ đúng không nào?');
                };

                recognition.onend = () => {
                    isRecording = false;
                    btnRecordVoice.classList.remove('animate-pulse', 'scale-110');
                };
            } else {
                // Fallback simulation after 2 seconds
                setTimeout(() => {
                    isRecording = false;
                    btnRecordVoice.classList.remove('animate-pulse', 'scale-110');
                    simulateVoiceAnswer('Chào con! Gia sư đã nghe thấy rồi. Để cộng 27 + 35, con nhớ đặt tính thẳng cột nhé!');
                }, 1800);
            }
        } else {
            isRecording = false;
            btnRecordVoice.classList.remove('animate-pulse', 'scale-110');
            if (voiceStatusText) voiceStatusText.textContent = 'Chạm vào chiếc micro để nói với Gia Sư nhé!';
        }
    });

    function handleChildQuestion(question) {
        if (voiceStatusText) voiceStatusText.textContent = `Bé đã hỏi: "${question}"`;
        simulateVoiceAnswer(`Gia sư trả lời cho bé: Về câu hỏi "${question}", con làm rất đúng hướng rồi đấy! Hãy cùng luyện tập thêm câu tiếp theo nhé!`);
    }

    function simulateVoiceAnswer(answer) {
        if (aiResponseBox && aiResponseText) {
            aiResponseBox.classList.remove('hidden');
            aiResponseText.textContent = answer;
            playChime(1046.50, 'triangle'); // High C6 reward chime
        }

        // Optional Web Speech TTS
        if ('speechSynthesis' in window && soundEnabled) {
            try {
                const utterance = new SpeechSynthesisUtterance(answer);
                utterance.lang = 'vi-VN';
                utterance.rate = 0.95; // Slightly slower for children
                window.speechSynthesis.speak(utterance);
            } catch (e) {}
        }
    }

    // Quick suggestion chips
    document.querySelectorAll('.quick-question-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const q = chip.getAttribute('data-question');
            openAiModal('voice');
            setTimeout(() => {
                handleChildQuestion(q);
            }, 300);
        });
    });

    // Photo input handling
    const homeworkInput = document.getElementById('homework-image-input');
    const btnSelectPhoto = document.getElementById('btn-select-photo');
    const photoPreviewBox = document.getElementById('photo-preview-box');
    const photoPreviewImg = document.getElementById('photo-preview-img');

    btnSelectPhoto?.addEventListener('click', () => {
        homeworkInput?.click();
    });

    homeworkInput?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                if (photoPreviewImg && photoPreviewBox) {
                    photoPreviewImg.src = event.target.result;
                    photoPreviewBox.classList.remove('hidden');
                    photoPreviewBox.classList.add('flex');
                    playChime(880, 'triangle');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Continue lesson button
    const btnContinueLesson = document.getElementById('btn-continue-lesson');
    btnContinueLesson?.addEventListener('click', () => {
        playChime(659.25, 'triangle');
        const subjectsEl = document.getElementById('subjects-section');
        subjectsEl?.scrollIntoView({ behavior: 'smooth' });
    });
});
