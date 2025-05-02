<!DOCTYPE html>
<!-- এটি একটি HTML5 ডকুমেন্ট নির্দেশ করে -->
<html lang="en">
<!-- এটি HTML ডকুমেন্টের রুট উপাদান। lang="en" মানে ডকুমেন্টের প্রধান ভাষা ইংরেজি। -->
<head>
  <!-- head অংশে ডকুমেন্টের মেটাডেটা, টাইটেল, এবং অন্যান্য রিসোর্স লিঙ্ক করা থাকে -->
  <meta charset="UTF-8">
  <!-- এটি অক্ষর এনকোডিং নির্দিষ্ট করে, যা ব্রাউজারকে সঠিকভাবে টেক্সট প্রদর্শন করতে সাহায্য করে -->
  <title>AI Quiz Master</title>
  <!-- এটি ব্রাউজার ট্যাবে প্রদর্শিত ডকুমেন্টের টাইটেল -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- এটি ভিউপোর্টের আকার নিয়ন্ত্রণ করে, যা ডিভাইস অনুযায়ী পৃষ্ঠার স্কেলিং ঠিক রাখে এবং প্রতিক্রিয়াশীল ডিজাইন সক্ষম করে -->
  <!-- Link to Tailwind CSS CDN -->
  <!-- Tailwind CSS ফ্রেমওয়ার্কের জন্য CDN লিঙ্ক -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Link to your separate CSS file -->
  <!-- আপনার নিজস্ব কাস্টম CSS ফাইলের লিঙ্ক -->
  <link rel="stylesheet" href="style.css">
