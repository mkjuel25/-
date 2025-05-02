<?php
// api.php

// --- .env ফাইল থেকে এনভায়রনমেন্ট ভ্যারিয়েবল লোড করা হচ্ছে ---
// .env ফাইল লোড করার এটি একটি সরল ম্যানুয়াল পদ্ধতি। বড় প্রোজেক্টের জন্য vlucas/phpdotenv লাইব্রেরি ব্যবহার করার কথা বিবেচনা করতে পারেন।
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // কমেন্টগুলো এড়িয়ে যান
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // কেবল '=' যুক্ত লাইনগুলো প্রসেস করুন
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // যদি আগে থেকে সেট করা না থাকে তবে এনভায়রনমেন্ট ভ্যারিয়েবল সেট করুন
        if (!isset($_SERVER[$name]) && !isset($_ENV[$name])) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}
// --- এনভায়রনমেন্ট ভ্যারিয়েবল লোড শেষ ---


// এনভায়রনমেন্ট ভ্যারিয়েবল থেকে API কী সংগ্রহ করুন
// $_ENV অথবা getenv() ব্যবহার করুন
$api_key = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');


// API রিকোয়েস্ট হ্যান্ডেল করা হচ্ছে (কেবল POST রিকোয়েস্ট এখানে আসবে)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // POST থেকে প্রম্পট নিন, অথবা ডিফল্ট ব্যবহার করুন
    $userPrompt = $_POST['prompt'] ?? '';

    // প্রয়োজন হলে userPrompt যাচাই বা স্যানিটাইজ করুন (সংক্ষিপ্ততার জন্য বাদ দেওয়া হয়েছে)

    // ডিফল্ট প্রম্পট
    $defaultPrompt = "একটি সাধারণ জ্ঞানের যেকোনো বিষয়ে (যেমন কঠিন, মজার ইত্যাদি) ভিন্নধর্মী একটি মাল্টিপল চয়েস প্রশ্ন তৈরি করুন। বাংলাদেশী স্টাইলে লিখুন";

    // যদি ব্যবহারকারী প্রম্পট সরবরাহ করে, তবে সেটি ব্যবহার করুন, না হলে ডিফল্ট প্রম্পট ব্যবহার করুন
    $finalPrompt = !empty($userPrompt) ? "বিষয়টি নিয়ে একটি ইউনিক ও ভিন্নধর্মী মাল্টিপল চয়েস প্রশ্ন তৈরি করুন: " . $userPrompt : 
    $defaultPrompt;

    // স্ট্রিক্ট ফরম্যাটের নির্দেশনা যুক্ত করুন
    // নিশ্চিত করা হয়েছে ফরম্যাট নির্দেশনা প্রিফিক্স এবং ফাইনাল Answer লাইন সম্পর্কে স্পষ্ট।
    $finalPrompt .= "\n\nনিচের কঠোর ফরম্যাটটি অনুসরণ করুন (অনুগ্রহ করে 'Question:', 'A)', 'B)', 'C)', 'D)', এবং 'Answer:' প্রিফিক্সগুলি অন্তর্ভুক্ত করুন):\n\nQuestion: <প্রশ্নের লেখা>\nA) <অপশন A এর লেখা>\nB) <অপশন B এর লেখা>\nC) <অপশন C এর লেখা>\nD) <অপশন D এর লেখা>\nAnswer: <সঠিক অপশনের অক্ষর: A/B/C/D>";
    

    $data = [
        "contents" => [
            [
                "parts" => [["text" => $finalPrompt]]
            ]
        ],
        // ঐচ্ছিক: ক্ষতিকর কন্টেন্ট ফিল্টার করার জন্য সেফটি সেটিংস যুক্ত করুন। প্রয়োজন অনুযায়ী থ্রেশহোল্ড অ্যাডজাস্ট করুন।
        // লেটেস্ট ক্যাটাগরি এবং থ্রেশহোল্ডের জন্য জেমিনি API ডকুমেন্টেশন দেখুন:
        // https://ai.google.dev/tutorials/rest_quickstart#safety_settings
        // ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => "BLOCK_ONLY_HIGH"],
    ];

    // API কী সেট করা আছে কিনা বা প্লেসহোল্ডার কিনা চেক করুন
    if (empty($api_key) || $api_key === 'YOUR_API_KEY') {
         echo json_encode(["error" => "API কী কনফিগার করা নেই। অনুগ্রহ করে একই ডিরেক্টরিতে '.env' ফাইল তৈরি করুন এবং GEMINI_API_KEY ভ্যারিয়েবল সেট করুন, অথবা ওয়েব সার্ভার কনফিগারেশনের মাধ্যমে সেট করুন।"]);
         exit;
    }

    // API এন্ডপয়েন্ট
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // স্পষ্টভাবে POST মেথড সেট করুন
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    // সংযোগ টাইমআউট (যেমন ১০ সেকেন্ড) এবং রেসপন্স টাইমআউট (যেমন ৩০ সেকেন্ড) যোগ করুন
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // প্রোডাকশনে সর্বদা SSL যাচাই করুন
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // প্রোডাকশনে সর্বদা হোস্টনেম যাচাই করুন


    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // --- পার্সিং এবং ত্রুটি হ্যান্ডেল করা হচ্ছে ---
    $json = json_decode($response, true);
    $text = '';
    $error_message = null; // প্রাথমিক ত্রুটি বার্তা
    $parse_error_detail = null; // বিশেষভাবে পার্সিং ব্যর্থতা সম্পর্কে বিস্তারিত

    if ($response === false) {
        $error_message = "cURL ত্রুটি: " . $curl_error;
    } elseif ($http_status >= 400) {
        $error_message = "API ত্রুটি: HTTP অবস্থা " . $http_status;
        if (isset($json['error']['message'])) {
             $error_message .= " - " . $json['error']['message'];
        } else {
             // স্ট্যান্ডার্ড ফরম্যাটে না থাকলেও ত্রুটির বিস্তারিত ধরার চেষ্টা
              $error_message .= " - Raw Response: " . (is_string($response) ? $response : json_encode($response));
        }
    } elseif (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $json['candidates'][0]['content']['parts'][0]['text'];

        // --- শক্তিশালী পার্সিং ---
        $qMatch = [];
        $a = []; $b = []; $c = []; $d = [];
        $answerMatch = [];

        // DOTALL (. নিউলাইন সহ যেকোনো অক্ষর বোঝায়) এবং নন-গ্রিডি ম্যাচিং (`.*?`) ব্যবহার করুন
        // প্রশ্ন ম্যাচ করুন
        preg_match("/Question:\s*(.*?)\n/is", $text, $qMatch);
        // অপশন A-D ম্যাচ করুন (নন-গ্রিডি) - নিশ্চিত করুন যেগুলি নিউলাইন বা স্ট্রিংয়ের শেষ দিয়ে ফলো হচ্ছে robustness এর জন্য
         preg_match("/A\)\s*(.*?)\n/is", $text, $a);
         preg_match("/B\)\s*(.*?)\n/is", $text, $b);
         preg_match("/C\)\s*(.*?)\n/is", $text, $c);
         // নিশ্চিত করুন D স্ট্রিংয়ের শেষ পর্যন্ত বা Answer লাইন পর্যন্ত ম্যাচ করে
         preg_match("/D\)\s*(.*?)(?=\nAnswer:|$)/is", $text, $d);

        // উত্তর ম্যাচ করুন (কেইস-ইনসেনসিটিভ, একটি অক্ষর [A-D])
        // ফিক্সড রেজেক্স: "Answer:" ম্যাচ করে, ঐচ্ছিক হোয়াইটস্পেস, তারপর [A-D] ক্যাপচার করে।
        // অক্ষর ([A-D]) এর পরে নিউলাইন বা শেষ হওয়ার আগে ঐচ্ছিক অক্ষর (যেমন ')') থাকার অনুমতি দেয়।
        preg_match("/Answer:\s*([A-D]).*?(?:\n|$)/i", $text, $answerMatch);


        // সব অপরিহার্য অংশ সফলভাবে পার্স হয়েছে কিনা চেক করুন
        // !empty($match[1]) ব্যবহার করুন যাতে ক্যাপচার করা গ্রুপ খালি না থাকে
        if (empty($qMatch[1]) || empty($a[1]) || empty($b[1]) || empty($c[1]) || empty($d[1]) || empty($answerMatch[1])) {
             $parse_error_detail = "রেসপন্স টেক্সট থেকে সব অপরিহার্য অংশ (প্রশ্ন, অপশন A-D, উত্তরের অক্ষর) এক্সট্রাক্ট করতে ব্যর্থ হয়েছে। রেসপন্স ফরম্যাট ফলো করেনি।";
             // ডিবাগিংয়ের জন্য raw রেসপন্স টেক্সট লগ করুন
             error_log("কুইজ মাস্টার পার্স ত্রুটি: " . $parse_error_detail . "\nরেসপন্স টেক্সট:\n" . $text);

             // যদি পার্স করা অংশ পাওয়া যায় তবে ফলব্যাক ডাটা সরবরাহ করুন
             $question = $qMatch[1] ?? "প্রশ্ন টেক্সট পার্স করতে ত্রুটি হয়েছে।";
             $options = [
                 $a[1] ?? "অপশন A ত্রুটি।",
                 $b[1] ?? "অপশন B ত্রুটি।",
                 $c[1] ?? "অপশন C ত্রুটি।",
                 $d[1] ?? "অপশন D ত্রুটি।"
             ];
             $correctAnswer = ""; // সঠিক উত্তর নির্ধারণ করা যায়নি যদি পার্সিং ব্যর্থ হয়

        } else {
             // সফল পার্সিং
             $question = trim($qMatch[1]);
             $options = [trim($a[1]), trim($b[1]), trim($c[1]), trim($d[1])];
             $correctOptionLetter = strtoupper(trim($answerMatch[1]));

             // অক্ষর based সঠিক উত্তরের টেক্সট খুঁজুন
             $correctAnswer = "";
             $letter_to_index = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
             $correctIndex = $letter_to_index[$correctOptionLetter] ?? -1;

             if ($correctIndex !== -1 && isset($options[$correctIndex])) {
                  $correctAnswer = $options[$correctIndex];
             } else {
                 // এই ক্ষেত্রে সাধারণত আসার কথা নয় যদি $answerMatch[1] সফলভাবে [A-D] হিসাবে পার্স হয়,
                 // কিন্তু চূড়ান্ত সুরক্ষা হিসাবে যদি কোনোভাবে অক্ষর ইন্ডেক্সে ম্যাপ না করে বা অপশন অনুপস্থিত থাকে।
                 $parse_error_detail = "পার্স করা উত্তরের অক্ষর '" . $correctOptionLetter . "' বৈধ অপশন ইন্ডেক্সে ম্যাপ করছে না বা অপশন টেক্সট অনুপস্থিত।";
                 $correctAnswer = ""; // সঠিক উত্তর নির্ধারণ করা যায়নি
             }
        }

    } else {
        // অপ্রত্যাশিত API রেসপন্স স্ট্রাকচার বা রেসপন্সে খালি কন্টেন্ট।
        $error_message = "অনপ্রত্যাশিত API রেসপন্স স্ট্রাকচার বা রেসপন্সে খালি কন্টেন্ট।";
        // ডিবাগিংয়ের জন্য raw রেসপন্স লগ করুন
        error_log("কুইজ মাস্টার অপ্রত্যাশিত রেসপন্স: " . (is_string($response) ? $response : json_encode($response)));
    }


    // API এবং পার্সিং দুটিই ব্যর্থ হলে ত্রুটি বার্তাগুলো একত্র করুন
    if ($error_message && $parse_error_detail) {
         $final_error_message = "API ত্রুটি: " . $error_message . "\n---\nপার্সিং ত্রুটি: " . $parse_error_detail;
    } elseif ($error_message) {
        $final_error_message = $error_message;
    } elseif ($parse_error_detail) {
         // যদি কেবল পার্সিং ব্যর্থ হয়, কিন্তু API কল 200 OK ছিল
         $final_error_message = "পার্সিং ত্রুটি: " . $parse_error_detail . "\n---\nRaw text:\n" . $text; // পার্সিং ত্রুটির জন্য raw text যোগ করুন
    } else {
         $final_error_message = null; // কোন ত্রুটি নেই
    }


    echo json_encode([
        "error" => $final_error_message,
        "question" => $question ?? "প্রশ্ন লোড করা যায়নি।", // পার্স করা বা ফলব্যাক প্রশ্ন ব্যবহার করুন
        "options" => $options ?? [], // পার্স করা বা ফলব্যাক অপশন ব্যবহার করুন
        "answer" => $correctAnswer ?? "" // পার্স করা বা ফলব্যাক সঠিক উত্তর ব্যবহার করুন (ত্রুটির ক্ষেত্রে খালি)
    ]);

    exit; // JSON রেসপন্স পাঠানোর পর স্ক্রিপ্ট বন্ধ করুন
}

// যদি এটি POST রিকোয়েস্ট না হয়, কিছু করবেন না। index.html ডিসপ্লে হ্যান্ডেল করবে।
// শুধুমাত্র PHP কোড ধারণকারী ফাইলগুলিতে ক্লোজিং ?> ট্যাগের প্রয়োজন নেই।
