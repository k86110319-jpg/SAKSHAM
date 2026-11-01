let currentFontSize = 16;

function changeFontSize(amount) {
    currentFontSize += amount;

    if (currentFontSize < 12) currentFontSize = 12;
    if (currentFontSize > 26) currentFontSize = 26;

    document.documentElement.style.fontSize = currentFontSize + "px";
}

function toggleContrast() {
    document.body.classList.toggle("high-contrast");
}

function readPage() {
    if (!("speechSynthesis" in window)) {
        alert("Read aloud is not supported in this browser.");
        return;
    }

    window.speechSynthesis.cancel();

    let text = document.querySelector("main")?.innerText
        || document.body.innerText;

    let speech = new SpeechSynthesisUtterance(text);
    speech.lang = "en-IN";
    speech.rate = 0.9;

    window.speechSynthesis.speak(speech);
}

function stopReading() {
    window.speechSynthesis.cancel();
}