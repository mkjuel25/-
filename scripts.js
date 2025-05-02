let currentPrompt = '';
let currentQuestionData = null;
let correctCount = 0;
let wrongCount = 0;
let history = [];
const localStorageKey = 'quizMasterData';
const localStorageExpiryMinutes = 30;
let progressIntervalId = null;
let autoNextTimeoutId = null;
let isAnswered = false;

const elements = {
  question: document.getElementById('question'),
  options: document.getElementById('options'),
  feedback: document.getElementById('feedback'),
  nextBtn: document.getElementById('nextBtn'),
  skeletonLoader: document.getElementById('skeletonLoader'),
  content: document.getElementById('content'),
  progressBar: document.getElementById('progressBar'),
  correctCount: document.getElementById('correctCount'),
  wrongCount: document.getElementById('wrongCount'),
  historyModal: document.getElementById('historyModal'),
  historyList: document.getElementById('historyList'),
  historyBtn: document.getElementById('historyBtn'),
  closeHistory: document.getElementById('closeHistory'),
  customPrompt: document.getElementById('customPrompt'),
  submitPrompt: document.getElementById('submitPrompt'),
  errorMessage: document.getElementById('errorMessage')
};

function saveToStorage() {
  const expiryTime = new Date().getTime() + (localStorageExpiryMinutes * 60 * 1000);
  localStorage.setItem(localStorageKey, JSON.stringify({
    history: history,
    correctCount: correctCount,
    wrongCount: wrongCount,
    currentPrompt: currentPrompt,
    expiry: expiryTime
  }));
  console.log("ডাটা লোকাল স্টোরেজে সেভ করা হয়েছে। মেয়াদ উত্তীর্ণ হবে:", new Date(expiryTime).toLocaleString());
}

function loadFromStorage() {
  const savedData = localStorage.getItem(localStorageKey);
  const currentTime = new Date().getTime();

  if (savedData) {
    try {
      const data = JSON.parse(savedData);
      if (data && data.expiry && data.expiry > currentTime) {
         history = Array.isArray(data.history) ? data.history : [];
         correctCount = typeof data.correctCount === 'number' ? data.correctCount : 0;
         wrongCount = typeof data.wrongCount === 'number' ? data.wrongCount : 0;
         currentPrompt = typeof data.currentPrompt === 'string' ? data.currentPrompt : '';
         updateScores();
         elements.customPrompt.value = currentPrompt;
         console.log("লোকাল স্টোরেজ থেকে ডাটা লোড করা হয়েছে (মেয়াদ উত্তীর্ণ হয়নি)।");
         return;
      } else {
          console.log("লোকাল স্টোরেজের ডাটার মেয়াদ উত্তীর্ণ হয়েছে।");
          localStorage.removeItem(localStorageKey);
      }
    } catch (e) {
       console.error("লোকাল স্টোরেজ ডাটা পার্স করতে ব্যর্থ:", e);
        localStorage.removeItem(localStorageKey);
    }
  }

  console.log("অবস্থা ইনিশিয়ালাইজ বা ক্লিয়ার করা হচ্ছে।");
  history = [];
  correctCount = 0;
  wrongCount = 0;
  currentPrompt = '';
  elements.customPrompt.value = '';
  saveToStorage();
  updateScores();
}

function stopAutoProgress() {
  if (progressIntervalId !== null) {
    clearInterval(progressIntervalId);
    progressIntervalId = null;
  }
}

 function stopAutoNextTimeout() {
    if (autoNextTimeoutId !== null) {
        clearTimeout(autoNextTimeoutId);
        autoNextTimeoutId = null;
    }
 }

