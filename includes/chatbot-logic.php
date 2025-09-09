<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function process_chatbot_message($message, $course = 'us', $answer_length = 'long') {
    // Log the incoming request for debugging
    if (function_exists('error_log')) {
        error_log("Secretary Chatbot: Processing message - '$message' (course: $course, length: $answer_length)");
    }

    $message = strtolower(trim($message));

    // Remove non-word/space chars and clean up
    $text = preg_replace('/[^\w\s]/i', '', $message);
    $text = preg_replace('/[\d]/', '', $text);
    $text = trim($text);

    // Text replacements
    $text = str_replace(' a ', ' ', $text);
    $text = str_replace('i feel ', '', $text);
    $text = str_replace('whats', 'what is', $text);
    $text = str_replace('please ', '', $text);
    $text = str_replace(' please', '', $text);
    $text = str_replace('r u', 'are you', $text);

    // Get prompts and replies based on course
    $prompts = get_chatbot_prompts($course);
    $replies = get_chatbot_replies($course, $answer_length);

    // Check for exact matches
    $response = find_exact_match($prompts, $replies, $text);
    if ($response) {
        return format_response($response, $answer_length);
    }

    // Check for thank you
    if (preg_match('/thank/i', $text)) {
        return "You're welcome! Happy to help with your " . ($course === 'texas' ? 'Texas' : 'US') . " government studies!";
    }

    // Check for course-specific terms
    if ($course === 'texas' && preg_match('/(texas|lone star|austin|legislature|governor)/i', $text)) {
        $texas_responses = get_texas_responses($answer_length);
        return $texas_responses[array_rand($texas_responses)];
    }

    // Check for general government-related terms
    if (preg_match('/(government|politics|civic|democracy|america|constitution)/i', $text)) {
        $government_responses = get_government_responses($course, $answer_length);
        return $government_responses[array_rand($government_responses)];
    }

    // Default responses
    $alternatives = get_alternative_responses($course);
    return $alternatives[array_rand($alternatives)];
}

function format_response($response, $answer_length) {
    if ($answer_length === 'short') {
        // For short answers, take first sentence or up to 100 characters
        $sentences = preg_split('/[.!?]+/', $response);
        $first_sentence = trim($sentences[0]);
        if (strlen($first_sentence) > 100) {
            return substr($first_sentence, 0, 97) . '...';
        }
        return $first_sentence . '.';
    }
    return $response;
}

function find_exact_match($prompts, $replies, $text) {
    for ($x = 0; $x < count($prompts); $x++) {
        for ($y = 0; $y < count($prompts[$x]); $y++) {
            if ($prompts[$x][$y] === $text) {
                $reply_options = $replies[$x];
                return $reply_options[array_rand($reply_options)];
            }
        }
    }
    return false;
}

function get_chatbot_prompts($course = 'us') {
    $common_prompts = array(
        array("hi", "hey", "hello", "good morning", "good afternoon"),
        array("how are you", "how is life", "how are things"),
        array("what are you doing", "what is going on", "what is up"),
        array("how old are you"),
        array("who are you", "are you human", "are you bot", "are you human or bot"),
        array("who created you", "who made you"),
        array("your name please", "your name", "may i know your name", "what is your name", "what call yourself"),
        array("i love you"),
        array("happy", "good", "fun", "wonderful", "fantastic", "cool"),
        array("bad", "bored", "tired"),
        array("help me", "tell me story", "tell me joke"),
        array("ah", "yes", "ok", "okay", "nice"),
        array("bye", "good bye", "goodbye", "see you later"),
        array("what should i eat today"),
        array("bro"),
        array("what", "why", "how", "where", "when"),
        array("no", "not sure", "maybe", "no thanks"),
        array(""),
        array("haha", "ha", "lol", "hehe", "funny", "joke"),
        array("constitution", "what is the constitution"),
        array("bill of rights", "first amendment", "amendments"),
        array("federalism", "federal government", "state government"),
        array("democracy", "republic", "government"),
        array("voting", "elections", "electoral college"),
        array("civil rights", "civil liberties", "freedom")
    );

    if ($course === 'texas') {
        return array_merge($common_prompts, array(
            array("texas government", "texas constitution", "lone star state"),
            array("texas legislature", "texas house", "texas senate"),
            array("texas governor", "governor of texas"),
            array("texas political culture", "texas politics"),
            array("texas history", "republic of texas"),
            array("austin", "texas capital", "state capital")
        ));
    } else {
        return array_merge($common_prompts, array(
            array("congress", "house", "senate", "legislative branch"),
            array("president", "executive branch", "white house"),
            array("supreme court", "judicial branch", "courts"),
            array("political parties", "democrats", "republicans")
        ));
    }
}