</head>
<!-- body অংশে ডকুমেন্টের দৃশ্যমান বিষয়বস্তু থাকে -->
<body class="bg-gradient-to-br from-gray-950 to-gray-800 min-h-screen flex items-center justify-center p-4 text-white font-sans">
  <!-- body এর স্টাইল: ব্যাকগ্রাউন্ডে গ্রেডিয়েন্ট, পুরো স্ক্রিনের ন্যূনতম উচ্চতা, বিষয়বস্তুকে কেন্দ্রে আনা, প্যাডিং, সাদা টেক্সট, স্যান্স-সেরিফ ফন্ট -->

  <div class="w-full max-w-2xl bg-gray-900 rounded-2xl p-6 shadow-2xl relative overflow-hidden border border-gray-700">
    <!-- মূল কন্টেইনার div: স্ক্রিনের প্রস্থের ১০০%, সর্বোচ্চ প্রস্থ 2xl (48rem), গাঢ় ধূসর ব্যাকগ্রাউন্ড, গোলাকার কোণা (2xl), প্যাডিং, শ্যাডো, রিলেটিভ পজিশনিং, ওভারফ্লো লুকানো, ধূসর বর্ডার -->

    <!-- Custom Prompt Section -->
    <!-- কাস্টম প্রম্পট (বিষয়) নির্ধারণ করার অংশ -->
    <div class="mb-8 space-y-4 p-4 bg-gray-800 rounded-xl border border-gray-700 shadow-inner">
      <!-- মার্জিন বটম, উল্লম্ব স্পেস, প্যাডিং, গাঢ় ধূসর ব্যাকগ্রাউন্ড, গোলাকার কোণা, ধূসর বর্ডার, ইনার শ্যাডো -->
      <div class="flex flex-col md:flex-row gap-4">
        <!-- ফ্লেক্সবক্স লেআউট: ছোট স্ক্রিনে কলাম (উল্লম্ব), মাঝারি স্ক্রিনে রো (অনুভূমিক), উপাদানগুলির মধ্যে ৪ ইউনিট ফাঁকা -->
        <input id="customPrompt" type="text" placeholder="একটি মজার বিষয় লিখুন!"
               class="w-full md:flex-grow bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500 border border-gray-600 focus:border-blue-500 text-sm">
        <!-- কাস্টম প্রম্পটের জন্য ইনপুট ফিল্ড: ID 'customPrompt', টাইপ টেক্সট, বাংলা প্লেসহোল্ডার টেক্সট। স্টাইল: পুরো প্রস্থ, মাঝারি স্ক্রিনে ফ্লেক্স-গ্রো (স্থান দখল করবে), গাঢ় ধূসর ব্যাকগ্রাউন্ড, সাদা টেক্সট, প্যাডিং, গোলাকার কোণা, ফোকাসে আউটলাইন ও রিং সরানো, প্লেসহোল্ডার টেক্সটের রঙ, বর্ডার, ছোট ফন্ট -->
        <button id="submitPrompt" class="w-full md:w-auto bg-gradient-to-r from-purple-600 to-indigo-700 hover:from-purple-700 hover:to-indigo-800 text-white px-6 py-2 rounded-lg font-semibold transition-all hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-purple-500 active:scale-95 text-sm">
          বিষয় সেট করুন! ✨
        </button>
        <!-- বিষয় সেট করার জন্য বাটন: ID 'submitPrompt', পুরো প্রস্থ (ছোট স্ক্রিন), অটো প্রস্থ (মাঝারি স্ক্রিন), বেগুনি থেকে নীল গ্রেডিয়েন্ট ব্যাকগ্রাউন্ড, হোভারে গাঢ় গ্রেডিয়েন্ট, সাদা টেক্সট, প্যাডিং, গোলাকার কোণা, মোটা ফন্ট, ট্রানজিশন, হোভারে সামান্য বড় হওয়া, ফোকাসে আউটলাইন ও রিং, অ্যাকটিভে সামান্য ছোট হওয়া, ছোট ফন্ট। বাংলা বাটন টেক্সট -->
      </div>
      <div class="text-xs text-gray-400 text-center">অথবা একটি র্যান্ডম প্রশ্নের জন্য খালি রাখুন! 😉</div>
      <!-- ছোট্ট নির্দেশনা টেক্সট: অতিরিক্ত ছোট ফন্ট, ধূসর টেক্সট, কেন্দ্রে সারিবদ্ধ। বাংলা টেক্সট -->
    </div>

    <!-- Header & Score -->
    <!-- শিরোনাম এবং স্কোরের অংশ -->
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-700">
      <!-- ফ্লেক্সবক্স লেআউট: উপাদানগুলির মধ্যে স্থান সমানভাবে বিতরণ, উল্লম্বভাবে কেন্দ্রে সারিবদ্ধ, মার্জিন বটম, প্যাডিং বটম, ধূসর বর্ডার বটম -->
      <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">🧠 এআই কুইজ মাস্টার</h1>
      <!-- শিরোনাম: 2xl ফন্ট (ছোট স্ক্রিন), 3xl ফন্ট (মাঝারি স্ক্রিন), মোটা ফন্ট, নীল থেকে বেগুনি গ্রেডিয়েন্ট ব্যাকগ্রাউন্ড যা টেক্সটে ক্লিপ করা হয়েছে (টেক্সট গ্রেডিয়েন্ট ইফেক্ট), টেক্সট ট্রান্সপারেন্ট। বাংলা শিরোনাম -->
      <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- স্কোরের জন্য ফ্লেক্স কন্টেইনার: উপাদানগুলি উল্লম্বভাবে কেন্দ্রে সারিবদ্ধ, উপাদানগুলির মধ্যে ৩ ইউনিট ফাঁকা (ছোট স্ক্রিন), ৪ ইউনিট ফাঁকা (মাঝারি স্ক্রিন) -->
        <div class="bg-gray-800 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg border border-gray-700">
          <!-- স্কোর ডিসপ্লে কন্টেইনার: গাঢ় ধূসর ব্যাকগ্রাউন্ড, অনুভূমিক ও উল্লম্ব প্যাডিং, গোলাকার কোণা, ধূসর বর্ডার -->
          <span class="text-green-400 text-sm sm:text-base">✅</span>
          <!-- সঠিক উত্তরের আইকন: সবুজ টেক্সট, ছোট ফন্ট (ছোট স্ক্রিন), বেস ফন্ট (মাঝারি স্ক্রিন) -->
          <span id="correctCount" class="font-semibold text-sm sm:text-base">0</span>
          <!-- সঠিক উত্তরের সংখ্যা দেখানোর জন্য স্প্যান: ID 'correctCount', মোটা ফন্ট, ছোট/বেস ফন্ট। শুরুতে ০ -->
          <span class="mx-1 sm:mx-2 text-gray-500 text-sm sm:text-base">|</span>
          <!-- বিভাজক: মার্জিন অনুভূমিক, ধূসর টেক্সট, ছোট/বেস ফন্ট -->
          <span class="text-red-400 text-sm sm:text-base">❌</span>
          <!-- ভুল উত্তরের আইকন: লাল টেক্সট, ছোট/বেস ফন্ট -->
          <span id="wrongCount" class="font-semibold text-sm sm:text-base">0</span>
          <!-- ভুল উত্তরের সংখ্যা দেখানোর জন্য স্প্যান: ID 'wrongCount', মোটা ফন্ট, ছোট/বেস ফন্ট। শুরুতে ০ -->
        </div>
      </div>
    </div>

    <!-- Progress Bar -->
    <!-- কুইজের অগ্রগতির জন্য প্রোগ্রেস বার -->
    <div class="h-1.5 bg-gray-700 mb-6 rounded-full overflow-hidden">
      <!-- প্রোগ্রেস বারের ব্যাকগ্রাউন্ড ট্র্যাক: উচ্চতা ১.৫ ইউনিট, গাঢ় ধূসর ব্যাকগ্রাউন্ড, মার্জিন বটম, গোলাকার কোণা, ওভারফ্লো লুকানো -->
      <div id="progressBar" class="progress-bar h-full bg-gradient-to-r from-blue-500 to-teal-400 rounded-full w-0"></div>
      <!-- প্রোগ্রেস বারের ফিল: ID 'progressBar', পুরো উচ্চতা, নীল থেকে সায়ান গ্রেডিয়েন্ট ব্যাকগ্রাউন্ড, গোলাকার কোণা, শুরুতে প্রস্থ ০ (JS দ্বারা আপডেট হবে) -->
    </div>

    <!-- Question Container -->
    <!-- প্রশ্ন এবং বিকল্পগুলির জন্য কন্টেইনার -->
    <div id="questionContainer" class="relative min-h-[300px] p-4 sm:p-6 bg-gray-800 rounded-xl border border-gray-700 shadow-lg">
      <!-- ID 'questionContainer', রিলেটিভ পজিশনিং, ন্যূনতম উচ্চতা ৩০০px, প্যাডিং, গাঢ় ধূসর ব্যাকগ্রাউন্ড, গোলাকার কোণা, ধূসর বর্ডার, শ্যাডো -->
      <div id="skeletonLoader" class="absolute inset-0 space-y-6 p-4 sm:p-6 animate-pulse">
        <!-- লোডিং স্কেলেটন ইফেক্ট: ID 'skeletonLoader', অ্যাবসোলিউট পজিশনিং (কন্টেইনারের উপর), প্যাডিং, অ্যানিমেটেড পালস ইফেক্ট -->
        <div class="h-6 sm:h-8 shimmer rounded-lg w-3/4"></div>
        <!-- লোডিং প্লেসহোল্ডার লাইন: উচ্চতা, গোলাকার কোণা, প্রস্থ তিন-চতুর্থাংশ -->
        <div class="h-10 sm:h-12 shimmer rounded-xl"></div>
        <!-- লোডিং প্লেসহোল্ডার ব্লক: উচ্চতা, গোলাকার কোণা -->
        <div class="h-10 sm:h-12 shimmer rounded-xl"></div>
        <div class="h-10 sm:h-12 shimmer rounded-xl"></div>
        <div class="h-10 sm:h-12 shimmer rounded-xl"></div>
      </div>
      <div id="content" class="relative opacity-0 transition-opacity duration-300 space-y-4 sm:space-y-6">
         <!-- প্রশ্ন এবং বিকল্পগুলির আসল বিষয়বস্তু: ID 'content', রিলেটিভ পজিশনিং, শুরুতে অস্বচ্ছতা ০, ৩00ms এ অস্বচ্ছতা ট্রানজিশন, উল্লম্ব স্পেস -->
         <!-- Error message area -->
         <!-- ত্রুটির বার্তা দেখানোর স্থান -->
         <div id="errorMessage" class="text-red-400 text-center hidden text-xs sm:text-sm mb-4 whitespace-pre-wrap p-3 sm:p-4 bg-red-900 bg-opacity-30 rounded-lg border border-red-700"></div>
         <!-- ID 'errorMessage', লাল টেক্সট, কেন্দ্রে সারিবদ্ধ, শুরুতে লুকানো, অতিরিক্ত ছোট/ছোট ফন্ট, মার্জিন বটম, টেক্সট স্পেস ঠিক রাখা, প্যাডিং, হালকা লাল ব্যাকগ্রাউন্ড, গোলাকার কোণা, লাল বর্ডার -->
        <div id="question" class="text-base sm:text-xl font-medium mb-4 sm:mb-6 text-blue-300 leading-relaxed"></div>
        <!-- প্রশ্নের টেক্সট দেখানোর স্থান: ID 'question', বেস/xl ফন্ট, মাঝারি মোটা ফন্ট, মার্জিন বটম, নীল টেক্সট, লাইনের মধ্যে আলগা ব্যবধান। বিষয়বস্তু JS দ্বারা যোগ করা হবে -->
        <div id="options" class="grid gap-3 sm:gap-4">
          <!-- বিকল্প বা অপশনগুলি দেখানোর জন্য কন্টেইনার: ID 'options', গ্রিড লেআউট, উপাদানগুলির মধ্যে ফাঁকা স্থান। বিকল্পগুলি JS দ্বারা তৈরি হবে -->
          <!-- Options will be populated here -->
        </div>
      </div>
    </div>

    <!-- Feedback & Controls -->
    <!-- ফিডব্যাক (সঠিক/ভুল) এবং নিয়ন্ত্রণ বাটনগুলির অংশ -->
    <div id="feedback" class="mt-6 text-center text-base sm:text-lg font-semibold min-h-[1.5em] text-purple-400"></div>
    <!-- ফিডব্যাক দেখানোর স্থান: ID 'feedback', মার্জিন টপ, কেন্দ্রে সারিবদ্ধ টেক্সট, বেস/lg ফন্ট, মোটা ফন্ট, ন্যূনতম উচ্চতা (টেক্সট না থাকলেও জায়গা ধরে রাখার জন্য), বেগুনি টেক্সট। বিষয়বস্তু JS দ্বারা যোগ করা হবে -->
    <div class="mt-6 flex flex-col md:flex-row justify-between gap-4">
      <!-- বাটনগুলির কন্টেইনার: মার্জিন টপ, ফ্লেক্সবক্স লেআউট, কলাম (ছোট স্ক্রিন) বা রো (মাঝারি স্ক্রিন), উপাদানগুলির মধ্যে স্থান সমানভাবে বিতরণ, উপাদানগুলির মধ্যে ৪ ইউনিট ফাঁকা -->
      <button id="historyBtn" class="w-full md:flex-1 bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-gray-500 active:scale-95 font-semibold text-gray-300 text-sm sm:text-base">
        📜 ইতিহাস
      </button>
      <!-- ইতিহাস বাটন: ID 'historyBtn', পুরো প্রস্থ (ছোট স্ক্রিন) বা ফ্লেক্স-১ (মাঝারি স্ক্রিন - বাকি স্থান দখল করবে), গাঢ় ধূসর ব্যাকগ্রাউন্ড, হোভারে হালকা গাঢ় ধূসর, প্যাডিং, গোলাকার কোণা, ট্রানজিশন, ফোকাস স্টাইল, অ্যাকটিভ স্টাইল, মোটা ফন্ট, হালকা ধূসর টেক্সট, ছোট/বেস ফন্ট। বাংলা বাটন টেক্সট -->
      <button id="nextBtn" class="w-full md:flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-3 rounded-xl font-semibold transition-transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 active:scale-95 text-sm sm:text-base">
        পরবর্তী প্রশ্ন! 👉
      </button>
      <!-- পরবর্তী প্রশ্ন বাটন: ID 'nextBtn', পুরো প্রস্থ (ছোট স্ক্রিন) বা ফ্লেক্স-১ (মাঝারি স্ক্রিন), নীল থেকে সায়ান গ্রেডিয়েন্ট ব্যাকগ্রাউন্ড, হোভারে গাঢ় গ্রেডিয়েন্ট, সাদা টেক্সট, প্যাডিং, গোলাকার কোণা, মোটা ফন্ট, ট্রানজিশন, হোভারে সামান্য বড় হওয়া, ফোকাস স্টাইল, অ্যাকটিভ স্টাইল, ছোট/বেস ফন্ট। বাংলা বাটন টেক্সট -->
    </div>
  </div>

  <!-- History Modal -->
  <!-- ইতিহাস দেখানোর জন্য মডেল (পপআপ) -->
  <div id="historyModal" class="hidden fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-50">
    <!-- ID 'historyModal', শুরুতে লুকানো, ফিক্সড পজিশনিং (ভিউপোর্ট অনুযায়ী), পুরো স্ক্রিন কভার করে, হালকা অস্বচ্ছ কালো ব্যাকগ্রাউন্ড, ব্যাকড্রপে ব্লার ইফেক্ট, z-ইনডেক্স ৫০ (অন্যান্য উপাদানের উপরে থাকার জন্য) -->
    <div class="history-modal-panel absolute right-0 top-0 h-full w-full max-w-full md:max-w-md bg-gray-900 p-6 shadow-xl transform translate-x-full rounded-l-xl border-l border-gray-700">
      <!-- মডেল প্যানেল: অ্যাবসোলিউট পজিশনিং (পিতামাতার উপর), ডান এবং উপর প্রান্তে অবস্থিত, পুরো উচ্চতা, পুরো প্রস্থ (ছোট স্ক্রিন) বা সর্বোচ্চ md (48rem) প্রস্থ (মাঝারি স্ক্রিন), গাঢ় ধূসর ব্যাকগ্রাউন্ড, প্যাডিং, শ্যাডো, শুরুতে ডান দিকে সম্পূর্ণ বাইরে সরানো (JS দ্বারা সরানো হবে), বাম দিকে গোলাকার কোণা, বাম দিকে ধূসর বর্ডার -->
      <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
        <!-- মডেল হেডারের ফ্লেক্স কন্টেইনার: স্থান সমানভাবে বিতরণ, উল্লম্বভাবে কেন্দ্রে, মার্জিন বটম, বর্ডার বটম, প্যাডিং বটম -->
        <h2 class="text-xl font-bold text-blue-300">📚 কুইজ ইতিহাস</h2>
        <!-- মডেল শিরোনাম: xl ফন্ট, মোটা ফন্ট, নীল টেক্সট। বাংলা শিরোনাম -->
        <button id="closeHistory" class="text-3xl text-gray-400 hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 rounded-md px-2 leading-none">×</button>
        <!-- মডেল বন্ধ করার বাটন: ID 'closeHistory', 3xl ফন্ট সাইজ, ধূসর টেক্সট, হোভারে হালকা ধূসর, ফোকাস স্টাইল, গোলাকার কোণা, অনুভূমিক প্যাডিং, লাইন হাইট কমানো। বন্ধ করার চিহ্ন -->
      </div>
      <div id="historyList" class="history-list space-y-4 max-h-[calc(100vh-140px)] overflow-y-auto pr-2 text-sm">
        <!-- ইতিহাসের তালিকা দেখানোর স্থান: ID 'historyList', কাস্টম ক্লাস 'history-list', উল্লম্ব স্পেস, সর্বোচ্চ উচ্চতা (ভিউপোর্ট উচ্চতা থেকে 140px কম), উল্লম্ব স্ক্রলিং সক্ষম, ডান দিকে সামান্য প্যাডিং (স্ক্রলবারের জন্য জায়গা করতে), ছোট ফন্ট। ইতিহাসের আইটেমগুলি JS দ্বারা যোগ করা হবে -->
        <!-- History items will be populated here -->
      </div>
    </div>
  </div>

  <!-- Link to your separate JavaScript file -->
  <!-- আপনার কাস্টম JavaScript ফাইলের লিঙ্ক -->
  <!-- Place this just before the closing </body> tag -->
  <!-- এটি </body> ট্যাগের ঠিক আগে স্থাপন করা হয়েছে যাতে HTML লোড হওয়ার পর স্ক্রিপ্ট চলে -->
  <script src="scripts.js"></script>

</body>
</html>