function startAutoProgress() {
  stopAutoProgress();
   if (isAnswered) return;

  elements.progressBar.style.width = '0%';
  let width = 0;
  const duration = 15000;
  const intervalTime = 50;
  const increment = (100 / (duration / intervalTime));

  progressIntervalId = setInterval(() => {
    width += increment;
    if (width >= 100) {
      width = 100;
      stopAutoProgress();
      if (!isAnswered && currentQuestionData && currentQuestionData.question) {
           handleTimeout();
       } else if (!isAnswered) {
           elements.nextBtn.disabled = false;
       }
    }
    elements.progressBar.style.width = `${width}%`;
  }, intervalTime);
}

function handleTimeout() {
    if (isAnswered) return;
     if (!currentQuestionData || !currentQuestionData.question) {
         console.warn("টাইম আউট/স্কিপ হয়েছে কিন্তু সঠিক প্রশ্নের ডাটা নেই।");
         elements.feedback.textContent = '⏱️ স্কিপ করা হয়েছে! সঠিক প্রশ্ন লোড হয়নি।';
         elements.feedback.classList.remove('text-green-400', 'text-red-400', 'text-purple-400');
         elements.feedback.classList.add('text-yellow-400');
         elements.nextBtn.disabled = false;
         isAnswered = true;
         return;
     }

    isAnswered = true;

    stopAutoProgress();

    wrongCount++;
    updateScores();

    elements.feedback.textContent = `⏱️ সময় শেষ / স্কিপ করা হয়েছে! ⏭️ সঠিক উত্তর ছিল: ${currentQuestionData.answer ?? 'পাওয়া যায়নি'}`;
    elements.feedback.classList.remove('text-green-400', 'text-red-400', 'text-purple-400');
    elements.feedback.classList.add('text-orange-400');

    const optionsElements = elements.options.querySelectorAll('.option-btn');
    optionsElements.forEach(option => {
        option.disabled = true;
         if (currentQuestionData.answer && option.dataset.optionText === currentQuestionData.answer) {
              option.classList.add('correct');
               option.classList.remove('bg-gray-700', 'hover:bg-gray-600', 'focus:ring-blue-500');
         }
         option.classList.remove('hover:bg-gray-600', 'shadow-md', 'hover:shadow-lg');
    });

    addHistoryEntry("Timeout", false);
}

async function fetchQuestion() {
  stopAutoProgress();
  stopAutoNextTimeout();
  isAnswered = false;
  elements.errorMessage.classList.add('hidden');
  elements.skeletonLoader.classList.remove('hidden');
  elements.content.classList.add('opacity-0');
  elements.nextBtn.disabled = true;
  elements.options.innerHTML = '';
  elements.feedback.textContent = '';
  elements.question.textContent = '';
  currentQuestionData = null;
  elements.progressBar.style.width = '0%';
  elements.feedback.classList.remove('text-green-400', 'text-red-400', 'text-orange-400', 'text-yellow-400');
  elements.feedback.classList.add('text-purple-400');

  try {
    const response = await fetch('api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `prompt=${encodeURIComponent(currentPrompt)}`
    });

    if (!response.ok) {
         const errorBody = await response.text();
         throw new Error(`HTTP ত্রুটি! অবস্থা: ${response.status}\nসার্ভার রেসপন্স: ${errorBody}`);
    }

    const data = await response.json();

    if (data.error) {
         throw new Error(`API/সার্ভার ত্রুটি: ${data.error}`);
    }

    if (!data.question || !data.options || data.options.length !== 4 || !data.answer) {
         console.error("প্রপ্ত ডাটা:", data);
         throw new Error("API থেকে অবৈধ ডাটা ফরম্যাট বা অসম্পূর্ণ ডাটা পাওয়া গেছে। প্রশ্ন, অপশন বা উত্তর অনুপস্থিত।");
    }

    await new Promise(resolve => setTimeout(resolve, 300));

    currentQuestionData = data;
    displayQuestion(data);
    elements.nextBtn.disabled = false;
    startAutoProgress();

  } catch (error) {
    console.error('ডাটা আনতে ত্রুটি:', error);
    elements.feedback.textContent = '🚫 প্রশ্ন লোড করতে ত্রুটি হয়েছে।';
    elements.feedback.classList.remove('text-green-400', 'text-purple-400', 'text-orange-400', 'text-yellow-400');
    elements.feedback.classList.add('text-red-400');

    elements.question.textContent = 'প্রশ্ন লোড করা যায়নি।';
    elements.options.innerHTML = '<p class="text-red-400 text-center text-sm">API কী চেক করুন, প্রম্পট পরিবর্তন করুন অথবা আবার চেষ্টা করুন।</p>';
    elements.errorMessage.textContent = `${error.message}`;
    elements.errorMessage.classList.remove('hidden');
    elements.nextBtn.disabled = false;
    isAnswered = true;
    stopAutoProgress();
  } finally {
    elements.skeletonLoader.classList.add('hidden');
    elements.content.classList.remove('opacity-0');
  }
}