function get_chatbot_replies($course = 'us', $answer_length = 'long') {
    return array(
        array("Hello! I'm the Secretary of DeCourse!", "Hi there! Ready to learn about American Government?", "Hey! Ask me anything about civics!", "Hi! I'm here to help with your government studies!"),
        array("I'm doing great, ready to teach! How are you?", "Pretty well, excited to discuss government! How are you?", "Fantastic, love talking about democracy! How are you?"),
        array("Teaching about American Government!", "Helping students learn about democracy", "Discussing the Constitution and civil rights", "Explaining how our government works"),
        array("I've been around since the Constitution was written!"),
        array("I'm the Secretary of DeCourse, your American Government teaching assistant!", "I'm a bot designed to help you learn about civics and government!"),
        array("I was created to help students understand American Government"),
        array("I'm the Secretary of DeCourse", "You can call me Secretary", "I'm your government studies assistant"),
        array("I love teaching about democracy too!", "That's the spirit of civic engagement!"),
        array("Learning about government is exciting!", "Democracy is wonderful!", "Civic engagement is fantastic!"),
        array("Why not study some civics?", "Try learning about the Constitution!", "Maybe read about civil rights?"),
        array("I can help with government topics!", "Let me tell you about the three branches of government!", "Want to learn about the Bill of Rights?"),
        array("Great! Ready to learn more?", "Excellent! What government topic interests you?", "Perfect! Let's dive into civics!"),
        array("Goodbye! Keep being a good citizen!", "See you later! Remember to vote!", "Bye! Stay engaged in democracy!"),
        array("How about some knowledge about government?", "Try learning something new about civics!"),
        array("Fellow citizen!"),
        array("Excellent question about government!", "That's a great civics question!"),
        array("That's okay! What would you like to know about government?", "I understand! Any government topics you're curious about?", "What government topic can I help with?"),
        array("Please ask me about American Government! :)"),
        array("Glad you're enjoying learning about government!", "Democracy can be fun!", "Civic education is important!"),
        array("The Constitution is the supreme law of the land, establishing our government structure and protecting individual rights!"),
        array("The Bill of Rights protects fundamental freedoms like speech, religion, and press. The First Amendment is especially important for democracy!"),
        array("Congress is our legislative branch, made up of the House and Senate. They make federal laws and represent the people!"),
        array("The President leads the executive branch, enforcing laws and serving as Commander in Chief. They're elected every four years!"),
        array("The Supreme Court heads the judicial branch, interpreting laws and protecting constitutional rights. They serve for life!"),
        array("Federalism divides power between federal and state governments, allowing local control while maintaining national unity!"),
        array("We live in a democratic republic where citizens elect representatives to make decisions. It's government by and for the people!"),
        array("Voting is a fundamental right and responsibility! The Electoral College system elects our President based on state representation."),
        array("Political parties help organize government and give voters choices. The two-party system has dominated American politics!"),
        array("Civil rights ensure equal treatment under law, while civil liberties protect individual freedoms from government interference!")
    );
}

function get_alternative_responses($course = 'us') {
    $course_name = ($course === 'texas') ? 'Texas Government' : 'US Government';
    return array(
        "That's interesting! Can I help you with any $course_name topics?",
        "Tell me more... or ask about $course_name!",
        "I'm here to help with civics and government questions!",
        "Try asking about the Constitution, Congress, or civil rights!",
        "I'm listening... what would you like to know about government?",
        "I don't understand, but I'd love to help with $course_name topics! :/"
    );
}

