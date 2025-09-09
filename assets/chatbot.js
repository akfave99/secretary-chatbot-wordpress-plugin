jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize chatbot
    var SecretaryChatbot = {

        // User preferences
        userPreferences: {
            course: null,
            answerLength: null,
            isSetup: false
        },

        init: function() {
            this.bindEvents();
            this.loadUserPreferences();
            this.addWelcomeMessage();
        },
        
        bindEvents: function() {
            // Chat icon click
            $(document).on('click', '.secretary-chat-icon', this.openChat);

            // Close button click
            $(document).on('click', '.secretary-close-btn', this.closeChat);

            // Send button click
            $(document).on('click', '.secretary-send-btn', this.sendMessage);

            // Enter key press
            $(document).on('keypress', '.secretary-input-container input', function(e) {
                if (e.which === 13) {
                    SecretaryChatbot.sendMessage();
                }
            });

            // Course selection buttons
            $(document).on('click', '.secretary-selection-btn', this.handleCourseSelection);

            // Preference selection buttons
            $(document).on('click', '.secretary-preference-btn', this.handlePreferenceSelection);
        },
        
        openChat: function() {
            $('.secretary-chat-window').removeClass('hidden');
            $('.secretary-input-container input').focus();
        },
        
        closeChat: function() {
            $('.secretary-chat-window').addClass('hidden');
        },
        
        sendMessage: function() {
            var $input = $('.secretary-input-container input');
            var message = $input.val().trim();
            
            if (!message) {
                return;
            }
            
            // Clear input
            $input.val('');
            
            // Add user message
            SecretaryChatbot.addMessage(message, 'user');
            
            // Show typing indicator
            SecretaryChatbot.showTyping();
            
            // Try REST API first, fallback to AJAX
            SecretaryChatbot.sendToAPI(message);
        },

        sendToAPI: function(message) {
            var self = this;

            // First try REST API
            $.ajax({
                url: secretary_chatbot_ajax.rest_url + 'secretary/v1/chat/message',
                type: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', secretary_chatbot_ajax.nonce);
                },
                data: JSON.stringify({
                    message: message,
                    course: self.userPreferences.course || 'us',
                    answer_length: self.userPreferences.answerLength || 'long'
                }),
                contentType: 'application/json',
                success: function(response) {
                    self.hideTyping();

                    if (response && response.response) {
                        self.addMessage(response.response, 'bot');
                        self.speakText(response.response);
                    } else {
                        self.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('REST API failed, trying AJAX fallback...', xhr.status, error);

                    // Fallback to AJAX
                    self.sendToAJAX(message);
                }
            });
        },

        sendToAJAX: function(message) {
            var self = this;

            $.ajax({
                url: secretary_chatbot_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'secretary_chatbot_message',
                    message: message,
                    course: self.userPreferences.course || 'us',
                    answer_length: self.userPreferences.answerLength || 'long',
                    nonce: secretary_chatbot_ajax.nonce
                },
                success: function(response) {
                    self.hideTyping();

                    if (response.success && response.data && response.data.response) {
                        self.addMessage(response.data.response, 'bot');
                        self.speakText(response.data.response);
                    } else {
                        self.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                    }
                },
                error: function(xhr, status, error) {
                    self.hideTyping();
                    console.log('Both REST API and AJAX failed, using fallback response:', error);

                    // Use local fallback response instead of error message
                    var fallbackResponse = self.generateFallbackResponse(message);
                    self.addMessage(fallbackResponse, 'bot');
                }
            });
        },

        generateFallbackResponse: function(message) {
            var text = message.toLowerCase();

            // Government concepts responses
            if (text.includes('federalism')) {
                return 'Federalism divides power between federal and state governments, allowing local control while maintaining national unity!';
            }
            if (text.includes('democracy')) {
                return 'Democracy is a system where citizens participate in government through voting and representation!';
            }
            if (text.includes('constitution')) {
                return 'The Constitution is the supreme law that establishes our government structure and protects individual rights!';
            }
            if (text.includes('congress')) {
                return 'Congress is the legislative branch, consisting of the House of Representatives and Senate, responsible for making laws!';
            }
            if (text.includes('president')) {
                return 'The President leads the executive branch, enforcing laws and serving as Commander-in-Chief!';
            }
            if (text.includes('supreme court')) {
                return 'The Supreme Court is the highest judicial authority, interpreting the Constitution and ensuring justice!';
            }
            if (text.includes('bill of rights')) {
                return 'The Bill of Rights protects fundamental freedoms like speech, religion, and due process!';
            }
            if (text.includes('separation of powers')) {
                return 'Separation of powers divides government into legislative, executive, and judicial branches to prevent tyranny!';
            }
            if (text.includes('checks and balances')) {
                return 'Checks and balances ensure each branch of government can limit the others\' power!';
            }
            if (text.includes('civil rights')) {
                return 'Civil rights guarantee equal treatment and protection under the law for all citizens!';
            }

            // Texas-specific responses
            if (text.includes('texas')) {
                return 'Texas has a unique political culture emphasizing individualism, traditionalism, and limited government!';
            }

            // Default responses
            var defaultResponses = [
                'That\'s an interesting question about government! Let me help you understand the key concepts.',
                'Government involves the institutions and processes by which societies are governed. What specific aspect interests you?',
                'Understanding government helps us be better citizens. What would you like to explore further?',
                'Political science covers many fascinating topics. Can you be more specific about what you\'d like to learn?'
            ];

            return defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
        },

        addMessage: function(text, sender) {
            var $messages = $('.secretary-messages');
            var avatarSrc = sender === 'user' ?
                secretary_chatbot_ajax.plugin_url + 'assets/user.png' :
                secretary_chatbot_ajax.plugin_url + 'assets/badger.png';

            var messageHtml = '<div class="secretary-response ' + sender + '">' +
                '<img src="' + avatarSrc + '" class="secretary-avatar" alt="Avatar">' +
                '<div class="secretary-message-text">' + this.escapeHtml(text) + '</div>' +
                '</div>';

            $messages.append(messageHtml);
            this.scrollToBottom();
        },
        
        showTyping: function() {
            var $messages = $('.secretary-messages');
            var avatarSrc = secretary_chatbot_ajax.plugin_url + 'assets/badger.png';

            var typingHtml = '<div class="secretary-response bot secretary-typing-indicator">' +
                '<img src="' + avatarSrc + '" class="secretary-avatar" alt="Avatar">' +
                '<div class="secretary-message-text secretary-typing">Typing...</div>' +
                '</div>';

            $messages.append(typingHtml);
            this.scrollToBottom();
        },
        
        hideTyping: function() {
            $('.secretary-typing-indicator').remove();
        },
        
        addWelcomeMessage: function() {
            var self = this;
            setTimeout(function() {
                if (!self.userPreferences.isSetup) {
                    self.showCourseSelection();
                } else {
                    var courseName = self.userPreferences.course === 'texas' ? 'Texas Government' : 'US Government';
                    var lengthPref = self.userPreferences.answerLength === 'short' ? 'concise' : 'detailed';
                    self.addMessage(`Hello! I'm the Secretary of DeCourse 🦡. I'm ready to help you with ${courseName} using ${lengthPref} answers. What would you like to learn about?`, 'bot');
                }
            }, 1000);
        },

        loadUserPreferences: function() {
            var saved = localStorage.getItem('secretary_preferences');
            if (saved) {
                this.userPreferences = JSON.parse(saved);
            }
        },

        saveUserPreferences: function() {
            localStorage.setItem('secretary_preferences', JSON.stringify(this.userPreferences));
        },

        showCourseSelection: function() {
            var courseSelectionHtml = `
                <div class="secretary-course-selection">
                    <h4>🎓 Welcome to Secretary of DeCourse!</h4>
                    <p>What course are you taking?</p>
                    <div class="secretary-selection-buttons">
                        <button class="secretary-selection-btn" data-course="texas">
                            <span class="secretary-course-icon">🏛️</span>Texas Government
                        </button>
                        <button class="secretary-selection-btn" data-course="us">
                            <span class="secretary-course-icon">🇺🇸</span>US Government
                        </button>
                    </div>
                </div>
            `;
            this.addCustomMessage(courseSelectionHtml, 'bot', 'secretary-welcome-setup');
        },

        showPreferenceSelection: function() {
            var preferenceHtml = `
                <div class="secretary-preference-selection">
                    <h4>📝 How do you prefer your answers?</h4>
                    <div class="secretary-preference-buttons">
                        <button class="secretary-preference-btn" data-preference="short">
                            📋 Short & Concise
                        </button>
                        <button class="secretary-preference-btn" data-preference="long">
                            📚 Detailed & Comprehensive
                        </button>
                    </div>
                </div>
            `;
            this.addCustomMessage(preferenceHtml, 'bot');
        },

        handleCourseSelection: function(e) {
            var course = $(e.target).closest('.secretary-selection-btn').data('course');
            SecretaryChatbot.userPreferences.course = course;

            // Update header
            var courseName = course === 'texas' ? 'Texas Government' : 'US Government';
            $('.secretary-course-subtitle').text(courseName + ' Assistant');

            // Update placeholder
            $('.secretary-input-container input').attr('placeholder', 'Ask me about ' + courseName + '...');

            // Remove course selection and show preference selection
            $('.secretary-course-selection').fadeOut(300, function() {
                $(this).remove();
                SecretaryChatbot.showPreferenceSelection();
            });
        },

        handlePreferenceSelection: function(e) {
            var preference = $(e.target).closest('.secretary-preference-btn').data('preference');
            SecretaryChatbot.userPreferences.answerLength = preference;
            SecretaryChatbot.userPreferences.isSetup = true;
            SecretaryChatbot.saveUserPreferences();

            // Remove preference selection
            $('.secretary-preference-selection').fadeOut(300, function() {
                $(this).remove();
            });

            // Show completion message
            var courseName = SecretaryChatbot.userPreferences.course === 'texas' ? 'Texas Government' : 'US Government';
            var lengthPref = preference === 'short' ? 'concise' : 'detailed';

            setTimeout(function() {
                SecretaryChatbot.addMessage(`Perfect! I'm now configured for ${courseName} with ${lengthPref} answers. What would you like to learn about?`, 'bot');
            }, 500);
        },

        addCustomMessage: function(content, sender, extraClass) {
            var messageClass = 'secretary-message secretary-' + sender + '-message';
            if (extraClass) {
                messageClass += ' ' + extraClass;
            }

            var messageHtml = '<div class="' + messageClass + '">' +
                '<div class="secretary-message-content">' + content + '</div>' +
                '</div>';

            $('.secretary-messages').append(messageHtml);
            this.scrollToBottom();
        },
        
        scrollToBottom: function() {
            var $messages = $('.secretary-messages');
            $messages.scrollTop($messages[0].scrollHeight);
        },
        
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        },
        
        speakText: function(text) {
            // Text-to-speech functionality
            if ('speechSynthesis' in window) {
                var utterance = new SpeechSynthesisUtterance(text);
                utterance.rate = 0.8;
                utterance.pitch = 1;
                utterance.volume = 0.5;
                
                // Try to use a more natural voice
                var voices = speechSynthesis.getVoices();
                var preferredVoice = voices.find(function(voice) {
                    return voice.name.includes('Female') || voice.name.includes('Samantha') || voice.name.includes('Karen');
                });
                
                if (preferredVoice) {
                    utterance.voice = preferredVoice;
                }
                
                speechSynthesis.speak(utterance);
            }
        }
    };
    
    // Initialize when DOM is ready
    SecretaryChatbot.init();
    
    // Handle voice loading
    if ('speechSynthesis' in window) {
        speechSynthesis.onvoiceschanged = function() {
            // Voices are now loaded
        };
    }
});