function displayQuestion(data) {
  elements.question.textContent = data.question || 'কোন প্রশ্ন লোড হয়নি।';
  elements.options.innerHTML = '';

  if (data.options && data.options.length === 4) {
    const optionLetters = ['A', 'B', 'C', 'D'];
    data.options.forEach((optionText, index) => {
      const optionElement = document.createElement('button');
      optionElement.classList.add('option-btn', 'bg-gray-700', 'hover:bg-gray-600', 'px-4', 'py-3', 'rounded-lg', 'text-left', 'transition-all', 'w-full', 'focus:outline-none', 'focus:ring-2', 'focus-ring-blue-500', 'shadow-md', 'hover:shadow-lg');
      optionElement.innerHTML = `<span class="font-semibold mr-2">${optionLetters[index]})</span> ${optionText}`;
      optionElement.dataset.optionIndex = index;
      optionElement.dataset.optionText = optionText;

      optionElement.addEventListener('click', () => checkAnswer(optionElement));

      elements.options.appendChild(optionElement);
    });
    isAnswered = false;
  } else {
      elements.options.innerHTML = '<p class="text-red-400 text-center text-sm">সঠিক ফরম্যাটে অপশন লোড করা যায়নি।</p>';
      elements.nextBtn.disabled = false;
       isAnswered = true;
       stopAutoProgress();
       elements.feedback.textContent = '⚠️ অপশন অনুপস্থিত!';
       elements.feedback.classList.remove('text-green-400', 'text-purple-400', 'text-orange-400', 'text-yellow-400');
       elements.feedback.classList.add('text-red-400');
  }

  elements.feedback.textContent = 'সাবধানে পছন্দ করুন... 🤔';
  elements.feedback.classList.remove('text-green-400', 'text-red-400', 'text-orange-400', 'text-yellow-400');
  elements.feedback.classList.add('text-purple-400');
}

function checkAnswer(selectedElement) {
  if (isAnswered) return;
  isAnswered = true;

  stopAutoProgress();
  stopAutoNextTimeout();


  const selectedOptionText = selectedElement.dataset.optionText;
  const isCorrect = currentQuestionData?.answer && selectedOptionText === currentQuestionData.answer;
  const optionsElements = elements.options.querySelectorAll('.option-btn');

  optionsElements.forEach(option => {
    option.disabled = true;
    const optionText = option.dataset.optionText;

    if (currentQuestionData?.answer && optionText === currentQuestionData.answer) {
      option.classList.add('correct');
      option.classList.remove('bg-gray-700', 'hover:bg-gray-600', 'focus:ring-blue-500', 'shadow-md', 'hover:shadow-lg');
    } else if (option === selectedElement) {
      option.classList.add('wrong', 'selected');
       option.classList.remove('bg-gray-700', 'hover:bg-gray-600', 'focus:ring-blue-500', 'shadow-md', 'hover:shadow-lg');
    }
     option.classList.remove('hover:bg-gray-600', 'shadow-md', 'hover:shadow-lg');
  });

  if (isCorrect) {
    correctCount++;
    elements.feedback.textContent = 'সম্পূর্ণ সঠিক! 🎉 অসাধারণ!';
    elements.feedback.classList.remove('text-red-400', 'text-purple-400', 'text-orange-400', 'text-yellow-400');
    elements.feedback.classList.add('text-green-400');
  } else {
    wrongCount++;
     const correctAnswerText = currentQuestionData?.answer ?? 'পাওয়া যায়নি';
    elements.feedback.textContent = `ওহ, ঠিক নয়! 😞 সঠিক উত্তর ছিল: ${correctAnswerText}`;
    elements.feedback.classList.remove('text-green-400', 'text-purple-400', 'text-orange-400', 'text-yellow-400');
    elements.feedback.classList.add('text-red-400');
  }

  updateScores();
  addHistoryEntry(selectedOptionText, isCorrect);
  elements.nextBtn.disabled = false;
}