function get_government_responses($course = 'us', $answer_length = 'long') {
    $course_name = ($course === 'texas') ? 'Texas Government' : 'US Government';

    if ($course === 'texas') {
        return get_texas_responses($answer_length);
    }

    $responses = array(
        "Remember, civic engagement is important for democracy!",
        "The Constitution protects your rights as a citizen!",
        "Voting is both a right and a responsibility!",
        "Our three branches of government provide checks and balances!"
    );

    return $responses;
}

function get_texas_responses($answer_length = 'long') {
    if ($answer_length === 'short') {
        return array(
            "Texas has a unique political culture!",
            "The Texas Constitution is longer than the US Constitution.",
            "Texas has a plural executive system.",
            "The Texas Legislature meets biennially."
        );
    }

    return array(
        "Texas has a distinctive political culture shaped by traditionalism, individualism, and moralism. This influences how Texans view government's role in society.",
        "The Texas Constitution, adopted in 1876, is much longer and more detailed than the US Constitution. It reflects the state's distrust of centralized government power.",
        "Texas uses a plural executive system where executive power is divided among several elected officials, including the Governor, Lieutenant Governor, and Attorney General.",
        "The Texas Legislature meets in regular session every two years for 140 days, reflecting the state's preference for limited government involvement.",
        "Texas political culture emphasizes individual responsibility, limited government, and traditional values, which shapes policy decisions across the state."
    );
}

function get_constitution_responses($course, $answer_length) {
    if ($course === 'texas') {
        if ($answer_length === 'short') {
            return array("The Texas Constitution was adopted in 1876 and is much longer than the US Constitution.");
        }
        return array("The Texas Constitution, adopted in 1876, reflects the state's distrust of government power. It's one of the longest state constitutions, with detailed restrictions on government authority and frequent amendments.");
    }

    if ($answer_length === 'short') {
        return array("The Constitution is the supreme law establishing our government structure.");
    }
    return array("The Constitution is the supreme law of the land, establishing our government structure and protecting individual rights through a system of checks and balances!");
}

function get_bill_of_rights_responses($answer_length) {
    if ($answer_length === 'short') {
        return array("The Bill of Rights protects fundamental freedoms like speech and religion.");
    }
    return array("The Bill of Rights protects fundamental freedoms like speech, religion, and press. The First Amendment is especially important for democracy!");
}

function get_federalism_responses($course, $answer_length) {
    if ($course === 'texas') {
        if ($answer_length === 'short') {
            return array("Texas maintains significant autonomy within the federal system.");
        }
        return array("Texas federalism involves the relationship between state and federal government. Texas has historically emphasized states' rights and local control over many policy areas.");
    }

    if ($answer_length === 'short') {
        return array("Federalism divides power between federal and state governments.");
    }
    return array("Federalism divides power between federal and state governments, allowing local control while maintaining national unity!");
}

function get_democracy_responses($answer_length) {
    if ($answer_length === 'short') {
        return array("We live in a democratic republic with elected representatives.");
    }
    return array("We live in a democratic republic where citizens elect representatives to make decisions. It's government by and for the people!");
}

function get_voting_responses($course, $answer_length) {
    if ($course === 'texas') {
        if ($answer_length === 'short') {
            return array("Texas has specific voting laws and procedures for state elections.");
        }
        return array("Texas voting involves both federal and state elections. The state has specific voter ID requirements and election procedures that reflect Texas political culture and legal framework.");
    }

    if ($answer_length === 'short') {
        return array("Voting is a fundamental right and responsibility!");
    }
    return array("Voting is a fundamental right and responsibility! The Electoral College system elects our President based on state representation.");
}

function get_civil_rights_responses($answer_length) {
    if ($answer_length === 'short') {
        return array("Civil rights ensure equal treatment under law.");
    }
    return array("Civil rights ensure equal treatment under law, while civil liberties protect individual freedoms from government interference!");
}