function addHistoryEntry(userAnswerText, isCorrect) {
    if (!currentQuestionData || !currentQuestionData.question || !currentQuestionData.options || !currentQuestionData.answer) {
         console.warn("অসম্পূর্ণ প্রশ্ন ডাটা দিয়ে হিস্টোরি এন্ট্রি যোগ করার চেষ্টা হয়েছে।");
         return;
    }

    const historyEntry = {
        question: currentQuestionData.question,
        options: currentQuestionData.options,
        correctAnswer: currentQuestionData.answer,
        userAnswer: userAnswerText,
        isCorrect: userAnswerText !== "Timeout" ? isCorrect : false,
        timestamp: new Date().toISOString()
    };
    history.push(historyEntry);
    saveToStorage();
}

function deleteHistoryEntry(index) {
    if (index >= 0 && index < history.length) {
        history.splice(index, 1);
        saveToStorage();
        showHistory();
    } else {
        console.warn("অবৈধ ইন্ডেক্স সহ হিস্টোরি এন্ট্রি মুছে ফেলার চেষ্টা হয়েছে:", index);
    }
}

function showHistory() {
    elements.historyList.innerHTML = '';
    if (history.length === 0) {
        elements.historyList.innerHTML = '<p class="text-gray-400 text-center py-4">হিস্টোরি খালি। কুইজিং শুরু করুন! 😄</p>';
    } else {
        history.slice().reverse().forEach((entry, reverseIndex) => {
             const originalIndex = history.length - 1 - reverseIndex;

            const entryElement = document.createElement('div');
            entryElement.classList.add('history-item');

             const contentDiv = document.createElement('div');
             contentDiv.classList.add('history-content', 'space-y-2');

            const qEl = document.createElement('p');
            qEl.classList.add('font-semibold', 'text-blue-300', 'text-sm');
            qEl.textContent = entry.question || 'প্রশ্ন পাওয়া যায়নি';
            contentDiv.appendChild(qEl);

            const answersEl = document.createElement('div');
            answersEl.classList.add('text-xs', 'space-y-1', 'ml-2');

            let correctLetter = '';
            const optionLetters = ['A', 'B', 'C', 'D'];
            if (entry.options && entry.correctAnswer) {
                 entry.options.forEach((opt, i) => {
                     if (opt === entry.correctAnswer) correctLetter = optionLetters[i];
                 });
            }

            const userFeedback = document.createElement('p');
            if (entry.userAnswer === "Timeout") {
                 userFeedback.innerHTML = '<span class="text-orange-400 font-semibold">⏳ সময় শেষ/স্কিপ করা হয়েছে।</span>';
            } else if (entry.userAnswer !== undefined && entry.userAnswer !== null && entry.userAnswer !== '') {
                userFeedback.innerHTML = `<span class="${entry.isCorrect ? 'text-green-400' : 'text-red-400'} font-semibold">${entry.isCorrect ? '✅ আপনার উত্তর:' : '❌ আপনার উত্তর:'}</span> ${entry.userAnswer}`;
            } else {
                 userFeedback.innerHTML = '<span class="text-gray-500 font-semibold">কোন উত্তর দেওয়া হয়নি।</span>';
            }
            answersEl.appendChild(userFeedback);

             if (entry.userAnswer === "Timeout" || !entry.isCorrect) {
                const correctFeedback = document.createElement('p');
                if (entry.correctAnswer) {
                   correctFeedback.innerHTML = `<span class="text-green-400 font-semibold">সঠিক উত্তর:</span> ${correctLetter ? `${correctLetter}) ` : ''}${entry.correctAnswer}`;
                } else {
                    correctFeedback.innerHTML = '<span class="text-gray-500 font-semibold">সঠিক উত্তর:</span> N/A (ডাটা অনুপস্থিত)';
                }
                answersEl.appendChild(correctFeedback);
            }

            contentDiv.appendChild(answersEl);
            entryElement.appendChild(contentDiv);

             const deleteButton = document.createElement('button');
             deleteButton.classList.add('delete-history-btn');
             deleteButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                    <path d="M216 48h-40V36a28 28 0 0 0-28-28H108A28 28 0 0 0 80 36v12H40a12 12 0 0 0 0 24h4v136a20 20 0 0 0 20 20h88a20 20 0 0 0 20-20V72h4a12 12 0 0 0 0-24ZM108 36a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v12H108ZM184 208a4 4 0 0 1-4 4H76a4 4 0 0 1-4-4V72h112Z"/>
                </svg>
             `;
             deleteButton.title = 'এই হিস্টোরি এন্ট্রি মুছুন';
             deleteButton.setAttribute('aria-label', 'এই হিস্টোরি এন্ট্রি মুছুন');
             deleteButton.dataset.indexToDelete = originalIndex;

             deleteButton.addEventListener('click', (event) => {
                 const btn = event.target.closest('.delete-history-btn');
                 if (btn) {
                      const index = parseInt(btn.dataset.indexToDelete);
                      if (!isNaN(index)) {
                          deleteHistoryEntry(index);
                      }
                 }
             });
             entryElement.appendChild(deleteButton);

            elements.historyList.appendChild(entryElement);
        });
    }

    elements.historyModal.classList.remove('hidden');
    elements.historyModal.querySelector('.history-modal-panel').classList.add('translate-x-0');
    elements.historyModal.querySelector('.history-modal-panel').classList.remove('translate-x-full');
}

function hideHistory() {
     elements.historyModal.querySelector('.history-modal-panel').classList.remove('translate-x-0');
     elements.historyModal.querySelector('.history-modal-panel').classList.add('translate-x-full');

     elements.historyModal.querySelector('.history-modal-panel').addEventListener('transitionend', function handler() {
         elements.historyModal.classList.add('hidden');
         elements.historyModal.querySelector('.history-modal-panel').removeEventListener('transitionend', handler);
     }, { once: true });
}


function updateScores() {
  elements.correctCount.textContent = correctCount;
  elements.wrongCount.textContent = wrongCount;
  saveToStorage();
}

elements.nextBtn.addEventListener('click', () => {
    if (!isAnswered) {
        handleTimeout();
        fetchQuestion();
    } else {
        fetchQuestion();
    }
});


elements.historyBtn.addEventListener('click', showHistory);
elements.closeHistory.addEventListener('click', hideHistory);

elements.submitPrompt.addEventListener('click', () => {
  const newPrompt = elements.customPrompt.value.trim();
  currentPrompt = newPrompt;
  elements.customPrompt.value = '';
  saveToStorage();
  if (!isAnswered && currentQuestionData && currentQuestionData.question) {
       handleTimeout();
       fetchQuestion();
   } else {
        fetchQuestion();
   }
});


elements.customPrompt.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        elements.submitPrompt.click();
    }
});

elements.historyModal.addEventListener('click', (event) => {
    if (event.target === elements.historyModal) {
        hideHistory();
    }
});

loadFromStorage();
fetchQuestion();
